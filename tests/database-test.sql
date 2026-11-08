-- Whats Cookin Database Validation

CREATE DATABASE IF NOT EXISTS whatscookin_test;

USE whatscookin_test;

CREATE TABLE IF NOT EXISTS recipes_test (
  recipe_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL
);

INSERT INTO recipes_test (title)
VALUES ('Chocolate Chip Cookies');

SELECT * FROM recipes_test;