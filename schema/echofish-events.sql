/* $Id$ */

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8 COLLATE 'utf8_unicode_ci';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
DELIMITER //

DROP EVENT IF EXISTS e_populate_whitelist_mem//
CREATE EVENT e_populate_whitelist_mem ON SCHEDULE EVERY 1 SECOND DO 
BEGIN
IF (SELECT COUNT(*) FROM whitelist_mem)<1 THEN
  IF (SELECT COUNT(*) FROM whitelist)>0 THEN
    INSERT INTO whitelist_mem  SELECT id,host,facility,level,program,pattern from whitelist;
  END IF;
END IF;
END
//

DROP EVENT IF EXISTS e_archive_parse_unparsed//
CREATE EVENT e_archive_parse_unparsed
ON SCHEDULE EVERY 10 SECOND COMMENT 'PROCESS ' DO
BEGIN
  ALTER EVENT e_archive_parse_unparsed DISABLE;
  call archive_parse_unparsed();
  SELECT id into @local_id_avoid_mysql_bug FROM `user` limit 1;
  ALTER EVENT e_archive_parse_unparsed ENABLE;
END
//

DROP EVENT IF EXISTS e_rotate_archive//
CREATE EVENT e_rotate_archive
ON SCHEDULE EVERY 1 DAY COMMENT 'ROTATE OLD ARCHIVE ENTRIES' DO
BEGIN
IF (SELECT count(*) FROM sysconf WHERE id='archive_rotate' and val='yes')>0 THEN
  SET @archive_days=IFNULL((SELECT val FROM sysconf WHERE id='archive_delete_days'),7);
  SET @archive_limit=IFNULL((SELECT val FROM sysconf WHERE id='archive_delete_limit'),0);
  SET @use_mem=IFNULL((SELECT val FROM sysconf WHERE id='archive_delete_use_mem'),'no');
  IF @archive_days>0 THEN
	IF @use_mem != 'yes' THEN
  	  CREATE TEMPORARY TABLE archive_ids (id BIGINT UNSIGNED NOT NULL PRIMARY KEY);
  	ELSE
  	  CREATE TEMPORARY TABLE archive_ids (id BIGINT UNSIGNED NOT NULL PRIMARY KEY) ENGINE=MEMORY;
	END IF;
	
	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
	START TRANSACTION;
	IF @archive_limit > 0 THEN
    	PREPARE choose_archive_ids FROM 'INSERT INTO archive_ids SELECT id FROM `archive` WHERE received_ts < NOW() - INTERVAL ? DAY LIMIT ?';
	   	EXECUTE choose_archive_ids USING @archive_days, @archive_limit;
    ELSE
    	PREPARE choose_archive_ids FROM 'INSERT INTO archive_ids SELECT id FROM `archive` WHERE received_ts < NOW() - INTERVAL ?';
	   	EXECUTE choose_archive_ids USING @archive_days;
	END IF;
	DEALLOCATE PREPARE choose_archive_ids;
	-- Ignore ID's from entries that exist on archive_unparse
	DELETE t1.* FROM archive_ids as t1 LEFT JOIN archive_unparse AS t2 ON t1.id=t2.id WHERE t2.id IS NOT NULL;
	-- Ignore ID's from entries that exist on syslog
	DELETE t1.* FROM archive_ids as t1 LEFT JOIN syslog AS t2 ON t1.id=t2.id WHERE t2.id IS NOT NULL;
	-- Ignore ID's from entries that exist on abuser_evidense
	DELETE t1.* FROM archive_ids as t1 LEFT JOIN abuser_evidence AS t2 ON t1.id=t2.archive_id WHERE t2.archive_id IS NOT NULL;
	DELETE t1.* FROM `archive` AS t1 LEFT JOIN archive_ids AS t2 ON t1.id=t2.id WHERE t2.id IS NOT NULL;
	COMMIT;
  END IF;
END IF;  
END
//

ALTER EVENT e_rotate_archive DISABLE//
