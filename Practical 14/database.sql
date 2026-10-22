
CREATE DATABASE practical14;
USE practical14;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    event_date DATE
);

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(100) NOT NULL,
    allowed_role ENUM('admin', 'student') NOT NULL
);

INSERT INTO menu_items (title, url, allowed_role) VALUES
('Dashboard', 'index.php', 'admin'),
('Manage Users', 'users.php', 'admin'),
('Audit Logs', 'audit_logs.php', 'admin'),
('Student Home', 'index.php', 'student');

INSERT INTO students (name, email) VALUES
('Aarav Shah', 'aarav@example.com'),
('Riya Patel', 'riya@example.com'),
('Neel Mehta', 'neel@example.com');

INSERT INTO events (event_name, event_date) VALUES
('Tech Fest', '2026-11-15'),
('Coding Competition', '2026-12-05'),
('Web Development Workshop', '2027-01-10');

INSERT INTO registrations (student_id, event_id) VALUES
(1, 1), (2, 1), (3, 2), (1, 3);
