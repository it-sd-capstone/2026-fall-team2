<?php
require 'includes/db.php';
require 'includes/recipes.php';

$search = trim($_GET['query'] ?? '');
$recipes = [];

if ($search !== '') {
    $recipes = searchRecipes($pdo, $search);
}

include 'includes/header.php'; ?>
    <h2>Search results for "<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"</h2>

<?php if (empty($recipes)): ?>
    <p>No recipes found. Try a different word or phrase.</p>
<?php else: ?>
    <div class="recipe-grid">
        <?php foreach ($recipes as $recipe): ?>
            <?php include 'includes/recipe-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>