CREATE DATABASE at;

USE at;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    activation_token VARCHAR(255),
    role ENUM('user', 'company') NOT NULL,
    company_name VARCHAR(255),
    company_website VARCHAR(255),
    company_address TEXT,
    company_description TEXT
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    job_title VARCHAR(255) NOT NULL,
    job_description TEXT NOT NULL,
    category VARCHAR(255),
    location VARCHAR(255),
    application_deadline DATE,
    is_active BOOLEAN DEFAULT FALSE,
    is_expired BOOLEAN DEFAULT FALSE,,
    company_name VARCHAR(255),
    FOREIGN KEY (company_id) REFERENCES users(id)
);

CREATE TABLE job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT,
    user_id INT,
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);


SHOW GRANTS FOR 'root'@'localhost';

ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;


CREATE USER 'job_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON job.* TO 'job_user'@'localhost';
FLUSH PRIVILEGES;


CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE job_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin'
);

INSERT INTO admin_users (username, password_hash, role) VALUES ('AT', 'sandor3fiam', 'admin');

ALTER TABLE job_posts ADD COLUMN is_approved TINYINT(1) DEFAULT 0;
ALTER TABLE job_posts ADD COLUMN is_active TINYINT(1) DEFAULT 1;

CREATE TABLE job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    deadline DATE NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO job_posts (company_id, position, description, is_approved) VALUES
(8, 'Software Engineer', 'Develop and maintain software solutions.', 0),
(8, 'Project Manager', 'Manage projects and lead teams to success.', 0);

SELECT * FROM job_posts WHERE is_approved = 0;

SELECT * FROM jobs WHERE is_active = 1 AND is_expired = 0;
