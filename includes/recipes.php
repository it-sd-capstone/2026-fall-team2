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

function searchRecipes(PDO $pdo, string $search): array
{
    $stmt = $pdo->prepare( // Prepare the SQL statement with placeholders for the search term
        "SELECT recipe_id, title, prep_time, bake_time
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
        ORDER BY title ASC"
    );

    $like = '%' . $search . '%'; // this line lets you search for recipes that contain the search term anywhere in the title or notes 
    $stmt->execute(['title' => $like,
                    'notes' => $like,
                    'step' => $like,
                    'ingredient' => $like
                    ]); // Execute the statement with the search term bound to the placeholders
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