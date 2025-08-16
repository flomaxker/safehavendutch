-- migrations/005_add_credit_balance_to_users.sql
ALTER TABLE users
ADD COLUMN credit_balance INT NOT NULL DEFAULT 0;
