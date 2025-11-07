
-- Sample categories data
INSERT INTO categories (CategoryId, user_id, categoryName, type, created_at) VALUES
(1, 1, 'Salary', 'income', NOW()),
(2, 1, 'Bonus', 'income', NOW()),
(3, 1, 'Groceries', 'expense', NOW()),
(4, 1, 'Rent', 'expense', NOW());
