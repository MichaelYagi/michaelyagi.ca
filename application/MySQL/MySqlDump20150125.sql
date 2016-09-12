CREATE DATABASE  IF NOT EXISTS `myagi` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `myagi`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: myagi
-- ------------------------------------------------------
-- Server version	5.5.37-0+wheezy1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog`
--

DROP TABLE IF EXISTS `blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `post` text NOT NULL,
  `created` date NOT NULL,
  `modified` date NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `created` date NOT NULL,
  `markedread` int(11) NOT NULL,
  `blogid` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ingredient`
--

DROP TABLE IF EXISTS `ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingredient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `amount` varchar(20) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `ingredient` varchar(100) NOT NULL,
  `added` date NOT NULL,
  `modified` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_id_index` (`recipe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1177 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recipe`
--

DROP TABLE IF EXISTS `recipe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `prep_time` time DEFAULT NULL,
  `cook_time` time DEFAULT NULL,
  `serves` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `added` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `published` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recipe_image`
--

DROP TABLE IF EXISTS `recipe_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipe_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(10) DEFAULT NULL,
  `recipe_id` int(11) NOT NULL,
  `added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recipe_x_tag`
--

DROP TABLE IF EXISTS `recipe_x_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipe_x_tag` (
  `recipe_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  KEY `tag_id_index` (`recipe_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `step`
--

DROP TABLE IF EXISTS `step`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `step` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `description` text NOT NULL,
  `added` date NOT NULL,
  `modified` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_id_index` (`recipe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=953 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword_unique_constraint` (`keyword`),
  KEY `recipe_id_index` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `suspended` tinyint(1) NOT NULL,
  `added` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_email`
--

DROP TABLE IF EXISTS `user_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'myagi'
--
/*!50003 DROP PROCEDURE IF EXISTS `commonCreateUserSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonCreateUserSP`(IN `n_user` VARCHAR(100), IN `n_hash` VARCHAR(100), IN `n_email` VARCHAR(100))
    NO SQL
BEGIN
	DECLARE username_count INT;
	DECLARE email_count INT;
	DECLARE uid INT DEFAULT 0;
	DECLARE ret_val INT;

	SET username_count = (SELECT COUNT(*) FROM user WHERE username = n_user);
	SET email_count = (SELECT COUNT(*) FROM user_email WHERE email = n_email);

	IF (username_count > 0 OR email_count > 0) THEN
		SET ret_val = 0;
	ELSE
		INSERT INTO user (username,hash,added,modified) VALUES (n_user,n_hash,NOW(),NOW());
		SET uid = (SELECT LAST_INSERT_ID());
        INSERT INTO user_email (user_id,email) VALUES (uid,n_email);
		SET ret_val = uid;
	END IF;

	SELECT ret_val;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonGetAllUserInfoSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonGetAllUserInfoSP`(IN `u_order` VARCHAR(20))
    NO SQL
BEGIN
	IF u_order = "user" THEN
		SELECT * FROM user ORDER BY username ASC;
	ELSE
		SELECT * FROM user ORDER BY suspended ASC;
	END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonGetUserIdEmailSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonGetUserIdEmailSP`(IN `user_email` VARCHAR(100))
    NO SQL
BEGIN
	DECLARE username_count INT;
	DECLARE email_count INT;

	SET username_count = (SELECT COUNT(*) FROM user 
                          WHERE 
                          username = user_email);

	SET email_count = (SELECT COUNT(*) FROM user u 
                       INNER JOIN user_email ue ON ue.user_id = u.id
                       WHERE 
                       ue.email = user_email);

	IF username_count = 1 THEN
		SELECT u.id as userid,ue.email as email FROM user u
		INNER JOIN user_email ue ON ue.user_id = u.id
		WHERE u.username = user_email;
	ELSEIF email_count = 1 THEN
		SELECT u.id as userid,ue.email as email FROM user u 
		INNER JOIN user_email ue ON ue.user_id = u.id 
		WHERE ue.email = user_email;
	ELSE
		SELECT 0 as userid,null as email;
	END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonGetUserIdSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonGetUserIdSP`(IN `user_email` VARCHAR(100))
    NO SQL
BEGIN
	DECLARE username_count INT;
	DECLARE email_count INT;

	SET username_count = (SELECT COUNT(*) FROM user 
                          WHERE 
                          username = user_email);

	SET email_count = (SELECT COUNT(*) FROM user u 
                       INNER JOIN user_email ue ON ue.user_id = u.id
                       WHERE 
                       ue.email = user_email);

	IF username_count = 1 THEN
		SELECT id as userid FROM user WHERE username = user_email;
	ELSEIF email_count = 1 THEN
		SELECT u.id as userid FROM user u 
		INNER JOIN user_email ue ON ue.user_id = u.id 
		WHERE ue.email = user_email;
	ELSE
		SELECT 0 as userid;
	END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonGetUserInfoByIdSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonGetUserInfoByIdSP`(IN `uid` INT)
    NO SQL
BEGIN
	SELECT *,ue.email FROM user u
	INNER JOIN user_email ue ON ue.user_id = u.id
	WHERE u.id = uid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonGetUserInfoByUserSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonGetUserInfoByUserSP`(IN `user_email` VARCHAR(100))
    NO SQL
BEGIN
	DECLARE username_count INT;
	DECLARE email_count INT;

	SET username_count = (SELECT COUNT(*) FROM user 
                          WHERE 
                          username = user_email);

	SET email_count = (SELECT COUNT(*) FROM user u 
                       INNER JOIN user_email ue ON ue.user_id = u.id
                       WHERE 
                       ue.email = user_email);

	IF username_count = 1 THEN
		SELECT hash,suspended FROM user WHERE username = user_email;
	ELSEIF email_count = 1 THEN
		SELECT u.hash,u.suspended as hash FROM user u 
		INNER JOIN user_email ue ON ue.user_id = u.id 
		WHERE ue.email = user_email;
	ELSE
		SELECT NULL as hash,NULL as suspended;
	END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonSetUserEmailSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonSetUserEmailSP`(IN `uid` INT, IN `new_email` VARCHAR(100))
    NO SQL
BEGIN
	DECLARE email_count INT;
	DECLARE ret_val INT DEFAULT 0;

	SET email_count = (SELECT COUNT(*) FROM user_email ue 
                       WHERE ue.email = new_email AND
                          	 ue.user_id != uid);

	IF email_count = 0 THEN
		UPDATE user_email SET email = new_email WHERE user_id = uid;
		SET ret_val = 1;
	END IF;

	SELECT ret_val;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonSetUserHashSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonSetUserHashSP`(IN `uid` INT, IN `u_hash` VARCHAR(100))
    NO SQL
BEGIN
	DECLARE retVal INT;

	SET retVal = 0;

	UPDATE user SET hash = u_hash,modified = NOW() WHERE id = uid;

	SET retVal = 1;

	SELECT retVal,(SELECT email FROM user_email WHERE user_id = uid) as email;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `commonSetUserStatusSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `commonSetUserStatusSP`(IN `uid_list` VARCHAR(100), IN `u_status` INT)
    NO SQL
BEGIN

	DECLARE user_count INT;
	DECLARE ret_val INT;
	SET ret_val = 0;

	SET user_count = (SELECT COUNT(*) FROM user WHERE FIND_IN_SET(id, uid_list));

	IF user_count > 0 THEN
		UPDATE user SET suspended = u_status,modified = NOW() WHERE FIND_IN_SET(id, uid_list);
		SET ret_val = 1;
	END IF;

	SELECT ret_val;
	
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeCreateImageIdSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeCreateImageIdSP`(IN `rid` INT, IN `ext` VARCHAR(10))
    NO SQL
BEGIN
	DECLARE iid INT DEFAULT 0;

	INSERT INTO recipe_image (recipe_id,extension,added) VALUES (rid,ext,NOW());

	SET iid = (SELECT LAST_INSERT_ID());
    SELECT iid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeCreateIngredientSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeCreateIngredientSP`(IN `rid` INT, IN `s_order` INT, IN `i_amount` VARCHAR(20), IN `i_unit` VARCHAR(20), IN `i_ing` VARCHAR(100))
    NO SQL
BEGIN
    INSERT INTO ingredient (recipe_id,sort_order,amount,unit,ingredient,added,modified)
    VALUES (rid,s_order,i_amount,i_unit,i_ing,NOW(),NOW());
    SELECT * FROM ingredient WHERE id = (SELECT LAST_INSERT_ID());
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeCreateRecipeSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeCreateRecipeSP`(IN `r_title` VARCHAR(100), IN `r_user` VARCHAR(100), IN `r_user_id` INT, IN `r_prep` TIME, IN `r_cook` TIME, IN `r_serves` INT, IN `r_published` TINYINT(1))
BEGIN
    DECLARE rid INT DEFAULT 0;
    INSERT INTO recipe (title,user,user_id,prep_time,cook_time,serves,added,modified,published) VALUES (r_title,r_user,r_user_id,r_prep,r_cook,r_serves,NOW(),NOW(),r_published);
    SET rid = (SELECT LAST_INSERT_ID());
    SELECT rid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeCreateStepSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeCreateStepSP`(IN `rid` INT, IN `s_order` INT, IN `s_desc` TEXT)
    NO SQL
BEGIN
    INSERT INTO step (recipe_id,sort_order,description,added,modified)
    VALUES (rid,s_order,s_desc,NOW(),NOW());
    SELECT * FROM step WHERE id = (SELECT LAST_INSERT_ID());
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeCreateTagSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeCreateTagSP`(IN `rid` INT, IN `r_keyword` VARCHAR(100))
    NO SQL
BEGIN
	DECLARE tag_count INT DEFAULT 0;
	DECLARE tid INT DEFAULT 0;

	SET tag_count = (SELECT COUNT(*) FROM tag WHERE keyword = LOWER(r_keyword));

	IF tag_count = 1 THEN
		SET tid = (SELECT id FROM tag WHERE keyword = LOWER(r_keyword));
	ELSE
		INSERT INTO tag (keyword) VALUES (LOWER(r_keyword));
		SET tid = (SELECT LAST_INSERT_ID());
	END IF;	

	INSERT INTO recipe_x_tag (recipe_id,tag_id) VALUES (rid,tid);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeDeleteImageSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeDeleteImageSP`(IN `iid` INT)
    NO SQL
BEGIN
	DELETE FROM recipe_image WHERE id = iid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeDeleteIngredientSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeDeleteIngredientSP`(IN `rid` INT)
    NO SQL
BEGIN

	DECLARE check_exists INT;
	DECLARE retval INT; 

	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        ROLLBACK;
    END;

	SET check_exists = 0; 

	SELECT COUNT(*) INTO check_exists FROM ingredient WHERE recipe_id = rid;

	IF (check_exists > 0) THEN 
		START TRANSACTION;

		DELETE FROM ingredient WHERE recipe_id = rid;

		COMMIT;

		SET retval = 1;
	ELSE
		SET retval = 0;
	END IF;

	SELECT retval;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeDeleteRecipeSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeDeleteRecipeSP`(IN `rid` INT)
    NO SQL
BEGIN

	DECLARE check_exists INT;
	DECLARE retval INT; 

	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        ROLLBACK;
    END;

	SET check_exists = 0; 

	SELECT COUNT(*) INTO check_exists FROM recipe WHERE id = rid;

	IF (check_exists > 0) THEN 
		START TRANSACTION;

		DELETE FROM recipe WHERE id = rid;
		DELETE FROM ingredient WHERE recipe_id = rid;
		DELETE FROM step WHERE recipe_id = rid;
		DELETE FROM recipe_x_tag WHERE recipe_id = rid;
		DELETE FROM recipe_image WHERE recipe_id = rid;

		COMMIT;

		SET retval = 1;
	ELSE
		SET retval = 0;
	END IF;

	SELECT retval;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeDeleteRecipesSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeDeleteRecipesSP`(IN `rid_list` VARCHAR(100))
    NO SQL
BEGIN

	DECLARE check_exists INT;
	DECLARE retval INT; 

	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        ROLLBACK;
    END;

	START TRANSACTION;

	DELETE FROM recipe WHERE FIND_IN_SET(id, rid_list);
	DELETE FROM ingredient WHERE FIND_IN_SET(recipe_id, rid_list);
	DELETE FROM step WHERE FIND_IN_SET(recipe_id, rid_list);
	DELETE FROM recipe_x_tag WHERE FIND_IN_SET(recipe_id, rid_list);
	DELETE FROM recipe_image WHERE FIND_IN_SET(recipe_id, rid_list);

	COMMIT;

	SET retval = 1;

	SELECT retval;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeDeleteStepSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeDeleteStepSP`(IN `rid` INT)
    NO SQL
BEGIN

	DECLARE check_exists INT;
	DECLARE retval INT; 

	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        ROLLBACK;
    END;

	SET check_exists = 0; 

	SELECT COUNT(*) INTO check_exists FROM step WHERE recipe_id = rid;

	IF (check_exists > 0) THEN 
		START TRANSACTION;

		DELETE FROM step WHERE recipe_id = rid;

		COMMIT;

		SET retval = 1;
	ELSE
		SET retval = 0;
	END IF;

	SELECT retval;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeDeleteTagSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeDeleteTagSP`(IN `rid` INT)
    NO SQL
BEGIN

	DECLARE check_exists INT;
	DECLARE retval INT; 

	DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        ROLLBACK;
    END;

	SET check_exists = 0; 

	SELECT COUNT(*) INTO check_exists FROM recipe_x_tag WHERE recipe_id = rid;

	IF (check_exists > 0) THEN 
		START TRANSACTION;

		DELETE FROM recipe_x_tag WHERE recipe_id = rid;

		COMMIT;

		SET retval = 1;
	ELSE
		SET retval = 0;
	END IF;

	SELECT retval;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetAllRecipesSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetAllRecipesSP`()
    NO SQL
SELECT DISTINCT r.id,r.user,r.title,r.serves,r.prep_time,r.published,r.cook_time,ri.id AS image_id,ri.extension
FROM recipe r
LEFT JOIN recipe_image ri ON ri.recipe_id = r.id
GROUP BY r.id,r.user,r.title */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetIngredientsSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetIngredientsSP`(IN `rid` INT)
    NO SQL
SELECT
i.sort_order,
i.amount,
i.unit,
i.ingredient
FROM ingredient i
WHERE i.recipe_id = rid
ORDER BY i.sort_order ASC */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetRecipeImageInfoSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetRecipeImageInfoSP`(IN `rid` INT)
    NO SQL
BEGIN
	SELECT id,extension,recipe_id FROM recipe_image WHERE recipe_id = rid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetRecipeImagesSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetRecipeImagesSP`(IN `rid` INT)
    NO SQL
BEGIN

	SELECT * FROM recipe_image ri WHERE ri.recipe_id = rid;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetRecipesByTypeSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetRecipesByTypeSP`(IN `type` VARCHAR(50), IN `value` VARCHAR(100))
    NO SQL
BEGIN

	DECLARE retval INT DEFAULT 0;

	CASE type
      	WHEN "user" THEN 
			SELECT DISTINCT 1 AS retval,r.id,r.user,r.title,r.serves,r.prep_time,r.cook_time,r.published,ri.id AS image_id,ri.extension
			FROM recipe r
			LEFT JOIN recipe_image ri ON ri.recipe_id = r.id
			WHERE r.user = value
			GROUP BY r.id,r.user,r.title;
      	WHEN "tag" THEN 
			SELECT DISTINCT 1 AS retval,r.id,r.user,r.title,r.serves,r.prep_time,r.cook_time,r.published,ri.id AS image_id,ri.extension 
			FROM recipe r
			INNER JOIN recipe_x_tag rxt	ON rxt.recipe_id = r.id
			INNER JOIN tag t 			ON t.id = rxt.tag_id
			LEFT JOIN recipe_image ri 	ON ri.recipe_id = r.id
			WHERE t.keyword = value
			GROUP BY r.id,r.user,r.title;
		WHEN "search" THEN
			SELECT DISTINCT 1 AS retval,r.id,r.user,r.title,r.serves,r.prep_time,r.cook_time,r.published,ri.id AS image_id,ri.extension
			FROM recipe r
			LEFT JOIN recipe_x_tag rxt 	ON rxt.recipe_id = r.id
			LEFT JOIN tag t				ON t.id = rxt.tag_id
			LEFT JOIN recipe_image ri 	ON ri.recipe_id = r.id
			INNER JOIN ingredient i		ON i.recipe_id = r.id
			INNER JOIN step s			ON s.recipe_id = r.id
			WHERE 
			t.keyword LIKE CONCAT('%',value,'%') OR
			r.title LIKE CONCAT('%',value,'%') OR
			i.ingredient LIKE CONCAT('%',value,'%') OR
			s.description LIKE CONCAT('%',value,'%')
			GROUP BY 
			r.id,r.user,r.title
			ORDER BY
			t.keyword,r.title,i.ingredient,s.description;
      	ELSE
        	BEGIN
				SET retval = 0;
				SELECT retval;
        	END;
    END CASE;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetRecipeSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetRecipeSP`(IN `rid` INT)
    NO SQL
SELECT 
*
FROM recipe AS r
WHERE
r.id = rid */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetStepsSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetStepsSP`(IN `rid` INT)
    NO SQL
SELECT 
s.sort_order,
s.description
FROM step s
WHERE
s.recipe_id = rid
ORDER BY s.sort_order ASC */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeGetTagsSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeGetTagsSP`(IN `rid` INT)
    NO SQL
SELECT 
t.keyword
FROM tag t
INNER JOIN recipe_x_tag AS rxt ON rxt.tag_id = t.id
WHERE
rxt.recipe_id = rid */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeSearhTagsSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeSearhTagsSP`(IN `r_search` VARCHAR(100))
    NO SQL
SELECT DISTINCT
t.keyword
FROM tag t
WHERE
t.keyword LIKE CONCAT('%',r_search,'%') */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeUpdatePublishRecipeSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeUpdatePublishRecipeSP`(IN `rid` INT, IN `r_publish` TINYINT(1))
BEGIN

        DECLARE check_exists INT;
        DECLARE retval INT;

        SET check_exists = 0;

        SELECT COUNT(*) INTO check_exists FROM recipe WHERE id = rid;

        IF (check_exists > 0) THEN

                UPDATE recipe
                SET published = r_publish
                WHERE id = rid;
                SET retval = 1;

        ELSE
                SET retval = 0;
        END IF;

        SELECT retval;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recipeUpdateRecipeSP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `recipeUpdateRecipeSP`(IN `rid` INT, IN `r_title` VARCHAR(100), IN `r_user` VARCHAR(100), IN `r_user_id` INT, IN `r_prep` TIME, IN `r_cook` TIME, IN `r_serves` INT, IN `r_published` TINYINT(1))
    NO SQL
BEGIN 

	DECLARE check_exists INT;
	DECLARE retval INT; 

	SET check_exists = 0; 

	SELECT COUNT(*) INTO check_exists FROM recipe WHERE id = rid;

	IF (check_exists > 0) THEN 

		UPDATE recipe 
		SET title = r_title,
			user = r_user,
			user_id = r_user_id,
			published = r_published
		WHERE id = rid;

		SET retval = 1;

		IF (r_prep IS NOT NULL) THEN
			UPDATE recipe SET prep_time = r_prep WHERE id = rid;
		END IF;

		IF (r_cook IS NOT NULL) THEN
			UPDATE recipe SET cook_time = r_cook WHERE id = rid;
		END IF;

		IF (r_serves IS NOT NULL) THEN
			UPDATE recipe SET serves = r_serves WHERE id = rid;
		END IF;
		
	ELSE

		SET retval = 0;

	END IF;



	SELECT retval;

	

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-25  0:29:38
