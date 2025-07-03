ALTER TABLE users MODIFY COLUMN role ENUM('student', 'admin', 'member') NOT NULL DEFAULT 'student';
UPDATE users SET role = 'member' WHERE role = 'student';
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'member') NOT NULL DEFAULT 'member';