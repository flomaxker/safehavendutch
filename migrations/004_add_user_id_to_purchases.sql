-- migrations/004_add_user_id_to_purchases.sql
ALTER TABLE purchases
ADD COLUMN user_id INT,
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
