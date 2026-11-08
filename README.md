What's Cookin'

What's Cookin' is a community recipe-sharing website where users can 
discover, share, and organize cooking and baking recipes. Users will be able
to search for recipes by keywords, categories, or ingredients they have 
already have available. Registered users will be able to submit and manage 
their own recipes. The website uses HTML, CSS, JavaScript, PHP, and MySQL.

Installation

1. Install XAMPP, which includes Apache, PHP, and MariaDB/MySQL-compatible
 database servicees.
2. Start Apache and MySql from the XAMPP Control Panel.
3. Place the project's PHP files inside C:\xampp\htdocs.
4. Open phpMyAdmin at http://localhost/phpmyadmin/.
5. Create a local database named whatscookin_test.
6. Create the required database tables using the SQL files provided with the project.
7. Configure the local database connection settings.
8. Open the project through localhost in web browser. 

Testing

The technology stack was validated using manual tests. 

PHP Validation: A PHP test script was executed through Apache using XAMPP. The browser
successfully displayed a PHP test message and confirmed PHP version 8.2.12.

MySQL Validation: A test database names whatscookin_test was created. A sample table was
used to insert and retrieve a Chocolate Chip Cookies recipe.

PHP/MySQL Integration: A PHP script connected to MySQL using PDO and successfully
retrieved the sample recipe.

These tests demonstrate that PHP and MySQL can communicate and support the planned
backend functionality.

Usage

The What's Cookin' website is intended to be accessed through a web browser. Users will
be able to browse recipes, search by ingredients and categories, and create accounts to
submit their own recipes.
A test version of the website is available at:
https://whatscookin.gt.tc/test.php?i=1
For local development, start Apache and MySQL in XAMPP and open the project's PHP pages
through http://localhost/.