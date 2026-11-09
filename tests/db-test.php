<?php
// PHP/MySQL Integration Test

$configFile = __DIR__ . "/config.php";
if (file_exists($configFile)) {
  require $configFile;}

$host = $DB_HOST ?? getenv("DB_HOST") ?: "127.0.0.1";
$dbname = $DB_NAME ?? getenv("DB_NAME") ?: "whatscookin_test";
$username = $DB_USER ?? getenv("DB_USER") ?: "root";
$password = $DB_PASSWORD ?? getenv("DB_PASSWORD");

if ($password === false){
  $password = "";
}

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

  $stmt = $pdo->query(
    "SELECT recipe_id, title FROM recipes_test"
  );

  echo "<h1>What's Cookin' Database Test</h1>";
  echo "<p>PHP and MySQL connection successful!</p>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p>Recipe ID: " .
            htmlspecialchars((string)$row["recipe_id"]) .
            "</p>";

        echo "<p>Recipe: " .
            htmlspecialchars($row["title"], ENT_QUOTES, "UTF-8") .
            "</p>";
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "<p>Database test failed. Check your connection settings.</p>";
}
?>
