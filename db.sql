CREATE DATABASE IF NOT EXISTS faculty_leave;
USE faculty_leave;

-- users: faculty, hod, principal, admin
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('faculty','hod','principal','admin') NOT NULL DEFAULT 'faculty',
  department VARCHAR(100) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- leaves
CREATE TABLE IF NOT EXISTS leaves (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  type ENUM('casual','medical','on-duty') NOT NULL,
  reason TEXT,
  document VARCHAR(255) DEFAULT NULL,
  substitute_id INT DEFAULT NULL,
  substitute_status ENUM('pending','accepted','rejected','none') DEFAULT 'none',
  hod_status ENUM('pending','approved','rejected') DEFAULT 'pending',
  principal_status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- notifications
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- substitute_requests
CREATE TABLE IF NOT EXISTS substitute_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  leave_id INT NOT NULL,
  from_user INT NOT NULL,
  to_user INT NOT NULL,
  status ENUM('pending','accepted','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (leave_id) REFERENCES leaves(id) ON DELETE CASCADE
);

-- Seed sample users (password: password)
INSERT IGNORE INTO users (name,email,password,role,department) VALUES
('Dr. A Faculty','faculty1@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'faculty', 'CSE'),
('Dr. B Faculty','faculty2@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'faculty', 'CSE'),
('HOD C','hod@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'hod', 'CSE'),
('Principal X','principal@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'principal', NULL),
('Admin','admin@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin', NULL);

