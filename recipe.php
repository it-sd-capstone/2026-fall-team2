<?php
require_once __DIR__ . '/includes/recipes.php';

$recipe = null;
$ingredients = [];
$steps = [];
$errorMessage = '';
$id = $_GET['id'] ?? null;

// Reject arrays, missing IDs, zero, negative values, and malformed numbers.
$recipeId = is_string($id)
    ? filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;

if ($recipeId === false) {
    http_response_code(400);
    $errorMessage = 'Please choose a recipe using a valid recipe ID.';
} else {
    require __DIR__ . '/includes/db.php';

    try {
        $recipe = getRecipeById($pdo, $recipeId);

        if ($recipe === null) {
            http_response_code(404);
            $errorMessage = 'The recipe you requested could not be found.';
        } else {
            $ingredients = getRecipeIngredients($pdo, $recipeId);
            $steps = getRecipeSteps($pdo, $recipeId);
        }
    } catch (PDOException $error) {
        error_log($error->getMessage());
        http_response_code(500);
        $errorMessage = 'Sorry, this recipe could not be loaded. Please try again later.';
    }
}

include __DIR__ . '/includes/header.php';
?>
    <div class="recipe-detail">
        <p><a href="index.php">Back to all recipes</a></p>

        <?php if ($errorMessage !== ''): ?>
            <h2>Recipe unavailable</h2>
            <p><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
            <article>
                <h2><?php echo htmlspecialchars($recipe['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <dl class="recipe-times">
                    <div>
                        <dt>Prep time</dt>
                        <dd><?php echo (int)$recipe['prep_time']; ?> minutes</dd>
                    </div>
                    <div>
                        <dt>Bake time</dt>
                        <dd><?php echo (int)$recipe['bake_time']; ?> minutes</dd>
                    </div>
                    <?php if ($recipe['bake_temp'] !== null): ?>
                        <div>
                            <dt>Oven temperature</dt>
                            <dd><?php echo (int)$recipe['bake_temp']; ?> &deg;F</dd>
                        </div>
                    <?php endif; ?>
                </dl>

                <section>
                    <h3>Ingredients</h3>
                    <?php if ($ingredients === []): ?>
                        <p>No ingredients have been added yet.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($ingredients as $ingredient): ?>
                                <li><?php echo htmlspecialchars($ingredient['quantity'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php echo htmlspecialchars($ingredient['name'], ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <section>
                    <h3>Steps</h3>
                    <?php if ($steps === []): ?>
                        <p>No steps have been added yet.</p>
                    <?php else: ?>
                        <ol>
                            <?php foreach ($steps as $step): ?>
                                <li value="<?php echo (int)$step['step_number']; ?>"><?php echo nl2br(htmlspecialchars($step['instruction'], ENT_QUOTES, 'UTF-8')); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </section>

                <?php if ($recipe['notes'] !== null && trim($recipe['notes']) !== ''): ?>
                    <section>
                        <h3>Notes</h3>
                        <p><?php echo nl2br(htmlspecialchars($recipe['notes'], ENT_QUOTES, 'UTF-8')); ?></p>
                    </section>
                <?php endif; ?>
            </article>
        <?php endif; ?>
    </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
