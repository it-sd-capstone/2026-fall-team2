<?php

function getOrderBy(string $sort, string $default = 'title'): string
{
    // Only these sorts are allowed. Anything else falls back to title.
    $sortOptions = [
        'title'  => 'title ASC',
        'newest' => 'created_at DESC, recipe_id DESC',
        'steps'  => 'step_count ASC, title ASC'
    ];
    return $sortOptions[$sort] ?? $sortOptions[$default];
}

function getRecipeCards(PDO $pdo, string $sort = 'newest'): array
{
    $orderBy = getOrderBy($sort, 'newest');

    $stmt = $pdo->query(
        "SELECT recipe_id, title, prep_time, bake_time,
            (SELECT COUNT(*) FROM steps WHERE steps.recipe_id = recipes.recipe_id) AS step_count
        FROM recipes
        ORDER BY $orderBy"
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchRecipes(PDO $pdo, string $search, string $sort = 'title'): array
{
    $orderBy = getOrderBy($sort, 'title');

    // Split the search into words so "banana oats" can match "Banana Overnight Oats"
    $searchWords = preg_split('/\s+/', trim($search));
    // preg_split cuts on a pattern instead of exact text.
    // '/\s+/' means one or more spaces, tabs or new lines in a row,
    // so "banana   oats" becomes ["banana", "oats"] with no empty words.

    $conditions = [];
    $params = [];

    foreach ($searchWords as $n => $word) {
        // Each word gets its own numbered placeholders, like :title0 and :title1
        $conditions[] = "(title LIKE :title$n
            OR notes LIKE :notes$n
            OR EXISTS (
                SELECT 1
                FROM steps
                WHERE steps.recipe_id = recipes.recipe_id
                    AND steps.instruction LIKE :step$n
            )
            OR EXISTS (
                SELECT 1
                FROM recipe_ingredients ri
                JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
                WHERE ri.recipe_id = recipes.recipe_id
                    AND i.name LIKE :ingredient$n
            ))";

        $like = '%' . $word . '%';
        $params["title$n"] = $like;
        $params["notes$n"] = $like;
        $params["step$n"] = $like;
        $params["ingredient$n"] = $like;
    }

    // Every word has to match somewhere in the recipe
    $where = implode(' AND ', $conditions);

    $stmt = $pdo->prepare(
        "SELECT recipe_id, title, prep_time, bake_time,
            (SELECT COUNT(*) FROM steps WHERE steps.recipe_id = recipes.recipe_id) AS step_count
        FROM recipes
        WHERE $where
        ORDER BY $orderBy"
    );
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// fixed the function to use EXISTS due to recipe_id being in multiple tables and not being unique to one table
function getRecipesByCategory(PDO $pdo, string $category, string $sort = 'newest'): array
{
    $orderBy = getOrderBy($sort, 'newest');

    $stmt = $pdo->prepare(
        "SELECT recipe_id, title, prep_time, bake_time,
            (SELECT COUNT(*) FROM steps WHERE steps.recipe_id = recipes.recipe_id) AS step_count
        FROM recipes
        WHERE EXISTS (
            SELECT 1
            FROM recipe_categories rc
            JOIN categories c ON c.category_id = rc.category_id
            WHERE rc.recipe_id = recipes.recipe_id
                AND c.category_name = :category
        )
        ORDER BY $orderBy"
    );

    $stmt->execute(['category' => $category]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Builds a list of every real word on the site (recipe titles and ingredient names)
// so we have something to compare a misspelled search against
function getSearchWords(PDO $pdo): array
{
    // UNION stacks the titles and ingredient names into one list
    $stmt = $pdo->query(
        "SELECT title AS text FROM recipes
        UNION
        SELECT name FROM ingredients"
    );

    $words = [];

    // FETCH_COLUMN gives us a simple list of text instead of rows
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $text) {
        // Explode -> Splits "Banana Overnight Oats" into "banana", "overnight", "oats"
        foreach (explode(' ', strtolower($text)) as $word) {
            $words[] = $word;
        }
    }

    // Remove duplicates like "banana" showing up in two titles
    return array_unique($words);
}

// Finds the real word closest to what the user typed
// Returns null if nothing is close enough to be a typo
function suggestWord(string $search, array $words): ?string
{
    $search = strtolower($search);
    $closestWord = null;

    // Only accept words that are 2 or fewer letter changes away
    $fewestChanges = 3;

    foreach ($words as $word) {
        // levenshtein counts how many letter changes turn one word into the other
        //"bananna" to "banana" = 1
        $letterChanges = levenshtein($search, $word);

        // Keep this word if it's closer than the best one so far
        if ($letterChanges < $fewestChanges) {
            $fewestChanges = $letterChanges;
            $closestWord = $word;
        }
    }

    return $closestWord;
}

// Fixes each misspelled word in a search, like "bananna oats" to "banana oats"
// Returns the fixed search, or null if nothing needed fixing
function suggestSearch(string $search, array $words): ?string
{
    $searchWords = preg_split('/\s+/', strtolower(trim($search)));
    $changed = false;

    foreach ($searchWords as $n => $searchWord) {
        // Skip words that are already real words on the site
        if (in_array($searchWord, $words)) {
            continue;
        }

        $closestWord = suggestWord($searchWord, $words);
        if ($closestWord !== null) {
            $searchWords[$n] = $closestWord;
            $changed = true;
        }
    }

    // Only return a suggestion if at least one word changed
    // implode glues the fixed words back into one search, like "banana oats"
    return $changed ? implode(' ', $searchWords) : null;
}

// Detail queries are separate so multiple ingredients and steps stay distinct.
function getRecipeById(PDO $pdo, int $recipeId): ?array
{
    $statement = $pdo->prepare(
        'SELECT recipe_id, title, prep_time, bake_time, bake_temp, notes
         FROM recipes
         WHERE recipe_id = :recipe_id'
    );
    $statement->execute(['recipe_id' => $recipeId]);

    $recipe = $statement->fetch(PDO::FETCH_ASSOC);
    return $recipe === false ? null : $recipe;
}

function getRecipeIngredients(PDO $pdo, int $recipeId): array
{
    $statement = $pdo->prepare(
        'SELECT i.name, ri.quantity
         FROM recipe_ingredients ri
         JOIN ingredients i ON i.ingredient_id = ri.ingredient_id
         WHERE ri.recipe_id = :recipe_id
         ORDER BY i.name, i.ingredient_id'
    );
    $statement->execute(['recipe_id' => $recipeId]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getRecipeSteps(PDO $pdo, int $recipeId): array
{
    $statement = $pdo->prepare(
        'SELECT step_number, instruction
         FROM steps
         WHERE recipe_id = :recipe_id
         ORDER BY step_number, step_id'
    );
    $statement->execute(['recipe_id' => $recipeId]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
