<?php 
require 'includes/db.php';
require 'includes/recipes.php';

$recipes = getRecipeCards($pdo);

include 'includes/header.php'; ?>

        <h2>Welcome to What's Cookin'</h2>
        <p>Discover delicious recipes for every meal!</p>

        <div class="recipe-grid">
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <h3><?php echo htmlspecialchars($recipe['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p>Prep Time: <?php echo htmlspecialchars($recipe['prep_time'], ENT_QUOTES, 'UTF-8'); ?> minutes</p>
                <?php if ($recipe['bake_time']): ?>
                    <p>Bake Time: <?php echo htmlspecialchars($recipe['bake_time'], ENT_QUOTES, 'UTF-8'); ?> minutes</p>
                <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

<?php include 'includes/footer.php'; ?>