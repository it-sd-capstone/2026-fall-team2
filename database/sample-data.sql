-- Run once after schema.sql on a fresh local development database.
USE whatscookin;
START TRANSACTION;

-- The sample author has no usable password and exists to own the recipes.
INSERT INTO users (user_id, username, email, password_hash)
VALUES (1, 'sample_cook', 'sample-cook@example.invalid', 'disabled');

INSERT INTO categories (category_id, category_name) VALUES
  (1, 'Breakfast'),
  (2, 'Dinner'),
  (3, 'Snack');

INSERT INTO ingredients (ingredient_id, name) VALUES
  (1, 'Rolled oats'),
  (2, 'Banana'),
  (3, 'Milk'),
  (4, 'Broccoli'),
  (5, 'Carrots'),
  (6, 'Olive oil'),
  (7, 'Salt');

INSERT INTO recipes
  (recipe_id, user_id, title, prep_time, bake_time, bake_temp, notes)
VALUES
  (1, 1, 'Banana Overnight Oats', 5, 0, NULL, 'Chill overnight before serving.'),
  (2, 1, 'Roasted Broccoli and Carrots', 10, 25, 425, NULL),
  (3, 1, 'Baked Banana Oat Cups', 15, 20, 350, 'Makes six oat cups.');

INSERT INTO recipe_categories (recipe_id, category_id) VALUES
  (1, 1),
  (2, 2),
  (3, 1),
  (3, 3);

INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
  (1, 1, '1/2 cup'),
  (1, 2, '1'),
  (1, 3, '1/2 cup'),
  (2, 4, '2 cups florets'),
  (2, 5, '2 cups sliced'),
  (2, 6, '2 tablespoons'),
  (2, 7, '1/2 teaspoon'),
  (3, 1, '1 1/2 cups'),
  (3, 2, '2'),
  (3, 3, '1/2 cup');

INSERT INTO steps (recipe_id, step_number, instruction) VALUES
  (1, 1, 'Mash the banana and stir in the oats and milk.'),
  (1, 2, 'Cover and refrigerate overnight.'),
  (2, 1, 'Preheat the oven to 425°F. Toss the vegetables with olive oil and salt.'),
  (2, 2, 'Spread on a baking sheet and roast for 25 minutes, stirring halfway through.'),
  (3, 1, 'Preheat the oven to 350°F. Mash the bananas and mix in the oats and milk.'),
  (3, 2, 'Divide among six lined muffin cups and bake for 20 minutes.');

COMMIT;
