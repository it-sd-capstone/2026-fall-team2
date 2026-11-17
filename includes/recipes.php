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