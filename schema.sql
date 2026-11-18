CREATE DATABASE IF NOT EXISTS auth_demo
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE auth_demo;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(50)  NOT NULL,
  username      VARCHAR(15)  NOT NULL UNIQUE,
  student_id    CHAR(8)      NOT NULL UNIQUE,
  age           TINYINT UNSIGNED NOT NULL,
  phone         CHAR(11)     NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;