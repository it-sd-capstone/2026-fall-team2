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

    // Prepare the SQL statement with placeholders for the search term
    $stmt = $pdo->prepare(
        "SELECT recipe_id, title, prep_time, bake_time,
            (SELECT COUNT(*) FROM steps WHERE steps.recipe_id = recipes.recipe_id) AS step_count
        FROM recipes
        WHERE title LIKE :title
            OR notes LIKE :notes
            OR EXISTS (
                SELECT 1
                FROM steps
                WHERE steps.recipe_id = recipes.recipe_id
                    AND steps.instruction LIKE :step
            )
            OR EXISTS (
                SELECT 1
                FROM recipe_ingredients ri
                JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
                WHERE ri.recipe_id = recipes.recipe_id
                    AND i.name LIKE :ingredient
            )
        ORDER BY $orderBy"
    );

    // Matches the search term anywhere in the title, notes, steps or ingredients
    $like = '%' . $search . '%';

    // Execute the statement with the search term bound to each placeholder
    $stmt->execute([
        'title'      => $like,
        'notes'      => $like,
        'step'       => $like,
        'ingredient' => $like
    ]);

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
