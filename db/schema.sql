  SET FOREIGN_KEY_CHECKS=0;
  DROP TABLE IF EXISTS vouches;
  DROP TABLE IF EXISTS teachers;
  DROP TABLE IF EXISTS cases;
  DROP TABLE IF EXISTS users;
  SET FOREIGN_KEY_CHECKS=1;

  CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE cases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_anonymous TINYINT(1) NOT NULL DEFAULT 0
  );

  INSERT INTO teachers (name, note) VALUES
    ('Rafal Rogal', 'Specjalista od baz danych'),
    ('Anna Nowak Los', 'Polonistka z wieloletnim doswiadczeniem'),
    ('Pawel Krajewski', 'Ekspert w przedmiocie matematyki');

  CREATE TABLE vouches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT DEFAULT NULL,
    case_id INT DEFAULT NULL,
    user_id INT NOT NULL,
    opinion TEXT,
    is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );
