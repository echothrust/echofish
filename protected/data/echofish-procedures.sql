/* $Id$ */

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DELIMITER //

DROP PROCEDURE IF EXISTS delete_duplicate_whitelist//
CREATE PROCEDURE delete_duplicate_whitelist() 
BEGIN
  DECLARE wid,wfacility,wlevel,done BIGINT DEFAULT 0;
  DECLARE whost,wprogram VARCHAR(255) DEFAULT '';
  DECLARE wpattern VARCHAR(512) DEFAULT '';
  DECLARE uwp CURSOR FOR SELECT id,host,program,facility,`level`,pattern FROM whitelist;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = -1;
  START TRANSACTION WITH CONSISTENT SNAPSHOT;
  OPEN uwp;
  
  read_loop: LOOP
      FETCH uwp INTO wid,whost,wprogram,wfacility,wlevel,wpattern;
      IF done = -1 THEN
        LEAVE read_loop;
      END IF;
    delete_segment: BEGIN
      DECLARE CONTINUE HANDLER FOR NOT FOUND SET @x='fuckoff';
 	    DELETE FROM whitelist WHERE
          pattern LIKE wpattern AND 
    		  program LIKE if(wprogram='' or wprogram is null,'%%',wprogram) AND 
      		facility like if(wfacility<0,'%%',wfacility) AND  
      		`level` like if(wlevel<0,'%%',wlevel) AND  
		      host LIKE if(whost='0','%%',whost) AND
		      id!=wid; 	    
 	  END delete_segment;
  END LOOP read_loop;
  CLOSE uwp;
  COMMIT;
END;
//

DROP PROCEDURE IF EXISTS extract_ipaddr//
CREATE PROCEDURE extract_ipaddr(IN msg VARCHAR(5000))
BEGIN
DECLARE matching INT default 1;
DECLARE ipaddr VARCHAR(255);
SET ipaddr=(SELECT preg_capture('/(([0-9]+)(?:\.[0-9]+){3})/', msg, 1, matching));
tfer_loop:WHILE (ipaddr IS NOT NULL and length(ipaddr)>0 ) DO
	SELECT ipaddr;
	set matching=matching+1;
	SET ipaddr=(SELECT preg_capture('/(([0-9]+)(?:\.[0-9]+){3})/', msg, 1, matching));
END WHILE tfer_loop;
END;
//


DROP PROCEDURE IF EXISTS archive_parser_trigger//
CREATE PROCEDURE archive_parser_trigger(IN aid BIGINT UNSIGNED,IN ahost BIGINT UNSIGNED,IN aprogram VARCHAR(255),IN afacility INT,in alevel INT,IN apid BIGINT,in amsg TEXT,in areceived_ts TIMESTAMP,IN ttype VARCHAR(10))
BEGIN
  DECLARE apid,done INT;
  DECLARE apptype,apname VARCHAR(255);
  DECLARE uwp CURSOR FOR SELECT id,name FROM archive_parser WHERE ptype=ttype ORDER BY weight,name,id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = -1;
  OPEN uwp;
  
  read_loop: LOOP
      FETCH uwp INTO apid,apname;
      IF done = -1 THEN
        LEAVE read_loop;
      END IF;

    SET @callquery=concat('CALL ',apname,'(?,?,?,?,?,?,?,?)');
    PREPARE stmtcall FROM @callquery;
    set @aid=aid;
    set @ahost=ahost;
    set @aprogram=aprogram;
    set @afacility=afacility;
    set @alevel=alevel;
    set @apid=apid;
    set @amsg=amsg;
    set @areceived_ts=areceived_ts;
    EXECUTE stmtcall USING @aid,@ahost,@aprogram,@afacility,@alevel,@apid,@amsg,@areceived_ts;
    DEALLOCATE PREPARE stmtcall;
  END LOOP read_loop;
  CLOSE uwp;

END;
//

DROP PROCEDURE IF EXISTS archive_parse_unparsed//
CREATE PROCEDURE archive_parse_unparsed()
BEGIN
DECLARE deadlock,done INT DEFAULT 0;
DECLARE attempts INT DEFAULT 0;
DECLARE auid BIGINT UNSIGNED DEFAULT 0;
DECLARE uwp CURSOR FOR SELECT id FROM archive_unparse WHERE pending=1 ORDER BY id LIMIT 10000;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = -1;
SET SESSION time_zone='+00:00';
START TRANSACTION;
OPEN uwp;
read_loop: LOOP
  FETCH uwp INTO auid;
  IF done = -1 THEN
    LEAVE read_loop;
  END IF;
  DELETE FROM archive_unparse WHERE id=auid;
  SELECT host,facility,`level`,program,pid,msg,received_ts INTO @ahost,@afacility,@alevel,@aprogram,@apid,@amsg,@areceived_ts FROM archive WHERE id=auid;
  CALL archive_parser_trigger(auid,@ahost,@aprogram,@afacility,@alevel,@apid,@amsg,@areceived_ts,'archive');
END LOOP read_loop;
CLOSE uwp;
COMMIT;

END;
//
/*
SAMPLE PROCEDURE FOR PARSING
DROP PROCEDURE IF EXISTS demo//
CREATE PROCEDURE demo(IN aid BIGINT UNSIGNED,IN ahost BIGINT UNSIGNED,IN aprogram VARCHAR(255),IN afacility INT,in alevel INT,IN apid BIGINT,in amsg TEXT,in areceived_ts TIMESTAMP)
BEGIN
  SELECT 'MPIKA STO DEMO',aid;
END;
//
*/