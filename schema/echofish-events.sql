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
	CALL eproc_rotate_archive();
END IF;  
END
//


