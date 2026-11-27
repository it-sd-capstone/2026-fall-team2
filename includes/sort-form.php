<?php
// Shared sort dropdown for every page that lists recipes.
// Before including this file, the page must set $sort ('newest', 'title' or 'steps').
// It also uses $search or $category if the page has them.
?>

<?php // No action, so the form sends the choice back to whatever page it's on ?>
<form class="sort-form" method="get">

    <?php
    // A GET form drops the old URL values when it submits.
    // These hidden inputs carry the search word or category along
    // so sorting doesn't lose them. They only show up when the page has one.
    ?>
    <?php if (!empty($search)): ?>
        <input type="hidden" name="query" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <?php if (!empty($category)): ?>
        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <?php // for="sort" ties the label to the dropdown so screen readers announce it ?>
    <label for="sort">Sort by</label>

    <?php // onchange submits the form as soon as a new option is picked ?>
    <select name="sort" id="sort" onchange="this.form.submit()">
        <?php
        // Values must match the keys in getOrderBy() in includes/recipes.php.
        // "selected" keeps the current sort showing after the page reloads.
        ?>
        <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>>Newest</option>
        <option value="title" <?php if ($sort === 'title') echo 'selected'; ?>>Title (A-Z)</option>
        <option value="steps" <?php if ($sort === 'steps') echo 'selected'; ?>>Fewest steps</option>
    </select>
</form>