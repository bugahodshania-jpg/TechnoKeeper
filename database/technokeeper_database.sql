-- ==============================
-- DATABASE
-- ==============================
CREATE DATABASE IF NOT EXISTS technokeeper_database;
USE technokeeper_database;

-- ==============================
-- TABLE: users
-- ==============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(9) NOT NULL UNIQUE COMMENT 'Format: xxxx-xxxx',
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    extension_name VARCHAR(10),
    date_of_birth DATE NOT NULL,
    age INT NOT NULL,
    sex ENUM('Male', 'Female') NOT NULL,
    contact_number VARCHAR(50) NOT NULL,
    work_position VARCHAR(150) NOT NULL,
    school_department VARCHAR(150) NOT NULL,
    username VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    pin_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,

    INDEX idx_user_id (user_id),
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

-- ==============================
-- TABLE: admin_information
-- ==============================
CREATE TABLE admin_information (
    admin_info_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    position VARCHAR(150),
    employee_id VARCHAR(100) UNIQUE,
    contact_number VARCHAR(50),
    office_department VARCHAR(150),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==============================
-- TABLE: user_addresses
-- ==============================
CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    street VARCHAR(100) NOT NULL,
    barangay VARCHAR(50) NOT NULL,
    city_municipality VARCHAR(50) NOT NULL,
    province VARCHAR(50) NOT NULL,
    country VARCHAR(50) NOT NULL,
    zip_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==============================
-- TABLE: user_security_questions
-- ==============================
CREATE TABLE user_security_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    q1 VARCHAR(255) NOT NULL,
    a1 VARCHAR(255) NOT NULL,
    q2 VARCHAR(255) NOT NULL,
    a2 VARCHAR(255) NOT NULL,
    q3 VARCHAR(255) NOT NULL,
    a3 VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_user (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- ==============================
-- TABLE: user_sessions
-- ==============================
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    failed_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    lock_stage INT DEFAULT 0,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==============================
-- TABLE: RFID_cards
-- ==============================
CREATE TABLE RFID_cards (
    card_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    admin_info_id INT NULL,
    rfid_uid VARCHAR(200) UNIQUE NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'active',

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_info_id) REFERENCES admin_information(admin_info_id) ON DELETE SET NULL
);

-- ==============================
-- TABLE: override_credentials
-- ==============================
CREATE TABLE override_credentials (
    override_cred_id INT AUTO_INCREMENT PRIMARY KEY,
    override_card_uid VARCHAR(200) UNIQUE NOT NULL,
    override_pin VARCHAR(50),
    assigned_to INT NOT NULL,
    status ENUM('active', 'suspended', 'revoked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (assigned_to) REFERENCES admin_information(admin_info_id) ON DELETE CASCADE
);

-- ==============================
-- TABLE: schedules
-- ==============================
CREATE TABLE schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    admin_info_id INT NULL,
    card_id INT NOT NULL,
    date DATE NOT NULL,
    time_start TIME NOT NULL,
    time_end TIME NOT NULL,
    room VARCHAR(150) NOT NULL DEFAULT 'Room 206',

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_info_id) REFERENCES admin_information(admin_info_id) ON DELETE SET NULL,
    FOREIGN KEY (card_id) REFERENCES RFID_cards(card_id) ON DELETE CASCADE
);

-- ==============================
-- TABLE: override_access
-- ==============================
CREATE TABLE override_access (
    override_id INT AUTO_INCREMENT PRIMARY KEY,
    override_cred_id INT NOT NULL,
    admin_info_id INT NOT NULL,
    method_used ENUM('override_card', 'override_pin') NOT NULL,
    reason TEXT,
    override_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    room VARCHAR(150),
    schedule_id INT NULL,

    FOREIGN KEY (override_cred_id) REFERENCES override_credentials(override_cred_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_info_id) REFERENCES admin_information(admin_info_id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(schedule_id) ON DELETE SET NULL
);

-- ==============================
-- TABLE: access_log
-- ==============================
CREATE TABLE access_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    admin_info_id INT NULL,
    card_id INT NOT NULL,
    schedule_id INT NULL,
    access_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    access_status ENUM('granted', 'denied') NOT NULL,
    method_access ENUM('RFID', 'PIN', 'OVERRIDE') NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_info_id) REFERENCES admin_information(admin_info_id) ON DELETE SET NULL,
    FOREIGN KEY (card_id) REFERENCES RFID_cards(card_id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(schedule_id) ON DELETE SET NULL
);

CREATE OR REPLACE VIEW access_logs_view AS
SELECT 
    al.log_id,
    u.first_name AS user_first_name,
    u.last_name AS user_last_name,
    ai.first_name AS admin_first_name,
    ai.last_name AS admin_last_name,
    rc.rfid_uid AS card_rfid,
    s.room AS schedule_room,
    s.date AS schedule_date,
    al.access_timestamp,
    al.access_status,
    al.method_access
FROM access_log al
LEFT JOIN users u ON al.user_id = u.id
LEFT JOIN admin_information ai ON al.admin_info_id = ai.admin_info_id
LEFT JOIN RFID_cards rc ON al.card_id = rc.card_id
LEFT JOIN schedules s ON al.schedule_id = s.schedule_id;


-- ==============================
-- Sample Admin User
-- ==============================

-- 1. Insert into `users`
INSERT INTO users (
    user_id, first_name, middle_name, last_name, extension_name, date_of_birth, age, sex,
    contact_number, work_position, school_department, username, email, password_hash, role, pin_code
) VALUES (
    '1001-0001', 'John', 'A.', 'Doe', 'Jr.', '1985-04-15', 40, 'Male',
    '09171234567', 'IT Manager', 'Technology Department', 'john.doe', 'john.doe@example.com',
    '$2y$10$abcdefghijklmnopqrstuv', -- hashed password (example, replace with real hash)
    'admin', '1234'
);

-- 2. Insert into `admin_information`
INSERT INTO admin_information (
    user_id, first_name, middle_name, last_name, address, position, employee_id, contact_number, office_department
) VALUES (
    1, 'John', 'A.', 'Doe', '123 Tech Street', 'IT Manager', 'EMP-0001', '09171234567', 'Technology Department'
);

-- 3. Insert into `user_addresses`
INSERT INTO user_addresses (
    user_id, street, barangay, city_municipality, province, country, zip_code
) VALUES (
    1, '123 Tech Street', 'Barangay 1', 'Metro City', 'Tech Province', 'Philippines', '1001'
);

-- 4. Insert into `user_security_questions`
INSERT INTO user_security_questions (
    user_id, q1, a1, q2, a2, q3, a3
) VALUES (
    1,
    'What is your mother\'s maiden name?', 'Gultiano',
    'What was the name of your first pet?', 'hetty',
    'What is your favorite color?', 'Black'
);

-- 5. Insert into `RFID_cards`
INSERT INTO RFID_cards (
    user_id, admin_info_id, rfid_uid, status
) VALUES (
    1, 1, 'RFID123456789', 'active'
);

ALTER TABLE schedules
ADD COLUMN notify_time DATETIME NULL COMMENT 'Time to notify user 30 mins before schedule';

ALTER TABLE schedules 
ADD COLUMN status ENUM('pending','approved','overridden') NOT NULL DEFAULT 'pending';
 
 ALTER TABLE schedules
ADD COLUMN claimed_card_id INT NULL COMMENT 'card_id of the card that claimed this schedule (first-come)',
ADD COLUMN claimed_at DATETIME NULL COMMENT 'timestamp when claimed';

-- add FK if you want (optional)
ALTER TABLE schedules
ADD CONSTRAINT fk_schedules_claimed_card FOREIGN KEY (claimed_card_id) REFERENCES RFID_cards(card_id) ON DELETE SET NULL;

