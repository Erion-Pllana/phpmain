-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add user_id column to tasks table
ALTER TABLE tasks ADD COLUMN user_id INT;

-- Add foreign key constraint
ALTER TABLE tasks ADD CONSTRAINT fk_user_id 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Update existing tasks to have user_id (set to 1 for admin user)
UPDATE tasks SET user_id = 1 WHERE user_id IS NULL;