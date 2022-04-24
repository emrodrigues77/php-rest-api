CREATE DATABASE api_books CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'api_user'@'localhost' IDENTIFIED BY 'A01!p16_i_password';
GRANT ALL ON api_books.* TO 'api_user'@'localhost';

USE api_books;

CREATE TABLE roles (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL, 
  PRIMARY KEY (`id`) 
);

INSERT INTO roles 
        (id, name) 
    VALUES 
        (1, 'Administrator'),
        (2, 'User');

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` smallint(5) unsigned NOT NULL,
  `origin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `callback` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_USER_ROLE` (`role`),
  CONSTRAINT `FK_USER_ROLE` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;


INSERT INTO users
        (login, token, role, origin, active, callback)
    VALUES
        ('eduardo', 't0k3n', 1, 'dev.localhost', 1, 'dev.localhost/php-rest-api/callback'),
        ('teste', 't3st3', 2, 'dev.localhost', 1, 'dev.localhost/php-rest-api/callback'),
        ('teste2', 't3st32', 2, 'localhost', 1, 'dev.localhost/php-rest-api/callback'),
        ('ricardo', 't0k3n', 1, 'dev.localhost', 0, 'dev.localhost/php-rest-api/callback');

CREATE TABLE authors (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL, 
    PRIMARY KEY (id)    
) ENGINE=INNODB;

INSERT INTO authors
        (id, name)
    VALUES
        (1, 'Eduardo Rodrigues'),
        (2, 'Mary Shelley');

CREATE TABLE `books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `author` INT(11) NOT NULL,
  `title` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_BOOK_AUTHOR` (`author`),
  UNIQUE KEY `NAME_UNIQUE` (`name`)
  CONSTRAINT `FK_BOOK_AUTHOR` FOREIGN KEY (`author`) REFERENCES `authors` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=INNODB;

INSERT INTO books
        (author, title, price)
    VALUES
        (1, 'A Flor Negra', 25),
        (1, 'O Presidente', 30),
        (2, 'Frankenstein', 32.25);
        
CREATE TABLE `entities` (  
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`) 
);

INSERT INTO `entities`
        (id, name)
    VALUES
        (1, 'authors'),
        (2, 'books');

CREATE TABLE `entities_methods` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `entity` tinyint(3) unsigned NOT NULL,
  `role` smallint(5) unsigned NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allowed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `FK_METHOD_ENTITY` (`entity`),
  KEY `FK_METHOD_ROLE` (`role`),
  CONSTRAINT `FK_METHOD_ENTITY` FOREIGN KEY (`entity`) REFERENCES `entities_methods` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_METHOD_ROLE` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO `entities_methods` 
      (entity, method, role, allowed) 
    VALUES 
      ('1', 'GET', '1', '1'),
      ('1', 'POST', '1', '1'),
      ('1', 'PUT', '1', '1'),
      ('1', 'DELETE', '1', '1'),
      ('1', 'GET', '2', '1'),
      ('1', 'POST', '2', '0'),
      ('1', 'PUT', '2', '0'),
      ('1', 'DELETE', '2', '0'),
      ('2', 'GET', '1', '1'),
      ('2', 'POST', '1', '1'),
      ('2', 'PUT', '1', '1'),
      ('2', 'DELETE', '1', '1'),
      ('2', 'GET', '2', '1'),
      ('2', 'POST', '2', '0'),
      ('2', 'PUT', '2', '0'),
      ('2', 'DELETE', '2', '0');