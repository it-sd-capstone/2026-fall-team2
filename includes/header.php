<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>What's Cookin'</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php" id="home-link">Home</a></li>
            </ul>
        </nav>
        <h1>What's Cookin'</h1>
        <div class="header-right">
            <form class="search-form" action="search.php" method="get">
                <input type="search" name="query" placeholder="Search recipes">
                <button type="submit">Search</button>
            </form>
            <a href="login.php">Log In</a>
            <a href="register.php">Register</a>
        </div>
    </header>
    <button id="sidebar-toggle">☰</button>
    <nav id="sidebar" class="sidebar">
        <h2>Categories</h2>
        <ul>
            <li><a href="index.php?category=breakfast">Breakfast</a></li>
            <li><a href="index.php?category=lunch">Lunch</a></li>
            <li><a href="index.php?category=dinner">Dinner</a></li>
        </ul>
        <ul>
            <li><a href="index.php">All Recipes</a></li>
            <li><a href="ingredient-search.php">Ingredient Search</a></li>
            <li><a href="add-recipe.php">Add Recipe</a></li>
            <li><a href="favorites.php">My Favorites</a></li>
            <li><a href="my-recipes.php">My Recipes</a></li>
        </ul>
    </nav>
    <main>