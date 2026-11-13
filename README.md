# What's Cookin'

What's Cookin' is a community recipe-sharing website where users can 
discover, share, and organize cooking and baking recipes. Users will be able
to search for recipes by keywords, categories, or ingredients they have 
already available. Registered users will be able to submit and manage 
their own recipes. The website uses HTML, CSS, JavaScript, PHP, and MySQL.

## Installation

1. Install XAMPP, which includes Apache, PHP, and MariaDB/MySQL-compatible
database services.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Clone the repository from GitHub into C:\xampp\htdocs.
4. Open phpMyAdmin at http://localhost/phpmyadmin/.
5. Run database/database-test.sql in phpMyAdmin to create the database and tables.
6. Configuration is not necessary for XAMPP. For the live site, copy tests/config.example.php to tests/config.php and fill in the database values.
7. Open http://localhost/2026-fall-team2/ in a web browser.

## Testing

The technology stack was validated using manual tests. 

PHP Validation: A PHP test script was executed through Apache using XAMPP.
The browser successfully displayed a PHP test message and confirmed PHP
version 8.2.12.

MySQL Validation: A test database named whatscookin_test was created. A
sample table was used to insert and retrieve a Chocolate Chip Cookies recipe.

PHP/MySQL Integration: A PHP script connected to MySQL using PDO and
successfully retrieved the sample recipe.

These tests demonstrate that PHP and MySQL can communicate and support the
planned backend functionality.

### Running Validation Tests

1. Start Apache and MySQL in the XAMPP Control Panel.
2. Open phpMyAdmin at http://localhost/phpmyadmin/.
3. Run database/database-test.sql in phpMyAdmin to create the whatscookin_test
database and insert a sample recipe.
4. Open http://localhost/2026-fall-team2/tests/php-test.php to verify PHP
execution.
5. Open http://localhost/2026-fall-team2/tests/db-test.php to verify the
PHP/MySQL connection.

### Expected Results:

PHP displays a successful execution message and its version number.

MySQL creates a database and stores the sample recipe.

PHP connects to MySQL and retrieves the Chocolate Chip Cookies recipe.

The live site reads its settings from tests/config.php.

These tests confirm that the backend technologies can work together to
support the What's Cookin' application.

## Usage

The What's Cookin' website is intended to be accessed through a web browser.
Users will be able to browse recipes, search by ingredients and categories,
and create accounts to submit their own recipes.
A test version of the website is available at:

https://whatscookin.gt.tc/tests/db-test.php

For local development, start Apache and MySQL in XAMPP and open the project's 
PHP pages through http://localhost/2026-fall-team2.