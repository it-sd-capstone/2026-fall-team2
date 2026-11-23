<?php
require 'includes/db.php';
require 'includes/recipes.php';

$search = trim($_GET['query'] ?? '');
$sort = $_GET['sort'] ?? 'title';
$recipes = [];
$suggestion = null;

if ($search !== '') {
    $recipes = searchRecipes($pdo, $search, $sort);

    // No matches returned, so try the closest real word in case of a typo
    if (empty($recipes)) {
        $suggestion = suggestWord($search, getSearchWords($pdo));
        if ($suggestion !== null) {
            $recipes = searchRecipes($pdo, $suggestion, $sort);
        }
    }
}

include 'includes/header.php'; ?>
    <h2>Search results for "<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"</h2>

    <form class="sort-form" action="search.php" method="get">
        <input type="hidden" name="query" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="sort">Sort by</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="title" <?php if ($sort === 'title') echo 'selected'; ?>>Title (A-Z)</option>
            <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>>Newest</option>
            <option value="steps" <?php if ($sort === 'steps') echo 'selected'; ?>>Fewest steps</option>
        </select>
    </form>

    <?php if ($suggestion !== null && !empty($recipes)): ?>
        <p class="suggestion">No exact matches. Showing results for "<strong><?php echo htmlspecialchars($suggestion, ENT_QUOTES, 'UTF-8'); ?></strong>" instead.</p>
    <?php endif; ?>

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