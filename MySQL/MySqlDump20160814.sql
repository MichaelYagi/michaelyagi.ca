-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 23, 2016 at 04:40 AM
-- Server version: 5.5.44-0+deb8u1
-- PHP Version: 5.6.24-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `myagi`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `commonCreateUserSP`(IN `n_user` VARCHAR(100), IN `n_hash` VARCHAR(100), IN `n_email` VARCHAR(100))
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonGetAllUserInfoSP`(IN `u_order` VARCHAR(20))
    NO SQL
BEGIN

	IF u_order = "user" THEN

		SELECT * FROM user ORDER BY username ASC;

	ELSE

		SELECT * FROM user ORDER BY suspended ASC;

	END IF;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonGetUserIdEmailSP`(IN `user_email` VARCHAR(100))
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonGetUserIdSP`(IN `user_email` VARCHAR(100))
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonGetUserInfoByIdSP`(IN `uid` INT)
    NO SQL
BEGIN

	SELECT *,ue.email FROM user u

	INNER JOIN user_email ue ON ue.user_id = u.id

	WHERE u.id = uid;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonGetUserInfoByUserSP`(IN `user_email` VARCHAR(100))
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonSetUserEmailSP`(IN `uid` INT, IN `new_email` VARCHAR(100))
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonSetUserHashSP`(IN `uid` INT, IN `u_hash` VARCHAR(100))
    NO SQL
BEGIN

	DECLARE retVal INT;



	SET retVal = 0;



	UPDATE user SET hash = u_hash,modified = NOW() WHERE id = uid;



	SET retVal = 1;



	SELECT retVal,(SELECT email FROM user_email WHERE user_id = uid) as email;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `commonSetUserStatusSP`(IN `uid_list` VARCHAR(100), IN `u_status` INT)
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

	

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeCreateImageIdSP`(IN `rid` INT, IN `ext` VARCHAR(10))
    NO SQL
BEGIN

	DECLARE iid INT DEFAULT 0;



	INSERT INTO recipe_image (recipe_id,extension,added) VALUES (rid,ext,NOW());



	SET iid = (SELECT LAST_INSERT_ID());

    SELECT iid;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeCreateIngredientSP`(IN `rid` INT, IN `s_order` INT, IN `i_amount` VARCHAR(20), IN `i_unit` VARCHAR(20), IN `i_ing` VARCHAR(100))
    NO SQL
BEGIN

    INSERT INTO ingredient (recipe_id,sort_order,amount,unit,ingredient,added,modified)

    VALUES (rid,s_order,i_amount,i_unit,i_ing,NOW(),NOW());

    SELECT * FROM ingredient WHERE id = (SELECT LAST_INSERT_ID());

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeCreateRecipeSP`(IN `r_title` VARCHAR(100), IN `r_user` VARCHAR(100), IN `r_user_id` INT, IN `r_prep` TIME, IN `r_cook` TIME, IN `r_serves` INT, IN `r_published` TINYINT(1))
BEGIN
    DECLARE rid INT DEFAULT 0;
    INSERT INTO recipe (title,user,user_id,prep_time,cook_time,serves,added,modified,published) VALUES (r_title,r_user,r_user_id,r_prep,r_cook,r_serves,NOW(),NOW(),r_published);
    SET rid = (SELECT LAST_INSERT_ID());
    SELECT rid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeCreateStepSP`(IN `rid` INT, IN `s_order` INT, IN `s_desc` TEXT)
    NO SQL
BEGIN

    INSERT INTO step (recipe_id,sort_order,description,added,modified)

    VALUES (rid,s_order,s_desc,NOW(),NOW());

    SELECT * FROM step WHERE id = (SELECT LAST_INSERT_ID());

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeCreateTagSP`(IN `rid` INT, IN `r_keyword` VARCHAR(100))
    NO SQL
BEGIN

	DECLARE tag_count INT DEFAULT 0;

	DECLARE tid INT DEFAULT 0;
    
    DECLARE ret_val INT DEFAULT 0;



	SET tag_count = (SELECT COUNT(*) FROM tag WHERE keyword = LOWER(r_keyword));


	IF tag_count = 1 THEN

		SET tid = (SELECT id FROM tag WHERE keyword = LOWER(r_keyword));

	ELSE

		INSERT INTO tag (keyword) VALUES (LOWER(r_keyword));

		SET tid = (SELECT LAST_INSERT_ID());

	END IF;	


	INSERT INTO recipe_x_tag (recipe_id,tag_id) VALUES (rid,tid);
    
    SET ret_val = 1;
    
    SELECT ret_val;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeDeleteImageSP`(IN `iid` INT)
    NO SQL
BEGIN

	DELETE FROM recipe_image WHERE id = iid;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeDeleteIngredientSP`(IN `rid` INT)
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeDeleteRecipeSP`(IN `rid` INT)
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeDeleteRecipesSP`(IN `rid_list` VARCHAR(100))
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeDeleteStepSP`(IN `rid` INT)
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeDeleteTagSP`(IN `rid` INT)
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetAllRecipesSP`()
    NO SQL
SELECT DISTINCT r.id,r.user,r.title,r.serves,r.prep_time,r.published,r.cook_time,ri.id AS image_id,ri.extension
FROM recipe r
LEFT JOIN recipe_image ri ON ri.recipe_id = r.id
GROUP BY r.id,r.user,r.title$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetIngredientsSP`(IN `rid` INT)
    NO SQL
SELECT

i.sort_order,

i.amount,

i.unit,

i.ingredient

FROM ingredient i

WHERE i.recipe_id = rid

ORDER BY i.sort_order ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetRecipeImageInfoSP`(IN `rid` INT)
    NO SQL
BEGIN

	SELECT id,extension,recipe_id FROM recipe_image WHERE recipe_id = rid;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetRecipeImagesSP`(IN `rid` INT)
    NO SQL
BEGIN



	SELECT * FROM recipe_image ri WHERE ri.recipe_id = rid;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetRecipesByTypeSP`(IN `type` VARCHAR(50), IN `value` VARCHAR(100))
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



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetRecipeSP`(IN `rid` INT)
    NO SQL
SELECT 

*

FROM recipe AS r

WHERE

r.id = rid$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetStepsSP`(IN `rid` INT)
    NO SQL
SELECT 

s.sort_order,

s.description

FROM step s

WHERE

s.recipe_id = rid

ORDER BY s.sort_order ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeGetTagsSP`(IN `rid` INT)
    NO SQL
SELECT 

t.keyword

FROM tag t

INNER JOIN recipe_x_tag AS rxt ON rxt.tag_id = t.id

WHERE

rxt.recipe_id = rid$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeSearhTagsSP`(IN `r_search` VARCHAR(100))
    NO SQL
SELECT DISTINCT
t.keyword
FROM tag t
WHERE
t.keyword LIKE CONCAT('%',r_search,'%')$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeUpdatePublishRecipeSP`(IN `rid` INT, IN `r_publish` TINYINT(1))
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `recipeUpdateRecipeSP`(IN `rid` INT, IN `r_title` VARCHAR(100), IN `r_user` VARCHAR(100), IN `r_user_id` INT, IN `r_prep` TIME, IN `r_cook` TIME, IN `r_serves` INT, IN `r_published` TINYINT(1))
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

	

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`) VALUES
('myagi', 'c3d7ea6a0340dff0f81834567100eb15d52d6ef6');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE IF NOT EXISTS `blog` (
`id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `post` text NOT NULL,
  `created` date NOT NULL,
  `modified` date NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `created` date NOT NULL,
  `markedread` int(11) NOT NULL,
  `blogid` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ingredient`
--

CREATE TABLE IF NOT EXISTS `ingredient` (
`id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `amount` varchar(20) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `ingredient` varchar(100) NOT NULL,
  `added` date NOT NULL,
  `modified` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1221 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ingredient`
--

INSERT INTO `ingredient` (`id`, `recipe_id`, `sort_order`, `amount`, `unit`, `ingredient`, `added`, `modified`) VALUES
(1195, 155, 1, '1', 'cup', 'flour', '2016-02-21', '2016-02-21'),
(1196, 155, 2, '1', 'can', 'sliced pineapples', '2016-02-21', '2016-02-21'),
(1198, 157, 1, '1', 'test', 'ingredient', '2016-02-22', '2016-02-22'),
(1220, 154, 1, '5', 'TBS', 'salt', '2016-02-23', '2016-02-23');

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE IF NOT EXISTS `recipe` (
`id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `prep_time` time DEFAULT NULL,
  `cook_time` time DEFAULT NULL,
  `serves` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `added` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `published` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`id`, `title`, `prep_time`, `cook_time`, `serves`, `user_id`, `user`, `added`, `modified`, `published`) VALUES
(154, 'test recipe update', '00:01:00', '05:00:00', 7, 24, 'myagi', '2016-02-21 06:53:56', '2016-02-21 06:53:56', 1),
(155, 'Pineapple Upsidedown Cake', '02:00:00', '01:45:00', 12, 24, 'myagi', '2016-02-21 07:26:43', '2016-02-21 07:26:43', 1),
(157, 'test', '12:00:00', '12:34:00', 1, 25, 'test', '2016-02-22 07:02:47', '2016-02-22 07:02:47', 1);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_image`
--

CREATE TABLE IF NOT EXISTS `recipe_image` (
`id` int(11) NOT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `recipe_id` int(11) NOT NULL,
  `added` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recipe_image`
--

INSERT INTO `recipe_image` (`id`, `extension`, `recipe_id`, `added`) VALUES
(89, 'jpg', 154, '2016-02-21 07:21:41');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_x_tag`
--

CREATE TABLE IF NOT EXISTS `recipe_x_tag` (
  `recipe_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recipe_x_tag`
--

INSERT INTO `recipe_x_tag` (`recipe_id`, `tag_id`) VALUES
(154, 31),
(155, 29),
(157, 30);

-- --------------------------------------------------------

--
-- Table structure for table `step`
--

CREATE TABLE IF NOT EXISTS `step` (
`id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `description` text NOT NULL,
  `added` date NOT NULL,
  `modified` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1009 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `step`
--

INSERT INTO `step` (`id`, `recipe_id`, `sort_order`, `description`, `added`, `modified`) VALUES
(971, 155, 1, 'Open can', '2016-02-21', '2016-02-21'),
(972, 155, 2, 'Mix ingredients', '2016-02-21', '2016-02-21'),
(974, 157, 1, 'test step', '2016-02-22', '2016-02-22'),
(1007, 154, 1, 'step 1', '2016-02-23', '2016-02-23'),
(1008, 154, 2, 'step 2 - profit', '2016-02-23', '2016-02-23');

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
`id` int(11) NOT NULL,
  `keyword` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `keyword`) VALUES
(29, 'dessert'),
(28, 'ham'),
(31, 'salty'),
(30, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `suspended` tinyint(1) NOT NULL,
  `added` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `hash`, `suspended`, `added`, `modified`) VALUES
(24, 'myagi', '$2a$10$pACA2SsH4Ph6xDUDrHQiDe.rpYkB2uNOxgyoio3hVJW3arrXiitRe', 0, '2016-02-21 06:45:26', '2016-02-23 04:00:04'),
(25, 'test', '$2a$10$oOzkyqW6c36yswDZ6Ebu2esNZLqg4NorC1vl1j.TB9LHghDb1dSXO', 0, '2016-02-22 07:00:58', '2016-02-22 07:00:58'),
(27, 'asdf', '$2a$10$KQUuHeN3h3bFRR.NfaUYJuEpqr8HVrlU90oIKK.85q87VzXvwTZO.', 0, '2016-02-23 01:25:50', '2016-02-23 02:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `user_email`
--

CREATE TABLE IF NOT EXISTS `user_email` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_email`
--

INSERT INTO `user_email` (`id`, `user_id`, `email`) VALUES
(24, 24, 'michaeltyagi@gmail.com'),
(25, 25, 'test@test.com'),
(27, 27, 'qwer@qwer.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingredient`
--
ALTER TABLE `ingredient`
 ADD PRIMARY KEY (`id`), ADD KEY `recipe_id_index` (`recipe_id`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe_image`
--
ALTER TABLE `recipe_image`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe_x_tag`
--
ALTER TABLE `recipe_x_tag`
 ADD KEY `tag_id_index` (`recipe_id`,`tag_id`);

--
-- Indexes for table `step`
--
ALTER TABLE `step`
 ADD PRIMARY KEY (`id`), ADD KEY `recipe_id_index` (`recipe_id`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `keyword_unique_constraint` (`keyword`), ADD KEY `recipe_id_index` (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_email`
--
ALTER TABLE `user_email`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ingredient`
--
ALTER TABLE `ingredient`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1221;
--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=158;
--
-- AUTO_INCREMENT for table `recipe_image`
--
ALTER TABLE `recipe_image`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=90;
--
-- AUTO_INCREMENT for table `step`
--
ALTER TABLE `step`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1009;
--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `user_email`
--
ALTER TABLE `user_email`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
