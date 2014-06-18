/* $Id$ */
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8 COLLATE 'utf8_unicode_ci';

--
-- Database: `echofish`
--

-- --------------------------------------------------------

DROP TABLE IF EXISTS `archive_bh`;
CREATE TABLE IF NOT EXISTS `archive_bh` (
  `host` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'localhost',
  `facility` bigint(20) DEFAULT NULL,
  `priority` bigint(20) DEFAULT NULL,
  `level` bigint(20) DEFAULT NULL,
  `program` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `msg` text COLLATE utf8_unicode_ci,
  `received_ts` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=BLACKHOLE DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `archive`
--

DROP TABLE IF EXISTS `archive`;
CREATE TABLE IF NOT EXISTS `archive` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `host` bigint(20) unsigned NOT NULL,
  `facility` bigint(20) DEFAULT NULL,
  `priority` bigint(20) DEFAULT NULL,
  `level` bigint(20) DEFAULT NULL,
  `program` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `tag` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `msg` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `received_ts` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COLLATE=utf8_unicode_ci ;


--
-- Table structure for table `syslog`
--

DROP TABLE IF EXISTS `syslog`;
CREATE TABLE IF NOT EXISTS `syslog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `host` bigint(20) unsigned NOT NULL,
  `facility` tinyint(3) unsigned DEFAULT '0',
  `priority` tinyint(3) unsigned DEFAULT '0',
  `level` tinyint(3) unsigned DEFAULT NULL,
  `program` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pid` int(11) unsigned DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `msg` text COLLATE utf8_unicode_ci,
  `received_ts` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `host_index_idx` (`host`),
  KEY `facility_index_idx` (`facility`),
  KEY `level_index_idx` (`level`),
  KEY `program_index_idx` (`program`),
  KEY `msg_index_idx` (`msg`(255)),
  KEY `facility` (`facility`,`program`),
  KEY `level_program` (`level`,`program`),
  KEY `full_key` (`host`,`facility`,`level`,`program`,`pid`,`received_ts`),
  KEY `level_received` (`level`,`received_ts`),
  KEY `program_received` (`program`,`received_ts`),
  KEY `facility_received` (`facility`,`received_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `archive_counters`
--

DROP TABLE IF EXISTS `archive_counters`;
CREATE TABLE IF NOT EXISTS `archive_counters` (
  `ctype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `val` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ctype`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `archive_counters_daily`
--

DROP TABLE IF EXISTS `archive_counters_daily`;
CREATE TABLE IF NOT EXISTS `archive_counters_daily` (
  `ctype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `val` bigint(20) NOT NULL DEFAULT '0',
  `ts` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`ctype`,`name`,`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `firstname` (`firstname`,`lastname`,`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `sysconf`
--

DROP TABLE IF EXISTS `sysconf`;
CREATE TABLE IF NOT EXISTS `sysconf` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `val` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------



-- --------------------------------------------------------

--
-- Table structure for table `syslog_counters`
--

DROP TABLE IF EXISTS `syslog_counters`;
CREATE TABLE IF NOT EXISTS `syslog_counters` (
  `ctype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `val` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ctype`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `syslog_counters_daily`
--

DROP TABLE IF EXISTS `syslog_counters_daily`;
CREATE TABLE IF NOT EXISTS `syslog_counters_daily` (
  `ctype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `val` bigint(20) NOT NULL DEFAULT '0',
  `ts` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`ctype`,`name`,`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `syslog_facility`
--

DROP TABLE IF EXISTS `syslog_facility`;
CREATE TABLE IF NOT EXISTS `syslog_facility` (
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `syslog_severity`
--

DROP TABLE IF EXISTS `syslog_severity`;
CREATE TABLE IF NOT EXISTS `syslog_severity` (
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `activkey` varchar(128) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastvisit_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  salt VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `superuser` (`superuser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `whitelist`
--

DROP TABLE IF EXISTS `whitelist`;
CREATE TABLE IF NOT EXISTS `whitelist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8_unicode_ci,
  `host` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `facility` varchar(20) DEFAULT NULL,
  `level` varchar(20) DEFAULT NULL,
  `program` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pattern` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `host_idx` (`host`),
  KEY `facility_idx` (`facility`),
  KEY `level_idx` (`level`),
  KEY `pattern_idx` (`pattern`),
  KEY `program_idx` (`program`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;



-- --------------------------------------------------------

--
-- Table structure for table `whitelist_mem`
--

DROP TABLE IF EXISTS `whitelist_mem`;
CREATE TABLE IF NOT EXISTS `whitelist_mem` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `host` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `facility` varchar(20) DEFAULT NULL,
  `level` varchar(20) DEFAULT NULL,
  `program` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pattern` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `host_idx` (`host`),
  KEY `facility_idx` (`facility`),
  KEY `level_idx` (`level`),
  KEY `pattern_idx` (`pattern`),
  KEY `program_idx` (`program`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `pattern`;
CREATE TABLE IF NOT EXISTS `pattern` (
  id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `host` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `facility` int NOT NULL DEFAULT '0',
  `level` int NOT NULL DEFAULT '0',
  `program` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pattern` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  UNIQUE KEY (`host`,`facility`,`level`,`program`,`pattern`(255)),
  KEY `pattern_index_idx` (`pattern`),
   KEY `combo_index_idx` (`program`,`pattern`),
  KEY `program_index_idx` (`program`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;





DROP TABLE IF EXISTS message_parser;
CREATE TABLE message_parser (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  ptype varchar(10) NOT NULL default 'syslog',
  name VARCHAR(100),
  weight int,
  unique (name,weight)
) ENGINE=InnoDB COLLATE=utf8_unicode_ci;

-- Table structure for table `YiiSession`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` char(32) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` longblob,
  PRIMARY KEY (`id`),
  KEY `expire` (`expire`),
  KEY `id` (`id`,`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


DROP TABLE IF EXISTS archive_parser;
CREATE TABLE archive_parser (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  ptype varchar(10) NOT NULL default 'syslog',
  name VARCHAR(100),
  weight int,
  unique (name,weight)
) ENGINE=InnoDB COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS archive_unparse;
CREATE TABLE archive_unparse (
id BIGINT UNSIGNED PRIMARY KEY,
pending tinyint default 1
) ENGINE=InnoDB;

DROP TABLE IF EXISTS abuser_trigger;
CREATE TABLE IF NOT EXISTS `abuser_trigger` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `facility` tinyint(4) DEFAULT NULL,
  `severity` tinyint(4) DEFAULT NULL,
  `program` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `msg` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pattern` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grouping` tinyint(3) unsigned DEFAULT NULL,
  `capture` tinyint(3) unsigned DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `occurrence` int(11) DEFAULT NULL,
  `priority` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`),
 UNIQUE (
`facility` ,
`severity` ,
`program` (50),
`msg` (50),
`pattern`(50) ,
`grouping` ,
`capture`
)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS abuser_evidence;
CREATE TABLE IF NOT EXISTS `abuser_evidence` (
  `incident_id` bigint(20) NOT NULL,
  `archive_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`incident_id`,`archive_id`),
  KEY `fk_archive_id` (`archive_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS abuser_incident;
CREATE TABLE IF NOT EXISTS `abuser_incident` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` int(10) unsigned NOT NULL,
  `trigger_id` bigint(20) DEFAULT NULL,
  `counter` bigint(20) DEFAULT NULL,
  `first_occurrence` datetime DEFAULT NULL,
  `last_occurrence` datetime DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uniq_incident` (`ip`,`trigger_id`),
  KEY `fk_trigger_id` (`trigger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `host`;
CREATE TABLE `host` (
	ip int unsigned primary key,
	fqdn varchar(255) NOT NULL,
	short varchar(50),
	description text
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `trail`;
CREATE TABLE `trail` (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  category varchar(50),
  message longtext
) ENGINE=InnoDB COMMENT="Application Audit Trails";

ALTER TABLE `abuser_incident`
  ADD CONSTRAINT `fk_trigger_id` FOREIGN KEY (`trigger_id`) REFERENCES `abuser_trigger` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

 

INSERT INTO archive_parser (ptype,name) value ("syslog","abuser_parser");
INSERT INTO archive_parser (ptype,name) value ("archive","abuser_parser");

SET FOREIGN_KEY_CHECKS=1;
