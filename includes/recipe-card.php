<div class="recipe-card">
    <h3><a href="recipe.php?id=<?php echo (int)$recipe['recipe_id']; ?>"><?php echo htmlspecialchars($recipe['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
    <p>Prep Time: <?php echo htmlspecialchars($recipe['prep_time'], ENT_QUOTES, 'UTF-8'); ?> minutes</p>
<?php if ($recipe['bake_time']): ?>
    <p>Bake Time: <?php echo htmlspecialchars($recipe['bake_time'], ENT_QUOTES, 'UTF-8'); ?> minutes</p>
<?php endif; ?>
</div>