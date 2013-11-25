/* $Id$ */

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DELIMITER //
/*
  When a new Archive  comes (new syslog event) then make sure
  that it doesn't match the patterns on our whitelist
  if it doesn't insert into the live syslog feed.
*/
DROP TRIGGER IF EXISTS `tai_archive_bh`//
CREATE TRIGGER `tai_archive_bh` AFTER INSERT ON `archive_bh` FOR EACH ROW 
BEGIN 
DECLARE mts INT DEFAULT 0;
SELECT count(*) INTO mts FROM whitelist_mem WHERE 
    NEW.msg LIKE pattern AND 
    NEW.program LIKE if(program='' or program is null,'%',program) AND 
    NEW.facility like if(facility='' or facility is null,'%',facility) AND  
    NEW.level like if(`level`='' or level is null,'%',`level`) AND  
    NEW.host LIKE if(host='' or host is null,'%',host);

   INSERT DELAYED INTO archive (host,facility,priority,`level`,program,pid,tag,msg,received_ts,created_at) VALUES (INET_ATON(NEW.host),NEW.facility,NEW.priority,NEW.level,NEW.program,NEW.pid,NEW.tag,NEW.msg,NEW.received_ts,NOW());
   IF mts=0 THEN
     INSERT DELAYED INTO syslog (host,facility,priority,`level`,program,pid,tag,msg,received_ts,created_at) VALUES (INET_ATON(NEW.host),NEW.facility,NEW.priority,NEW.level,NEW.program,NEW.pid,NEW.tag,NEW.msg,NEW.received_ts,NOW());
   END IF;
END
//

/*
 When a new whitelist entry is added make sure we remove the matching patterns
 from our current view (Syslog) and make sure we re-populate the memory table.
*/
DROP TRIGGER IF EXISTS `tai_whitelist`//
CREATE TRIGGER `tai_whitelist` AFTER INSERT ON `whitelist`  FOR EACH ROW 
BEGIN
INSERT INTO whitelist_mem (id,host,facility,level,program,pattern) VALUES (NEW.id,NEW.host,NEW.facility,NEW.level,NEW.program,NEW.pattern)
ON DUPLICATE KEY UPDATE host=values(host), facility=values(facility), level=values(level),program=values(program),pattern=values(pattern);
DELETE FROM syslog WHERE 
    msg LIKE NEW.pattern AND 
    program LIKE if(NEW.program='' or NEW.program is null,'%',NEW.program) AND 
    facility like if(NEW.facility='' or NEW.facility IS NULL,'%',NEW.facility) AND  
    `level` like if(NEW.level='' or NEW.level IS NULL,'%',NEW.level) AND  
    INET_NTOA(host) LIKE if(NEW.host='' OR NEW.host IS NULL,'%',NEW.host);
END
//

/*
 When a whitelist entry is updated make sure we remove the matching patterns
 from our current view (Syslog)
*/
DROP TRIGGER IF EXISTS `tau_whitelist`//
CREATE TRIGGER `tau_whitelist` AFTER UPDATE ON `whitelist` FOR EACH ROW 
BEGIN
INSERT INTO whitelist_mem (id,host,facility,level,program,pattern) VALUES (NEW.id,NEW.host,NEW.facility,NEW.level,NEW.program,NEW.pattern)
ON DUPLICATE KEY UPDATE host=values(host), facility=values(facility), level=values(level),program=values(program),pattern=values(pattern);
DELETE FROM syslog WHERE 
    msg LIKE NEW.pattern AND 
    program LIKE if(NEW.program='' or NEW.program is null,'%',NEW.program) AND 
    facility like if(NEW.facility='' or NEW.facility IS NULL,'%',NEW.facility) AND  
    `level` like if(NEW.level='' or NEW.level IS NULL,'%',NEW.level) AND  
    INET_NTOA(host) LIKE if(NEW.host='' OR NEW.host IS NULL,'%',NEW.host);
END
//

/* 
 Sync whitelist_mem after we delete a row from the InnoDB version
*/
DROP TRIGGER IF EXISTS `tad_whitelist`//
CREATE TRIGGER `tad_whitelist` AFTER DELETE ON `whitelist` FOR EACH ROW 
BEGIN
  DELETE FROM whitelist_mem WHERE id=OLD.id; 
END
//



DROP TRIGGER IF EXISTS tai_archive//
CREATE TRIGGER tai_archive AFTER INSERT ON archive FOR EACH ROW
BEGIN
    INSERT INTO archive_counters (ctype,name,val) VALUES ('program',NEW.program,1) ON DUPLICATE KEY UPDATE val=val+1;
    INSERT INTO archive_counters (ctype,name,val) VALUES ('facility',NEW.facility,1) ON DUPLICATE KEY UPDATE val=val+1;
    INSERT INTO archive_counters (ctype,name,val) VALUES ('level',NEW.level,1) ON DUPLICATE KEY UPDATE val=val+1;
    INSERT INTO archive_counters (ctype,name,val) VALUES ('host',NEW.host,1) ON DUPLICATE KEY UPDATE val=val+1;
END
//

DROP TRIGGER IF EXISTS tai_syslog//
CREATE TRIGGER tai_syslog AFTER INSERT ON syslog FOR EACH ROW
BEGIN
    INSERT INTO syslog_counters (ctype,name,val) VALUES ('program',NEW.program,1) ON DUPLICATE KEY UPDATE val=val+1;
    INSERT INTO syslog_counters (ctype,name,val) VALUES ('facility',NEW.facility,1) ON DUPLICATE KEY UPDATE val=val+1;
    INSERT INTO syslog_counters (ctype,name,val) VALUES ('level',NEW.level,1) ON DUPLICATE KEY UPDATE val=val+1;
    INSERT INTO syslog_counters (ctype,name,val) VALUES ('host',NEW.host,1) ON DUPLICATE KEY UPDATE val=val+1;
END
//


DROP TRIGGER IF EXISTS `tai_syslog_counters`//
CREATE TRIGGER `tai_syslog_counters` AFTER INSERT ON `syslog_counters`
 FOR EACH ROW BEGIN
  INSERT DELAYED INTO syslog_counters_daily (ctype,name,val,ts) VALUES (NEW.ctype,NEW.name,1,NOW()) ON DUPLICATE KEY UPDATE val=val+1;
END
//

DROP TRIGGER IF EXISTS `tau_syslog_counters`//
CREATE TRIGGER `tau_syslog_counters` AFTER UPDATE ON `syslog_counters`
 FOR EACH ROW BEGIN
  INSERT DELAYED INTO syslog_counters_daily (ctype,name,val,ts) VALUES (NEW.ctype,NEW.name,1,NOW()) ON DUPLICATE KEY UPDATE val=val+1;
END
//


DROP TRIGGER IF EXISTS `tai_archive_counters`//
CREATE TRIGGER `tai_archive_counters` AFTER INSERT ON `archive_counters`
 FOR EACH ROW BEGIN
  INSERT DELAYED INTO archive_counters_daily (ctype,name,val,ts) VALUES (NEW.ctype,NEW.name,1,NOW()) ON DUPLICATE KEY UPDATE val=val+1;
END
//

DROP TRIGGER IF EXISTS `tau_archive_counters`//
CREATE TRIGGER `tau_archive_counters` AFTER UPDATE ON `archive_counters`
 FOR EACH ROW BEGIN
  INSERT LOW_PRIORITY INTO archive_counters_daily (ctype,name,val,ts) VALUES (NEW.ctype,NEW.name,1,NOW()) ON DUPLICATE KEY UPDATE val=val+1;
END
//