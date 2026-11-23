<?php 
require 'includes/db.php';
require 'includes/recipes.php';

$recipes = getRecipeCards($pdo);

include 'includes/header.php'; ?>
        <section class="hero">
                <h2>Find your next favorite meal</h2>
                <p>Discover delicious recipes for every meal!</p>
        </section>

        <div class="recipe-grid">
            <?php foreach ($recipes as $recipe): ?>
                <?php include 'includes/recipe-card.php'; ?>
            <?php endforeach; ?>
        </div>

<?php include 'includes/footer.php'; ?>