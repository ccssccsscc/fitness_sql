--
-- Script was generated by Devart dbForge Studio for MySQL, Version 9.2.128.0
-- Product home page: http://www.devart.com/dbforge/mysql/studio
-- Script date 15/11/2023 14:16:24
-- Server version: 8.0.34
-- Client version: 4.1
--

-- 
-- Disable foreign keys
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Set SQL mode
-- 
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

DROP DATABASE IF EXISTS fc2;

CREATE DATABASE fc2
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;

--
-- Set default database
--
USE fc2;

--
-- Create table `review`
--
CREATE TABLE review (
  review_code int NOT NULL AUTO_INCREMENT,
  comment text DEFAULT NULL,
  date_written date DEFAULT NULL,
  time_written time DEFAULT NULL,
  rating int DEFAULT NULL,
  PRIMARY KEY (review_code)
)
ENGINE = INNODB,
AUTO_INCREMENT = 12,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

DELIMITER $$

--
-- Create function `GetTotalReviewCount`
--
CREATE
DEFINER = 'root'@'localhost'
FUNCTION GetTotalReviewCount ()
RETURNS int(11)
DETERMINISTIC
BEGIN
  DECLARE total_review_count int;

  SELECT
    COUNT(*) INTO total_review_count
  FROM fc2.review;

  RETURN total_review_count;
END
$$

--
-- Create function `GetAverageRating`
--
CREATE
DEFINER = 'root'@'localhost'
FUNCTION GetAverageRating ()
RETURNS decimal(3, 2)
DETERMINISTIC
BEGIN
  DECLARE avg_rating decimal(3, 2);

  SELECT
    AVG(rating) INTO avg_rating
  FROM fc2.review;

  RETURN avg_rating;
END
$$

--
-- Create procedure `UpdateReview`
--
CREATE
DEFINER = 'root'@'localhost'
PROCEDURE UpdateReview (IN in_review_code int,
IN in_comment text,
IN in_date_written date,
IN in_time_written time,
IN in_rating int)
BEGIN
  UPDATE fc2.review
  SET comment = in_comment,
      date_written = in_date_written,
      time_written = in_time_written,
      rating = in_rating
  WHERE review_code = in_review_code;
END
$$

DELIMITER ;

--
-- Create table `client`
--
CREATE TABLE client (
  client_code int NOT NULL,
  first_name varchar(50) DEFAULT NULL,
  last_name varchar(50) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  address varchar(100) DEFAULT NULL,
  age int DEFAULT NULL,
  phone varchar(15) DEFAULT NULL,
  review_code int DEFAULT NULL,
  gender enum ('man', 'woman') DEFAULT NULL,
  PRIMARY KEY (client_code)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create foreign key
--
ALTER TABLE client
ADD CONSTRAINT client_ibfk_1 FOREIGN KEY (review_code)
REFERENCES review (review_code);

DELIMITER $$

--
-- Create procedure `ChangeGender`
--
CREATE
DEFINER = 'root'@'localhost'
PROCEDURE ChangeGender (IN in_client_code int,
IN action enum ('man', 'woman'))
BEGIN
  IF action = 'man' THEN
    UPDATE fc2.client
    SET gender = 'man'
    WHERE client_code = in_client_code;
  ELSEIF action = 'woman' THEN
    UPDATE fc2.client
    SET gender = 'woman'
    WHERE client_code = in_client_code;
  ELSE
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = '������������ ��������';
  END IF;

  SELECT
    *
  FROM fc2.client
  WHERE client_code = client_code;
END
$$

DELIMITER ;

--
-- Create table `visit_to_fitness_club`
--
CREATE TABLE visit_to_fitness_club (
  visit_code int NOT NULL,
  client_code int NOT NULL,
  date date NOT NULL,
  start_time time NOT NULL,
  end_time time NOT NULL,
  PRIMARY KEY (visit_code, client_code)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create foreign key
--
ALTER TABLE visit_to_fitness_club
ADD CONSTRAINT visit_to_fitness_club_ibfk_1 FOREIGN KEY (client_code)
REFERENCES client (client_code);

DELIMITER $$

--
-- Create procedure `DeleteVisit`
--
CREATE
DEFINER = 'root'@'localhost'
PROCEDURE DeleteVisit (IN in_visit_code int,
IN in_client_code int)
BEGIN
  DELETE
    FROM fc2.visit_to_fitness_club
  WHERE visit_code = in_visit_code
    AND client_code = in_client_code;
END
$$

DELIMITER ;

--
-- Create table `specification`
--
CREATE TABLE specification (
  specification_code int NOT NULL AUTO_INCREMENT,
  specification_name varchar(100) DEFAULT NULL,
  PRIMARY KEY (specification_code)
)
ENGINE = INNODB,
AUTO_INCREMENT = 11,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create table `coach`
--
CREATE TABLE coach (
  coach_code int NOT NULL AUTO_INCREMENT,
  specification_code int DEFAULT NULL,
  Name varchar(50) DEFAULT NULL,
  PRIMARY KEY (coach_code)
)
ENGINE = INNODB,
AUTO_INCREMENT = 11,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create foreign key
--
ALTER TABLE coach
ADD CONSTRAINT coach_ibfk_1 FOREIGN KEY (specification_code)
REFERENCES specification (specification_code) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Create table `subscription_type`
--
CREATE TABLE subscription_type (
  subscription_code_type_code int NOT NULL AUTO_INCREMENT,
  type varchar(50) DEFAULT NULL,
  PRIMARY KEY (subscription_code_type_code)
)
ENGINE = INNODB,
AUTO_INCREMENT = 4,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create table `subscription`
--
CREATE TABLE subscription (
  subscription_code int NOT NULL,
  number_of_classes int NOT NULL,
  price decimal(10, 2) NOT NULL,
  duration int NOT NULL,
  client_code int NOT NULL,
  subscription_code_type_code int NOT NULL,
  start_date date NOT NULL,
  end_date date NOT NULL,
  PRIMARY KEY (subscription_code, client_code)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create foreign key
--
ALTER TABLE subscription
ADD CONSTRAINT subscription_ibfk_1 FOREIGN KEY (client_code)
REFERENCES client (client_code);

--
-- Create foreign key
--
ALTER TABLE subscription
ADD CONSTRAINT subscription_ibfk_2 FOREIGN KEY (subscription_code_type_code)
REFERENCES subscription_type (subscription_code_type_code);

DELIMITER $$

--
-- Create function `GetMonthlySubscriptionCostForClient`
--
CREATE
DEFINER = 'root'@'localhost'
FUNCTION GetMonthlySubscriptionCostForClient (in_client_code int)
RETURNS decimal(10, 2)
DETERMINISTIC
BEGIN
  DECLARE monthly_cost decimal(10, 2);

  SELECT
    price / duration INTO monthly_cost
  FROM fc2.subscription
  WHERE client_code = in_client_code
  AND CURRENT_DATE BETWEEN start_date AND end_date
  LIMIT 1;

  RETURN monthly_cost;
END
$$

--
-- Create function `GetActiveSubscriptionsForClient`
--
CREATE
DEFINER = 'root'@'localhost'
FUNCTION GetActiveSubscriptionsForClient (in_client_code int)
RETURNS varchar(8) CHARSET utf8mb4
DETERMINISTIC
BEGIN
  DECLARE active_subscriptions int;

  SELECT
    COUNT(*) INTO active_subscriptions
  FROM fc2.subscription
  WHERE client_code = in_client_code
  AND CURRENT_DATE BETWEEN start_date AND end_date;

  IF active_subscriptions > 0 THEN
    RETURN 'active';
  ELSE
    RETURN 'noactive';
  END IF;
END
$$

DELIMITER ;

--
-- Create table `subscription_options`
--
CREATE TABLE subscription_options (
  subscription_code int NOT NULL,
  client_code int NOT NULL,
  info_about_subs text DEFAULT NULL,
  PRIMARY KEY (subscription_code, client_code)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create foreign key
--
ALTER TABLE subscription_options
ADD CONSTRAINT subscription_options_ibfk_1 FOREIGN KEY (subscription_code)
REFERENCES subscription (subscription_code);

--
-- Create foreign key
--
ALTER TABLE subscription_options
ADD CONSTRAINT subscription_options_ibfk_2 FOREIGN KEY (client_code)
REFERENCES client (client_code);

--
-- Create table `classes_type`
--
CREATE TABLE classes_type (
  type_code int NOT NULL AUTO_INCREMENT,
  type_name varchar(100) NOT NULL,
  PRIMARY KEY (type_code)
)
ENGINE = INNODB,
AUTO_INCREMENT = 11,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create table `class`
--
CREATE TABLE class (
  class_id int NOT NULL,
  type_code int NOT NULL,
  description text DEFAULT NULL,
  time time NOT NULL,
  status varchar(50) NOT NULL,
  subscription_code int NOT NULL,
  client_code int NOT NULL,
  visit_code int NOT NULL,
  coach_code int DEFAULT NULL,
  PRIMARY KEY (subscription_code, client_code, class_id)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_0900_ai_ci,
ROW_FORMAT = DYNAMIC;

--
-- Create foreign key
--
ALTER TABLE class
ADD CONSTRAINT class_ibfk_1 FOREIGN KEY (subscription_code)
REFERENCES subscription (subscription_code) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Create foreign key
--
ALTER TABLE class
ADD CONSTRAINT class_ibfk_2 FOREIGN KEY (client_code)
REFERENCES client (client_code) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Create foreign key
--
ALTER TABLE class
ADD CONSTRAINT class_ibfk_3 FOREIGN KEY (visit_code)
REFERENCES visit_to_fitness_club (visit_code) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Create foreign key
--
ALTER TABLE class
ADD CONSTRAINT class_ibfk_4 FOREIGN KEY (coach_code)
REFERENCES coach (coach_code);

--
-- Create foreign key
--
ALTER TABLE class
ADD CONSTRAINT class_ibfk_5 FOREIGN KEY (type_code)
REFERENCES classes_type (type_code) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Dumping data for table review
--
INSERT INTO review VALUES
(1, 'dsdsdsds', '2023-11-15', '12:00:11', 4),
(2, 'Excellent experience', '2023-01-02', '14:30:00', 4),
(3, 'Could be better', '2023-01-03', '10:45:00', 3),
(4, 'Amazing facilities', '2023-01-04', '09:15:00', 5),
(5, 'Not satisfied', '2023-01-05', '17:00:00', 2),
(6, 'Friendly staff', '2023-01-06', '11:30:00', 4),
(7, 'Great atmosphere', '2023-01-07', '16:20:00', 5),
(8, 'Poor service', '2023-01-08', '13:45:00', 2),
(9, 'Enjoyable classes', '2023-01-09', '18:00:00', 4),
(10, 'Could improve cleanliness', '2023-01-10', '15:10:00', 3),
(11, 'Отличный сервис!', '2023-11-15', '13:34:41', 5);

-- 
-- Dumping data for table subscription_type
--
INSERT INTO subscription_type VALUES
(1, 'Basic'),
(2, 'Premium'),
(3, 'Gold');

-- 
-- Dumping data for table client
--
INSERT INTO client VALUES
(101, 'John', 'Doe', 'john.doe@example.com', '123 Main Street', 30, '555-1234', 1, 'woman'),
(102, 'Jane', 'Doe', 'jane.doe@example.com', '456 Oak Avenue', 25, '555-5678', 2, 'woman'),
(103, 'Bob', 'Smith', 'bob.smith@example.com', '789 Elm Road', 40, '555-9876', 3, 'woman'),
(104, 'Alice', 'Johnson', 'alice.johnson@example.com', '101 Maple Lane', 35, '555-4321', 4, NULL),
(105, 'James', 'Brown', 'james.brown@example.com', '202 Cedar Drive', 28, '555-8765', 5, NULL),
(106, 'New', 'Client', 'new.client@example.com', '456 Oak Avenue', 25, '555-5678', 6, NULL),
(107, 'New', 'Client', 'new.client@example.com', '456 Oak Avenue', 25, '555-5678', 7, NULL),
(108, 'New1', 'Client1', 'new.client@example.com', '456 Oak Avenue', 25, '555-5678', 8, NULL),
(109, 'Client2', 'Name2', 'client2@example.com', '789 Elm Road', 30, '555-9876', 9, NULL),
(110, 'Client3', 'Name3', 'client3@example.com', '101 Maple Lane', 12, '555-4321', 10, NULL);

-- 
-- Dumping data for table specification
--
INSERT INTO specification VALUES
(1, 'Cardio'),
(2, 'Strength training'),
(3, 'Yoga'),
(4, 'Pilates'),
(5, 'Zumba'),
(6, 'CrossFit'),
(7, 'Spin'),
(8, 'HIIT'),
(9, 'Barre'),
(10, 'Kickboxing');

-- 
-- Dumping data for table visit_to_fitness_club
--
INSERT INTO visit_to_fitness_club VALUES
(201, 101, '2023-01-01', '08:00:00', '10:00:00'),
(202, 102, '2023-01-02', '09:30:00', '11:30:00'),
(203, 103, '2023-01-03', '11:00:00', '13:00:00'),
(204, 104, '2023-01-04', '13:30:00', '15:30:00'),
(205, 105, '2023-01-05', '15:00:00', '17:00:00'),
(206, 106, '2023-01-06', '17:30:00', '19:30:00'),
(207, 107, '2023-01-07', '18:00:00', '20:00:00'),
(208, 108, '2023-01-08', '10:00:00', '12:00:00'),
(209, 109, '2023-01-09', '12:30:00', '14:30:00'),
(210, 110, '2023-01-10', '14:00:00', '16:00:00');

-- 
-- Dumping data for table subscription
--
INSERT INTO subscription VALUES
(301, 10, 100.00, 30, 106, 1, '2023-01-01', '2024-01-30'),
(302, 15, 150.00, 45, 107, 1, '2023-01-02', '2024-02-15'),
(303, 12, 120.00, 40, 108, 2, '2023-01-03', '2024-02-12'),
(304, 8, 80.00, 20, 109, 2, '2023-01-04', '2024-01-24'),
(305, 20, 200.00, 60, 110, 3, '2023-01-05', '2024-03-01'),
(306, 10, 100.00, 30, 101, 3, '2023-01-06', '2024-01-30'),
(307, 15, 150.00, 45, 102, 2, '2023-01-07', '2024-02-15'),
(308, 12, 120.00, 40, 103, 2, '2023-01-08', '2024-02-12'),
(309, 8, 80.00, 20, 104, 1, '2023-01-09', '2024-01-24'),
(310, 20, 200.00, 60, 105, 1, '2023-01-10', '2024-03-01');

-- 
-- Dumping data for table coach
--
INSERT INTO coach VALUES
(1, 1, 'Coach 1'),
(2, 2, 'Coach 2'),
(3, 3, 'Coach 3'),
(4, 4, 'Coach 4'),
(5, 5, 'Coach 5'),
(6, 6, 'Coach 6'),
(7, 7, 'Coach 7'),
(8, 8, 'Coach 8'),
(9, 9, 'Coach 9'),
(10, 10, 'Coach 10');

-- 
-- Dumping data for table classes_type
--
INSERT INTO classes_type VALUES
(1, 'Cardio'),
(2, 'Strength training'),
(3, 'Yoga'),
(4, 'Pilates'),
(5, 'Zumba'),
(6, 'CrossFit'),
(7, 'Spin'),
(8, 'HIIT'),
(9, 'Barre'),
(10, 'Kickboxing');

-- 
-- Dumping data for table subscription_options
--
INSERT INTO subscription_options VALUES
(301, 106, 'Option 1 for Client 106'),
(302, 107, 'Option 2 for Client 107'),
(303, 108, 'Option 3 for Client 108'),
(304, 109, 'Option 4 for Client 109'),
(305, 110, 'Option 5 for Client 110'),
(306, 101, 'Option 6 for Client 101'),
(307, 102, 'Option 7 for Client 102'),
(308, 103, 'Option 8 for Client 103'),
(309, 104, 'Option 9 for Client 104'),
(310, 105, 'Option 10 for Client 105');

-- 
-- Dumping data for table class
--
INSERT INTO class VALUES
(1, 1, 'Cardio Class', '10:00:00', 'Active', 301, 106, 206, 1),
(2, 2, 'Strength Training', '11:30:00', 'Active', 302, 107, 207, 2),
(3, 3, 'Yoga Session', '13:00:00', 'Active', 303, 108, 208, 3),
(4, 4, 'Pilates Class', '14:30:00', 'Active', 304, 109, 209, 1),
(5, 5, 'Zumba Dance', '16:00:00', 'Active', 305, 110, 210, 2),
(6, 6, 'Cardio Class', '17:30:00', 'Active', 306, 101, 201, 4),
(7, 7, 'Strength Training', '18:00:00', 'Active', 307, 102, 202, 2),
(8, 8, 'Yoga Session', '10:00:00', 'Active', 308, 103, 203, 3),
(9, 9, 'Pilates Class', '11:30:00', 'Active', 309, 104, 204, 4),
(10, 10, 'Zumba Dance', '13:00:00', 'Active', 310, 105, 205, 2);

--
-- Set default database
--
USE fc2;

DELIMITER $$

--
-- Create trigger `before_insert_time`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER before_insert_time
BEFORE INSERT
ON review
FOR EACH ROW
BEGIN
  SET NEW.date_written = CURRENT_DATE();
  SET NEW.time_written = CURRENT_TIME();
END
$$

--
-- Create trigger `before_insert_review`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER before_insert_review
BEFORE INSERT
ON review
FOR EACH ROW
FOLLOWS before_insert_time
BEGIN
  SET NEW.review_code = (SELECT
      IFNULL(MAX(review_code), 0) + 1
    FROM fc2.review);
END
$$

--
-- Create trigger `before_insert_client`
--
CREATE
DEFINER = 'root'@'localhost'
TRIGGER before_insert_client
BEFORE INSERT
ON client
FOR EACH ROW
BEGIN
  IF NEW.age < 14 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = '������ ��������� �������� ������ 14 ���';
  END IF;
END
$$

DELIMITER ;

-- 
-- Restore previous SQL mode
-- 
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

-- 
-- Enable foreign keys
-- 
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;