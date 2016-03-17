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
/*
  When a new Archive  comes (new syslog event) then make sure
  that it doesn't match the patterns on our whitelist
  if it doesn't insert into the live syslog feed.
*/
DROP TRIGGER IF EXISTS `tai_archive_bh`//
CREATE TRIGGER `tai_archive_bh` AFTER INSERT ON `archive_bh` FOR EACH ROW 
BEGIN 
DECLARE mts INT DEFAULT 0;
IF NEW.host IS NOT NULL AND NEW.host != ''  THEN
      SET @hostexists=(SELECT id FROM `host` WHERE fqdn=NEW.host or short=NEW.host or ip like NEW.host or INET6_NTOA(ip)=NEW.host);
      IF @hostexists IS NULL THEN
        INSERT INTO `host` (`ip`,`fqdn`,`short`) VALUES (INET6_ATON(NEW.host),NEW.host,NEW.host);
        SET @hostexists=LAST_INSERT_ID();
      END IF;
      IF (SELECT count(*) FROM sysconf WHERE id="archive_activated" and val="yes")>0 AND @hostexists IS NOT NULL THEN 
	    INSERT DELAYED INTO archive (host,facility,priority,`level`,program,pid,tag,msg,received_ts,created_at) VALUES (@hostexists,NEW.facility,NEW.priority,NEW.level,NEW.program,NEW.pid,NEW.tag,NEW.msg,NEW.received_ts,sysdate());
	  ELSEIF @hostexists IS NOT NULL THEN
	    SELECT count(*) INTO mts FROM whitelist_mem as wm WHERE 
	    NEW.msg LIKE wm.pattern AND 
	    NEW.program LIKE if(wm.program='' or wm.program is null,'%',wm.program) AND 
		NEW.facility like if(wm.facility='' or wm.facility is null,'%',wm.facility) AND  
		NEW.level like if(wm.level='' or wm.level is null,'%',wm.level) AND
	    NEW.host LIKE if(wm.host='' OR wm.host IS NULL,'%',wm.host);
	    IF mts=0 THEN
	     INSERT DELAYED INTO syslog (host,facility,priority,`level`,program,pid,tag,msg,received_ts,created_at) VALUES (@hostexists,NEW.facility,NEW.priority,NEW.level,NEW.program,NEW.pid,NEW.tag,NEW.msg,NEW.received_ts,sysdate());
	    END IF;
	  END IF;
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
    msg like NEW.pattern AND 
    program LIKE if(NEW.program='' or NEW.program is null,'%',NEW.program) AND 
    facility like if(NEW.facility='' or NEW.facility IS NULL,'%',NEW.facility) AND  
    `level` like if(NEW.level='' or NEW.level IS NULL,'%',NEW.level) AND  
    (host in (SELECT id FROM host WHERE INET6_NTOA(ip) like NEW.host or fqdn like NEW.host or short like NEW.host)); 
    /*LIKE if(NEW.host='' OR NEW.host IS NULL,'%',NEW.host);*/
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
    (host in (SELECT id FROM host WHERE INET6_NTOA(ip) like NEW.host or fqdn like NEW.host or short like NEW.host));
    /* INET_NTOA(host) LIKE if(NEW.host='' OR NEW.host IS NULL,'%',NEW.host);*/
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
DECLARE mts INT DEFAULT 0;
IF (SELECT count(*) FROM sysconf WHERE (id="archive_activated" and val="yes") OR (id="whitelist_archived" and val="no"))=2 THEN
	SELECT count(*) INTO mts FROM whitelist_mem as wm WHERE 
    	NEW.msg LIKE wm.pattern AND 
    	NEW.program LIKE if(wm.program='' or wm.program is null,'%',wm.program) AND 
    	NEW.facility like if(wm.facility='' or wm.facility is null,'%',wm.facility) AND  
    	NEW.level like if(wm.`level`='' or wm.level is null,'%',wm.`level`) AND  
    	NEW.host IN (SELECT id FROM host WHERE INET6_NTOA(ip) LIKE wm.host or fqdn LIKE wm.host OR short LIKE wm.host);
	    IF mts=0 THEN
	      INSERT DELAYED INTO syslog (id,host,facility,priority,`level`,program,pid,tag,msg,received_ts,created_at) VALUES (NEW.id,NEW.host,NEW.facility,NEW.priority,NEW.level,NEW.program,NEW.pid,NEW.tag,NEW.msg,NEW.received_ts,sysdate());
	    END IF;
	    INSERT INTO archive_counters (ctype,name,val) VALUES ('program',NEW.program,1) ON DUPLICATE KEY UPDATE val=val+1;
	    INSERT INTO archive_counters (ctype,name,val) VALUES ('facility',NEW.facility,1) ON DUPLICATE KEY UPDATE val=val+1;
	    INSERT INTO archive_counters (ctype,name,val) VALUES ('level',NEW.level,1) ON DUPLICATE KEY UPDATE val=val+1;
	    INSERT INTO archive_counters (ctype,name,val) VALUES ('host',NEW.host,1) ON DUPLICATE KEY UPDATE val=val+1;
	    INSERT INTO archive_unparse VALUES (NEW.id,1);
END IF;
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

DROP TRIGGER IF EXISTS `tad_syslog`//
CREATE TRIGGER `tad_syslog` AFTER DELETE ON `syslog` FOR EACH ROW 
BEGIN
	IF (SELECT count(*) FROM sysconf WHERE (id="archive_activated" and val="no")  OR (id="whitelist_archived" and val="yes"))=2 THEN
 		INSERT INTO archive (id, host, facility, priority, level, program, pid, tag, msg, received_ts, created_at) values (OLD.id, OLD.host, OLD.facility, OLD.priority, OLD.level, OLD.program, OLD.pid, OLD.tag, OLD.msg, OLD.received_ts, OLD.created_at);
 	END IF;
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
