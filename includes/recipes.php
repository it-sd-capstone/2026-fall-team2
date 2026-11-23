<?php
function getRecipeCards(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT recipe_id, title, prep_time, bake_time
        FROM recipes
        ORDER BY created_at DESC, recipe_id DESC"
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchRecipes(PDO $pdo, string $search, string $sort = 'title'): array
{
    // Only these sorts are allowed. Anything else falls back to title.
    $sortOptions = [
        'title'  => 'title ASC',
        'newest' => 'created_at DESC, recipe_id DESC',
        'steps'  => 'step_count ASC, title ASC'
    ];
    $orderBy = $sortOptions[$sort] ?? $sortOptions['title'];

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

function getRecipesByCategory(PDO $pdo, string $category): array
{
    $stmt = $pdo->prepare(
        "SELECT r.recipe_id, r.title, r.prep_time, r.bake_time
        FROM recipes r
        JOIN recipe_categories rc ON rc.recipe_id = r.recipe_id
        JOIN categories c ON c.category_id = rc.category_id
        WHERE c.category_name = :category
        ORDER BY r.created_at DESC, r.recipe_id DESC"
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