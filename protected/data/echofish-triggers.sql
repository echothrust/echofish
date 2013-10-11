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
DROP TRIGGER IF EXISTS `auto_syslog_archive`//
CREATE TRIGGER `auto_syslog_archive` AFTER INSERT ON `archive` FOR EACH ROW 
BEGIN 
DECLARE mts INT DEFAULT 0;
SELECT count(*) INTO mts FROM whitelist_mem WHERE 
		NEW.msg LIKE pattern AND 
		NEW.program LIKE if(program='' or program is null,'%%',program) AND 
		NEW.facility like if(facility<0,'%%',facility) AND  
		NEW.level like if(`level`<0,'%%',`level`) AND  
		INET_NTOA(NEW.host) LIKE if(host='0','%%',host);

   IF mts=0 THEN
     INSERT DELAYED INTO syslog (id,host,facility,priority,`level`,program,pid,tag,msg,received_ts,created_at) VALUES (NEW.id,NEW.host,NEW.facility,NEW.priority,NEW.level,NEW.program,NEW.pid,NEW.tag,NEW.msg,NEW.received_ts,NOW());
   END IF;
  INSERT INTO archive_counters (ctype,name,val) VALUES ('program',NEW.program,1) ON DUPLICATE KEY UPDATE val=val+1;
  INSERT INTO archive_counters (ctype,name,val) VALUES ('facility',NEW.facility,1) ON DUPLICATE KEY UPDATE val=val+1;
  INSERT INTO archive_counters (ctype,name,val) VALUES ('level',NEW.level,1) ON DUPLICATE KEY UPDATE val=val+1;
  INSERT INTO archive_counters (ctype,name,val) VALUES ('host',NEW.host,1) ON DUPLICATE KEY UPDATE val=val+1;
  INSERT INTO archive_unparse VALUES (NEW.id,1);
END
//

/*
 When a new whitelist entry is added make sure we remove the matching patterns
 from our current view (Syslog) and make sure we re-populate the memory table.
*/
DROP TRIGGER IF EXISTS `auto_whitelist_syslog`//
CREATE TRIGGER `auto_whitelist_syslog` AFTER INSERT ON `whitelist`
 FOR EACH ROW BEGIN
REPLACE whitelist_mem (host,facility,level,program,pattern) VALUES (NEW.host,NEW.facility,NEW.level,NEW.program,NEW.pattern);
DELETE FROM syslog WHERE 
		msg LIKE NEW.pattern AND 
		program LIKE if(NEW.program='' or NEW.program is null,'%%',NEW.program) AND 
		facility like if(NEW.facility<0,'%%',NEW.facility) AND  
		`level` like if(NEW.level<0,'%%',NEW.level) AND  
		INET_NTOA(host) LIKE if(NEW.host='0','%%',NEW.host);
END
//

/*
 When a whitelist entry is updated make sure we remove the matching patterns
 from our current view (Syslog)
*/
DROP TRIGGER IF EXISTS `tau_auto_whitelist_syslog`//
CREATE TRIGGER `tau_auto_whitelist_syslog` AFTER UPDATE ON `whitelist`
 FOR EACH ROW BEGIN
DELETE FROM syslog WHERE 
		msg LIKE NEW.pattern AND 
		program LIKE if(NEW.program='' or NEW.program is null,'%%',NEW.program) AND 
		facility like if(NEW.facility<0,'%%',NEW.facility) AND  
		`level` like if(NEW.level<0,'%%',NEW.level) AND  
		INET_NTOA(host) LIKE if(NEW.host='0','%%',NEW.host);
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

DROP TRIGGER IF EXISTS tbi_syslog_memo//
CREATE TRIGGER tbi_syslog_memo BEFORE INSERT ON syslog_memo FOR EACH ROW 
BEGIN
  SET NEW.created_at=NOW();
  SET NEW.archive_id=NEW.syslog_id;
END
//



/*
DROP TRIGGER IF EXISTS tau_abuser_incident//
CREATE TRIGGER tau_abuser_incident AFTER UPDATE ON abuser_incident FOR EACH ROW 
BEGIN
	IF (SELECT priority FROM abuser_trigger WHERE occurrence=NEW.counter and id=NEW.trigger_id) IS NOT NULL THEN
		SELECT syslog_log(13,priority,concat(description,' ',INET_NTOA(NEW.ip)) into @ret FROM abuser_trigger WHERE id=NEW.trigger_id;
	END IF;
END
//

*/
/*
DROP TRIGGER IF EXISTS `auto_syslog_archive`//
CREATE TRIGGER `auto_syslog_archive` AFTER INSERT ON `archive` FOR EACH ROW 
BEGIN 
DECLARE mts INT DEFAULT 0;

SELECT id,pattern,grouping,capture INTO mts,@pattern,@grouping,@capture FROM abuser_trigger WHERE 
		NEW.msg LIKE msg AND 
		NEW.program LIKE if(program='' or program is null,'%%',program) AND 
		NEW.facility like if(facility<0,'%%',facility) AND  
		NEW.level like if(`severity`<0,'%%',`severity`)
		LIMIT 1;

  IF mts>0 THEN
	INSERT INTO abuser_ip (id,trigger_id,counter,first_occurrence,last_occurrence) 
		VALUES (INET_ATON(PREG_CAPTURE(@pattern,NEW.msg,@grouping,@capture)),
			mts,1,NOW(),now())
		ON DUPLICATE KEY UPDATE counter=counter+1,last_occurrence=NOW();
  END IF;
END
//

*/
