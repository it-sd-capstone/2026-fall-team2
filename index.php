<?php
require 'includes/db.php';
require 'includes/recipes.php';

$category = trim($_GET['category'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

if ($category !== '') {
    $recipes = getRecipesByCategory($pdo, $category, $sort);
} else {
    $recipes = getRecipeCards($pdo, $sort);
}

include 'includes/header.php'; ?>

<?php if ($category === ''): ?>
    <section class="hero">
        <h2>Find your next favorite meal</h2>
        <p>Discover delicious recipes for every meal!</p>
    </section>
<?php endif; ?>

<div class="results-header">
    <h2>
        <?php if ($category !== ''): ?>
            <?php echo htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?> Recipes
        <?php else: ?>
            All Recipes
        <?php endif; ?>
    </h2>
    <?php include 'includes/sort-form.php'; ?>
</div>

<?php if (empty($recipes)): ?>
    <p>No recipes in this category yet.</p>
<?php else: ?>
    <div class="recipe-grid">
        <?php foreach ($recipes as $recipe): ?>
            <?php include 'includes/recipe-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>