-- ============================================================
-- InternConnect Buea - Clean Database Schema (Stages 1–12)
-- Import this file in phpMyAdmin (XAMPP) or run:
--   mysql -u root -p < database/internconnect.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS internconnect
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE internconnect;

-- ------------------------------------------------------------
-- 1. users: Accounts table (students, organizations, admins)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('student','organization','admin') NOT NULL,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2. students: Profile table for student users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    student_id  INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL UNIQUE,
    full_name   VARCHAR(120) NOT NULL,
    phone       VARCHAR(30) DEFAULT NULL,
    university  VARCHAR(150) DEFAULT NULL,
    programme   VARCHAR(150) DEFAULT NULL,
    level       ENUM('100','200','300','400','500','Masters') DEFAULT NULL,
    location    VARCHAR(100) DEFAULT NULL,
    bio         TEXT DEFAULT NULL,
    cv_path     VARCHAR(255) DEFAULT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3. organizations: Profile table for organization users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS organizations (
    org_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL UNIQUE,
    org_name    VARCHAR(150) NOT NULL,
    sector      VARCHAR(100) DEFAULT NULL,
    location    VARCHAR(100) DEFAULT NULL,
    phone       VARCHAR(30) DEFAULT NULL,
    website     VARCHAR(200) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_organizations_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4. skills: Master skills list
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS skills (
    skill_id    INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pre-populate common skills for selection
INSERT IGNORE INTO skills (name) VALUES
('HTML & CSS'),
('JavaScript'),
('PHP'),
('MySQL'),
('Python'),
('Graphic Design'),
('Accounting'),
('Data Analysis'),
('Digital Marketing'),
('Cybersecurity');

-- ------------------------------------------------------------
-- 5. student_skills: Junction table for student skills
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS student_skills (
    student_id  INT NOT NULL,
    skill_id    INT NOT NULL,
    PRIMARY KEY (student_id, skill_id),
    CONSTRAINT fk_ss_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_ss_skill FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 6. internships: Posted internship opportunities
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS internships (
    internship_id  INT AUTO_INCREMENT PRIMARY KEY,
    org_id         INT NOT NULL,
    title          VARCHAR(150) NOT NULL,
    category       VARCHAR(100) NOT NULL,
    location       VARCHAR(100) NOT NULL,
    duration       VARCHAR(50) DEFAULT NULL,
    required_level ENUM('100','200','300','400','500','Masters') DEFAULT NULL,
    positions      INT NOT NULL DEFAULT 1,
    deadline       DATE DEFAULT NULL,
    description    TEXT NOT NULL,
    requirements   TEXT DEFAULT NULL,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_internships_org
        FOREIGN KEY (org_id) REFERENCES organizations(org_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 6.5. internship_skills: Junction table for internship requirements
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS internship_skills (
    internship_id INT NOT NULL,
    skill_id      INT NOT NULL,
    PRIMARY KEY (internship_id, skill_id),
    CONSTRAINT fk_is_internship FOREIGN KEY (internship_id) REFERENCES internships(internship_id) ON DELETE CASCADE,
    CONSTRAINT fk_is_skill FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 7. applications: Applications submitted by students
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id     INT NOT NULL,
    internship_id  INT NOT NULL,
    cover_letter   TEXT DEFAULT NULL,
    status         ENUM('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
    applied_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_student_internship (student_id, internship_id),
    CONSTRAINT fk_app_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_app_internship FOREIGN KEY (internship_id) REFERENCES internships(internship_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 8. saved_internships: Bookmarked internships by students
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS saved_internships (
    student_id    INT NOT NULL,
    internship_id INT NOT NULL,
    saved_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, internship_id),
    CONSTRAINT fk_saved_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    CONSTRAINT fk_saved_internship FOREIGN KEY (internship_id) REFERENCES internships(internship_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Initial Default System Administrator Account
-- Email: admin@internconnect.cm | Password: Admin@123
-- ------------------------------------------------------------
INSERT IGNORE INTO users (email, password, role) VALUES (
    'admin@internconnect.cm',
    '$2y$10$oFo.M2uxcWYYYsHfAXR.z.HRCnTZvh9xYvsiP9zvP11qxePXCRRPC',
    'admin'
);
