<?php
// Shared database connection for whatscookin application

$configFile = __DIR__ . "/../tests/config.php";
if (file_exists($configFile)) {
  require $configFile;}

$host = $DB_HOST ?? getenv("DB_HOST") ?: "127.0.0.1";
$port = $DB_PORT ?? getenv("DB_PORT") ?: "3306";
$dbname = $DB_NAME ?? getenv("DB_NAME") ?: "whatscookin";
$username = $DB_USER ?? getenv("DB_USER") ?: "root";
$password = $DB_PASSWORD ?? getenv("DB_PASSWORD");

if ($password === false){
  $password = "";
}

try {
  $pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Sorry, the database is unavailable. Please try again later.");
}
