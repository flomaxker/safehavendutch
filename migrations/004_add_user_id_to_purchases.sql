-- migrations/004_add_user_id_to_purchases.sql
ALTER TABLE purchases
ADD COLUMN IF NOT EXISTS user_id INT NULL,
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
