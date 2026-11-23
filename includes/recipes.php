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
        WHERE title LIKE :title OR notes LIKE :notes
        ORDER BY title ASC"
    );

    $like = '%' . $search . '%'; // this line lets you search for recipes that contain the search term anywhere in the title or notes 
    $stmt->execute(['title' => $like, 'notes' => $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}