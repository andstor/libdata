SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;
SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0;
SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema lib_data
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema lib_data
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `lib_data`
  DEFAULT CHARACTER SET utf8;
USE `lib_data`;

-- -----------------------------------------------------
-- Table `lib_data`.`publisher`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`publisher` (
  `publisher_id` INT          NOT NULL AUTO_INCREMENT,
  `publisher`    VARCHAR(255) NOT NULL,
  PRIMARY KEY (`publisher_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_language`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_language` (
  `language_id` INT          NOT NULL AUTO_INCREMENT,
  `language`    VARCHAR(255) NOT NULL,
  PRIMARY KEY (`language_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_details`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_details` (
  `isbn`          BIGINT         NOT NULL,
  `title`         VARCHAR(255)   NOT NULL,
  `publisher_id`  INT            NULL,
  `language_id`   INT            NULL,
  `edition`       INT            NULL,
  `year`          INT(4)         NULL,
  `pages`         INT            NULL,
  `price`         DECIMAL(10, 2) NULL,
  `time_modified` DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`isbn`),
  INDEX `fk_book_publisher1_idx` (`publisher_id` ASC),
  INDEX `fk_book_book_language1_idx` (`language_id` ASC),
  FULLTEXT `ft_book_idx` (`title`),
  CONSTRAINT `fk_book_publisher1`
  FOREIGN KEY (`publisher_id`)
  REFERENCES `lib_data`.`publisher` (`publisher_id`),
  CONSTRAINT `fk_book_book_language1`
  FOREIGN KEY (`language_id`)
  REFERENCES `lib_data`.`book_language` (`language_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`country`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`country` (
  `country_id` INT          NOT NULL AUTO_INCREMENT,
  `country`    VARCHAR(255) NOT NULL,
  PRIMARY KEY (`country_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`region`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`region` (
  `region_id`  INT          NOT NULL AUTO_INCREMENT,
  `region`     VARCHAR(255) NOT NULL,
  `country_id` INT          NOT NULL,
  PRIMARY KEY (`region_id`),
  INDEX `fk_region_country1_idx` (`country_id` ASC),
  CONSTRAINT `fk_region_country1`
  FOREIGN KEY (`country_id`)
  REFERENCES `lib_data`.`country` (`country_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`city`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`city` (
  `city_id`   INT          NOT NULL AUTO_INCREMENT,
  `city`      VARCHAR(255) NOT NULL,
  `region_id` INT          NOT NULL,
  PRIMARY KEY (`city_id`),
  INDEX `fk_city_region1_idx` (`region_id` ASC),
  CONSTRAINT `fk_city_region1`
  FOREIGN KEY (`region_id`)
  REFERENCES `lib_data`.`region` (`region_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`postal_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`postal_address` (
  `postal_address_id` INT          NOT NULL AUTO_INCREMENT,
  `postal_address`    VARCHAR(255) NOT NULL,
  PRIMARY KEY (`postal_address_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`postal_code`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`postal_code` (
  `postal_code_id`    INT NOT NULL AUTO_INCREMENT,
  `postal_code`       INT NOT NULL,
  `postal_address_id` INT NULL,
  PRIMARY KEY (`postal_code_id`),
  INDEX `fk_postal_code_city1_idx` (`postal_address_id` ASC),
  CONSTRAINT `fk_postal_code_city1`
  FOREIGN KEY (`postal_address_id`)
  REFERENCES `lib_data`.`postal_address` (`postal_address_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`address` (
  `address_id`     INT          NOT NULL AUTO_INCREMENT,
  `line_1`         VARCHAR(255) NOT NULL,
  `line_2`         VARCHAR(255) NULL,
  `city_id`        INT          NOT NULL,
  `postal_code_id` INT          NULL,
  `time_modified`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`address_id`),
  INDEX `fk_address_city1_idx` (`city_id` ASC),
  INDEX `fk_address_postal_code1_idx` (`postal_code_id` ASC),
  CONSTRAINT `fk_address_city1`
  FOREIGN KEY (`city_id`)
  REFERENCES `lib_data`.`city` (`city_id`),
  CONSTRAINT `fk_address_postal_code1`
  FOREIGN KEY (`postal_code_id`)
  REFERENCES `lib_data`.`postal_code` (`postal_code_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`library_branch`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`library_branch` (
  `library_branch_id` INT          NOT NULL AUTO_INCREMENT,
  `address_id`        INT          NOT NULL,
  `name`              VARCHAR(255) NOT NULL,
  PRIMARY KEY (`library_branch_id`),
  INDEX `fk_Library_Address1_idx` (`address_id` ASC),
  FULLTEXT `ft_library_idx` (`name`),
  CONSTRAINT `fk_Library_Address1`
  FOREIGN KEY (`address_id`)
  REFERENCES `lib_data`.`address` (`address_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`gender`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`gender` (
  `gender_id` INT          NOT NULL AUTO_INCREMENT,
  `gender`    VARCHAR(255) NOT NULL,
  PRIMARY KEY (`gender_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`user` (
  `user_id`       INT          NOT NULL AUTO_INCREMENT,
  `user_name`     VARCHAR(255) NOT NULL,
  `password`      VARCHAR(255) NOT NULL,
  `first_name`    VARCHAR(255) NOT NULL,
  `last_name`     VARCHAR(255) NOT NULL,
  `email`         VARCHAR(255) NOT NULL,
  `address_id`    INT          NULL,
  `gender_id`     INT          NULL,
  `create_time`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_modified` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  INDEX `fk_Publisher_Address1_idx` (`address_id` ASC),
  INDEX `fk_user_gender1_idx` (`gender_id` ASC),
  FULLTEXT `ft_user_idx` (`user_name`, `first_name`, `last_name`, `email`),
  CONSTRAINT `fk_Publisher_Address1`
  FOREIGN KEY (`address_id`)
  REFERENCES `lib_data`.`address` (`address_id`),
  CONSTRAINT `fk_user_gender1`
  FOREIGN KEY (`gender_id`)
  REFERENCES `lib_data`.`gender` (`gender_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`phone_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`phone_type` (
  `phone_type`  INT          NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`phone_type`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`phone`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`phone` (
  `user_id`    INT         NOT NULL,
  `phone`      VARCHAR(45) NOT NULL,
  `phone_type` INT         NOT NULL,
  PRIMARY KEY (`user_id`, `phone`),
  INDEX `fk_phone_phone_type1_idx` (`phone_type` ASC),
  CONSTRAINT `fk_phone_user1`
  FOREIGN KEY (`user_id`)
  REFERENCES `lib_data`.`user` (`user_id`),
  CONSTRAINT `fk_phone_phone_type1`
  FOREIGN KEY (`phone_type`)
  REFERENCES `lib_data`.`phone_type` (`phone_type`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`role` (
  `role_id` INT          NOT NULL AUTO_INCREMENT,
  `role`    VARCHAR(255) NOT NULL,
  PRIMARY KEY (`role_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_series`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_series` (
  `book_series_id` INT          NOT NULL AUTO_INCREMENT,
  `series_name`    VARCHAR(255) NOT NULL,
  `time_modified`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`book_series_id`),
  FULLTEXT `ft_book_series_idx` (`series_name`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_genre`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_genre` (
  `genre_id` INT          NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(255) NOT NULL,
  PRIMARY KEY (`genre_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`role_assignment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`role_assignment` (
  `user_user_id` INT NOT NULL,
  `role_role_id` INT NOT NULL,
  PRIMARY KEY (`user_user_id`, `role_role_id`),
  INDEX `fk_user_has_role_role1_idx` (`role_role_id` ASC),
  INDEX `fk_user_has_role_user1_idx` (`user_user_id` ASC),
  CONSTRAINT `fk_user_has_role_user1`
  FOREIGN KEY (`user_user_id`)
  REFERENCES `lib_data`.`user` (`user_id`),
  CONSTRAINT `fk_user_has_role_role1`
  FOREIGN KEY (`role_role_id`)
  REFERENCES `lib_data`.`role` (`role_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book` (
  `book_id`           INT    NOT NULL AUTO_INCREMENT,
  `isbn`              BIGINT NOT NULL,
  `library_branch_id` INT    NOT NULL,
  PRIMARY KEY (`book_id`),
  INDEX `fk_book_library_branch1_idx` (`library_branch_id` ASC),
  INDEX `fk_book_isbn1_idx` (`isbn` ASC),
  CONSTRAINT `fk_book_library_branch1`
  FOREIGN KEY (`library_branch_id`)
  REFERENCES `lib_data`.`library_branch` (`library_branch_id`),
  CONSTRAINT `fk_book_details1`
  FOREIGN KEY (`isbn`)
  REFERENCES `lib_data`.`book_details` (`isbn`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_loan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_loan` (
  `book_loan_id`  INT      NOT NULL AUTO_INCREMENT,
  `book_id`       INT      NOT NULL,
  `user_id`       INT      NOT NULL,
  `loan_date`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date`      DATETIME NOT NULL,
  `time_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`book_loan_id`),
  INDEX `fk_book_loan_book1_idx` (`book_id` ASC),
  INDEX `fk_book_loan_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_book_loan_book1`
  FOREIGN KEY (`book_id`)
  REFERENCES `lib_data`.`book` (`book_id`),
  CONSTRAINT `fk_book_loan_user1`
  FOREIGN KEY (`user_id`)
  REFERENCES `lib_data`.`user` (`user_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`library_branch_staff_assignment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`library_branch_staff_assignment` (
  `user_user_id`                     INT NOT NULL,
  `library_branch_library_branch_id` INT NOT NULL,
  PRIMARY KEY (`user_user_id`, `library_branch_library_branch_id`),
  INDEX `fk_user_has_branch_branch1_idx` (`library_branch_library_branch_id` ASC),
  INDEX `fk_user_has_branch_user1_idx` (`user_user_id` ASC),
  CONSTRAINT `fk_user_has_branch_user1`
  FOREIGN KEY (`user_user_id`)
  REFERENCES `lib_data`.`user` (`user_id`),
  CONSTRAINT `fk_user_has_branch_branch1`
  FOREIGN KEY (`library_branch_library_branch_id`)
  REFERENCES `lib_data`.`library_branch` (`library_branch_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_return`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_return` (
  `book_return_id` INT      NOT NULL AUTO_INCREMENT,
  `book_loan_id`   INT      NOT NULL,
  `return_date`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`book_return_id`),
  INDEX `fk_book_return_book_loan1_idx` (`book_loan_id` ASC),
  CONSTRAINT `fk_book_return_book_loan1`
  FOREIGN KEY (`book_loan_id`)
  REFERENCES `lib_data`.`book_loan` (`book_loan_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`author`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`author` (
  `author_id`  INT          NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name`  VARCHAR(255) NOT NULL,
  PRIMARY KEY (`author_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`ebook`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`ebook` (
  `ebook_id` INT NOT NULL AUTO_INCREMENT,
  `book_id`  INT NULL,
  PRIMARY KEY (`ebook_id`),
  INDEX `fk_ebook_book1_idx` (`book_id` ASC),
  CONSTRAINT `fk_ebook_book1`
  FOREIGN KEY (`book_id`)
  REFERENCES `lib_data`.`book` (`book_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`waiting_list`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`waiting_list` (
  `waiting_list_id`   INT    NOT NULL AUTO_INCREMENT,
  `isbn`              BIGINT NOT NULL,
  `library_branch_id` INT    NOT NULL,
  PRIMARY KEY (`waiting_list_id`),
  INDEX `fk_waiting_list_library_branch1_idx` (`library_branch_id` ASC),
  INDEX `fk_waiting_list_book_details1_idx` (`isbn` ASC),
  CONSTRAINT `fk_waiting_list_library_branch1`
  FOREIGN KEY (`library_branch_id`)
  REFERENCES `lib_data`.`library_branch` (`library_branch_id`),
  CONSTRAINT `fk_waiting_list_book_details1`
  FOREIGN KEY (`isbn`)
  REFERENCES `lib_data`.`book_details` (`isbn`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`waiting_list_line`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`waiting_list_line` (
  `waiting_list_waiting_list_id` INT NOT NULL,
  `user_user_id`                 INT NOT NULL,
  `subscription_date`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`waiting_list_waiting_list_id`, `user_user_id`),
  INDEX `fk_waiting_list_has_user_user1_idx` (`user_user_id` ASC),
  INDEX `fk_waiting_list_has_user_waiting_list1_idx` (`waiting_list_waiting_list_id` ASC),
  CONSTRAINT `fk_waiting_list_has_user_waiting_list1`
  FOREIGN KEY (`waiting_list_waiting_list_id`)
  REFERENCES `lib_data`.`waiting_list` (`waiting_list_id`),
  CONSTRAINT `fk_waiting_list_has_user_user1`
  FOREIGN KEY (`user_user_id`)
  REFERENCES `lib_data`.`user` (`user_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_genre_assignment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_genre_assignment` (
  `book_details_isbn`   BIGINT NOT NULL,
  `book_genre_genre_id` INT    NOT NULL,
  PRIMARY KEY (`book_details_isbn`, `book_genre_genre_id`),
  INDEX `fk_book_details_has_book_genre_book_genre1_idx` (`book_genre_genre_id` ASC),
  INDEX `fk_book_details_has_book_genre_book_details1_idx` (`book_details_isbn` ASC),
  CONSTRAINT `fk_book_details_has_book_genre_book_details1`
  FOREIGN KEY (`book_details_isbn`)
  REFERENCES `lib_data`.`book_details` (`isbn`),
  CONSTRAINT `fk_book_details_has_book_genre_book_genre1`
  FOREIGN KEY (`book_genre_genre_id`)
  REFERENCES `lib_data`.`book_genre` (`genre_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`book_series_assignment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`book_series_assignment` (
  `book_details_isbn`          BIGINT NOT NULL,
  `book_series_book_series_id` INT    NOT NULL,
  PRIMARY KEY (`book_details_isbn`, `book_series_book_series_id`),
  INDEX `fk_book_details_has_book_series_book_series1_idx` (`book_series_book_series_id` ASC),
  INDEX `fk_book_details_has_book_series_book_details1_idx` (`book_details_isbn` ASC),
  CONSTRAINT `fk_book_details_has_book_series_book_details1`
  FOREIGN KEY (`book_details_isbn`)
  REFERENCES `lib_data`.`book_details` (`isbn`),
  CONSTRAINT `fk_book_details_has_book_series_book_series1`
  FOREIGN KEY (`book_series_book_series_id`)
  REFERENCES `lib_data`.`book_series` (`book_series_id`)
)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `lib_data`.`author_list`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lib_data`.`author_list` (
  `book_details_isbn` BIGINT NOT NULL,
  `author_author_id`  INT    NOT NULL,
  PRIMARY KEY (`book_details_isbn`, `author_author_id`),
  INDEX `fk_book_details_has_author_author1_idx` (`author_author_id` ASC),
  INDEX `fk_book_details_has_author_book_details1_idx` (`book_details_isbn` ASC),
  CONSTRAINT `fk_book_details_has_author_book_details1`
  FOREIGN KEY (`book_details_isbn`)
  REFERENCES `lib_data`.`book_details` (`isbn`),
  CONSTRAINT `fk_book_details_has_author_author1`
  FOREIGN KEY (`author_author_id`)
  REFERENCES `lib_data`.`author` (`author_id`)
)
  ENGINE = InnoDB;


SET SQL_MODE = @OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;
