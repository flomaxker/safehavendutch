-- Add euro_balance column to users table
ALTER TABLE users ADD COLUMN euro_balance INT DEFAULT 0;

-- Migrate existing credit_balance to euro_balance (assuming 1 credit = 1 euro for simplicity)
UPDATE users SET euro_balance = credit_balance;

-- Drop old credit_balance column
ALTER TABLE users DROP COLUMN credit_balance;

-- Rename credit_amount to euro_value in packages table
ALTER TABLE packages CHANGE COLUMN credit_amount euro_value INT NOT NULL;

-- Update existing euro_value based on old credit_amount (assuming 1 credit = 1 euro for simplicity)
-- This step might need more complex logic if your credits had varying euro values.
-- For now, we'll assume a direct transfer.
-- If you had a fixed euro value per credit, you would apply it here.
-- UPDATE packages SET euro_value = credit_amount * [your_euro_value_per_credit];

-- Note: The previous credit_amount column is now euro_value.