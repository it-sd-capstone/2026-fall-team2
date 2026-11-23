<?php 
require 'includes/db.php';
require 'includes/recipes.php';

$category = trim($_GET['category'] ?? '');

if ($category !== '') {
    $recipes = getRecipesByCategory($pdo, $category);
} else {
    $recipes = getRecipeCards($pdo);
}

include 'includes/header.php'; ?>
    <?php if ($category !== ''): ?>
        <h2><?php echo htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?> Recipes</h2>
    <?php else: ?>
        <section class="hero">
            <h2>Find your next favorite meal</h2>
            <p>Discover delicious recipes for every meal!</p>
        </section>
    <?php endif; ?>
    
    <?php if (empty($recipes)): ?>
        <p>No recipes in this category yet.</p>
    <?php endif; ?>

        <div class="recipe-grid">
            <?php foreach ($recipes as $recipe): ?>
                <?php include 'includes/recipe-card.php'; ?>
            <?php endforeach; ?>
        </div>

<?php include 'includes/footer.php'; ?>