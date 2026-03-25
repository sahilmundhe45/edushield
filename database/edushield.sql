-- ============================================
-- EduShield – Database Schema & Dummy Data
-- ============================================

CREATE DATABASE IF NOT EXISTS edushield;
USE edushield;

-- Drop tables in reverse order (to avoid FK conflicts)
DROP TABLE IF EXISTS progress;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    category VARCHAR(100) NOT NULL,
    instructor VARCHAR(100) NOT NULL,
    thumbnail VARCHAR(255) DEFAULT 'default_course.png',
    video_url VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id)
) ENGINE=InnoDB;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'success', 'failed') NOT NULL DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (user_id, course_id)
) ENGINE=InnoDB;

-- Progress table
CREATE TABLE IF NOT EXISTS progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    progress_percent INT NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_progress (user_id, course_id)
) ENGINE=InnoDB;

-- ============================================
-- DUMMY DATA
-- ============================================

-- Passwords: all are 'password123' hashed with password_hash()
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@edushield.com', '$2y$10$J24yhsxxYdROfjwV7u.OWeJRNNsP15KCkfamHWmmFusI3pWdtCmDq', 'admin'),
('Aarav Sharma', 'aarav@example.com', '$2y$10$J24yhsxxYdROfjwV7u.OWeJRNNsP15KCkfamHWmmFusI3pWdtCmDq', 'student'),
('Priya Patel', 'priya@example.com', '$2y$10$J24yhsxxYdROfjwV7u.OWeJRNNsP15KCkfamHWmmFusI3pWdtCmDq', 'student'),
('Rahul Verma', 'rahul@example.com', '$2y$10$J24yhsxxYdROfjwV7u.OWeJRNNsP15KCkfamHWmmFusI3pWdtCmDq', 'student'),
('Sneha Gupta', 'sneha@example.com', '$2y$10$J24yhsxxYdROfjwV7u.OWeJRNNsP15KCkfamHWmmFusI3pWdtCmDq', 'student'),
('Vikram Singh', 'vikram@example.com', '$2y$10$J24yhsxxYdROfjwV7u.OWeJRNNsP15KCkfamHWmmFusI3pWdtCmDq', 'student');

INSERT INTO courses (title, description, price, category, instructor, thumbnail, video_url) VALUES
('Python for Beginners', 'Learn Python programming from scratch. Covers variables, loops, functions, OOP, and real-world projects.', 499.00, 'Programming', 'Dr. Ankit Mehta', 'python.png', 'https://www.youtube.com/embed/kqtD5dpn9C8'),
('Web Development Bootcamp', 'Master HTML, CSS, JavaScript and PHP. Build responsive websites from the ground up.', 799.00, 'Web Development', 'Prof. Sneha Roy', 'webdev.png', 'https://www.youtube.com/embed/pQN-pnXPaVg'),
('Data Science with R', 'Comprehensive data science course using R. Includes statistics, data visualization, and machine learning.', 999.00, 'Data Science', 'Dr. Rajesh Kumar', 'datascience.png', 'https://www.youtube.com/embed/fDRa82lxzaU'),
('Ethical Hacking Fundamentals', 'Learn penetration testing, vulnerability assessment, and cybersecurity essentials.', 1299.00, 'Cybersecurity', 'Prof. Vikram Joshi', 'hacking.png', 'https://www.youtube.com/embed/3Kq1MIfTWCE'),
('Machine Learning A-Z', 'Deep dive into ML algorithms, neural networks, and AI. Hands-on projects with Python.', 1499.00, 'AI & ML', 'Dr. Meera Iyer', 'ml.png', 'https://www.youtube.com/embed/GwIo3gDZCVQ'),
('Java Programming Masterclass', 'Complete Java course from basics to advanced – OOP, collections, multithreading, and Spring Boot intro.', 699.00, 'Programming', 'Prof. Arjun Nair', 'java.png', 'https://www.youtube.com/embed/eIrMbAQSU34'),
('Digital Marketing Pro', 'SEO, social media marketing, Google Ads, and email marketing strategies for business growth.', 599.00, 'Marketing', 'Nisha Kapoor', 'marketing.png', 'https://www.youtube.com/embed/bixR-KIJKYM'),
('Cloud Computing with AWS', 'Hands-on AWS course covering EC2, S3, Lambda, RDS, and deployment best practices.', 1199.00, 'Cloud', 'Dr. Saurabh Tiwari', 'aws.png', 'https://www.youtube.com/embed/k1RI5locZE4'),
('Mobile App Development (Flutter)', 'Build cross-platform mobile apps with Flutter and Dart. Includes Firebase integration.', 899.00, 'Mobile Dev', 'Prof. Kavita Desai', 'flutter.png', 'https://www.youtube.com/embed/1ukSR1GRtMU'),
('Database Management with MySQL', 'Master SQL, database design, normalization, stored procedures, and performance tuning.', 449.00, 'Database', 'Dr. Ankit Mehta', 'mysql.png', 'https://www.youtube.com/embed/7S_tz1z_5bA'),
('UI/UX Design Fundamentals', 'Learn design thinking, wireframing, prototyping with Figma, and user research.', 549.00, 'Design', 'Riya Malhotra', 'uiux.png', 'https://www.youtube.com/embed/c9Wg6Cb_YlU'),
('Blockchain & Cryptocurrency', 'Understand blockchain technology, smart contracts, Ethereum, and decentralized apps.', 1399.00, 'Blockchain', 'Prof. Deepak Chauhan', 'blockchain.png', 'https://www.youtube.com/embed/SSo_EIwHSd4');

-- Enrollments (students enrolled in courses)
INSERT INTO enrollments (user_id, course_id) VALUES
(2, 1), (2, 4), (2, 5),
(3, 1), (3, 2), (3, 7),
(4, 3), (4, 6), (4, 8),
(5, 2), (5, 5), (5, 9),
(6, 1), (6, 10), (6, 12);

-- Payments
INSERT INTO payments (user_id, course_id, amount, status) VALUES
(2, 1, 499.00, 'success'),
(2, 4, 1299.00, 'success'),
(2, 5, 1499.00, 'success'),
(3, 1, 499.00, 'success'),
(3, 2, 799.00, 'success'),
(3, 7, 599.00, 'success'),
(4, 3, 999.00, 'success'),
(4, 6, 699.00, 'success'),
(4, 8, 1199.00, 'success'),
(5, 2, 799.00, 'success'),
(5, 5, 1499.00, 'success'),
(5, 9, 899.00, 'success'),
(6, 1, 499.00, 'success'),
(6, 10, 449.00, 'success'),
(6, 12, 1399.00, 'success');

-- Reviews
INSERT INTO reviews (user_id, course_id, rating, comment) VALUES
(2, 1, 5, 'Excellent course! Very well explained for beginners.'),
(2, 4, 4, 'Good content but could use more hands-on labs.'),
(3, 1, 4, 'Great introduction to Python. Loved the projects.'),
(3, 2, 5, 'Best web development course I have ever taken!'),
(4, 3, 5, 'Dr. Kumar explains complex topics very clearly.'),
(4, 6, 4, 'Solid Java course. The OOP section was fantastic.'),
(5, 2, 4, 'Very comprehensive and well-structured.'),
(5, 5, 5, 'Mind-blowing ML content. Highly recommended!'),
(6, 1, 5, 'Perfect for absolute beginners. 10/10!'),
(6, 10, 4, 'Good MySQL course with practical examples.');

-- Progress
INSERT INTO progress (user_id, course_id, progress_percent) VALUES
(2, 1, 75), (2, 4, 40), (2, 5, 20),
(3, 1, 100), (3, 2, 60), (3, 7, 30),
(4, 3, 50), (4, 6, 85), (4, 8, 10),
(5, 2, 90), (5, 5, 45), (5, 9, 15),
(6, 1, 65), (6, 10, 55), (6, 12, 5);
