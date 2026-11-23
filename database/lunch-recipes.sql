START TRANSACTION;

SET @author_id = (
  SELECT user_id
  FROM users
  WHERE username = 'sample_cook'
);

INSERT INTO categories (category_name)
VALUES ('Lunch')
ON DUPLICATE KEY UPDATE category_name = VALUES(category_name);

SET @lunch_id = (
  SELECT category_id
  FROM categories
  WHERE category_name = 'Lunch'
);

INSERT INTO ingredients (name)
VALUES 
  ('Chickpeas'),
  ('Cucumber'),
  ('Cooked rice'),
  ('Broccoli'),
  ('Carrots'),
  ('Olive oil'),
  ('Salt')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Lunch recipe #1
INSERT INTO recipes
  (user_id, title, prep_time, bake_time, bake_temp, notes)
SELECT 
  @author_id,
  'Chickpea Cucumber Salad',
  10,
  0,
  NULL,
  'Serves two. Drain and rinse canned chickpeas before using.'
WHERE NOT EXISTS (
  SELECT 1
  FROM recipes
  WHERE user_id = @author_id
  AND title = 'Chickpea Cucumber Salad'
);

SET @salad_id = (
  SELECT recipe_id
  FROM recipes
  WHERE user_id = @author_id
  AND title = 'Chickpea Cucumber Salad'
  ORDER BY recipe_id
  LIMIT 1
);

-- Lunch recipe #2
INSERT INTO recipes 
  (user_id, title, prep_time, bake_time, bake_temp, notes)
SELECT 
  @author_id,
  'Vegetable Rice Bowl',
  10,
  20,
  425,
  'Serves two. Use freshly cooked rice for serving.'
WHERE NOT EXISTS (
  SELECT 1
  FROM recipes
  WHERE user_id = @author_id
  AND title = 'Vegetable Rice Bowl'
);

SET @bowl_id = (
  SELECT recipe_id
  FROM recipes
  WHERE user_id = @author_id
  AND title = 'Vegetable Rice Bowl'
  ORDER BY recipe_id
  LIMIT 1
);

INSERT INTO recipe_categories (recipe_id, category_id)
VALUES (@salad_id, @lunch_id), (@bowl_id, @lunch_id) ON DUPLICATE KEY UPDATE category_id = VALUES(category_id);

INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity)
VALUES 
  (@salad_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Chickpeas'), '1 can (15 ounces)'),
  (@salad_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Cucumber'), '1 diced'),
  (@salad_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Olive oil'), '1 tablespoon'),
  (@salad_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Salt'), '1/4 teaspoon'),
  (@bowl_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Cooked rice'), '2 cups'),
  (@bowl_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Broccoli'), '2 cups of florets'),
  (@bowl_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Carrots'), '1 cup sliced'),
  (@bowl_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Olive oil'), '1 tablespoon'),
  (@bowl_id,
    (SELECT ingredient_id FROM ingredients WHERE name = 'Salt'), '1/4 teaspoon')
  ON DUPLICATE KEY UPDATE quantity = VALUES(quantity);

  INSERT INTO steps (recipe_id, step_number, instruction)
  VALUES 
    (@salad_id, 1,
      'Drain and rinse the chickpeas. Dice the cucumber.'),
    (@salad_id, 2,
      'Combine the chickpeas and cucumber in a bowl.'),
    (@salad_id, 3,
      'Add the olive oil and salt, toss gently, and serve.'),
    (@bowl_id, 1,
      'Preheat the oven to 425 degrees Fahrenheit.'),
    (@bowl_id, 2,
      'Toss the broccoli and carrots with the olive oil and salt. Spread on baking sheet.'),
    (@bowl_id, 3,
      'Roast for 20 minutes, stirring halfway through.'),
    (@bowl_id, 4,
      'Divide the cooked rice between two bowls and top with the roasted vegetables.')
  ON DUPLICATE KEY UPDATE instruction = VALUES(instruction);

  COMMIT;
