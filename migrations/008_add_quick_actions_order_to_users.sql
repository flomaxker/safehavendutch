-- Add quick_actions_order column to users table
ALTER TABLE users ADD COLUMN quick_actions_order JSON DEFAULT (JSON_ARRAY());