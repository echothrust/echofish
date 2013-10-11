/* $Id$ */
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `echofish`
--

-- --------------------------------------------------------

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
/* ) ENGINE=blackhole DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COLLATE=utf8_unicode_ci ; */
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
  KEY `level` (`level`,`program`),
  KEY `host` (`host`,`facility`,`level`,`program`,`pid`,`received_ts`),
  KEY `level_2` (`level`,`received_ts`),
  KEY `program` (`program`,`received_ts`),
  KEY `facility_2` (`facility`,`received_ts`)
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
  `facility` bigint(20) DEFAULT NULL,
  `level` bigint(20) DEFAULT NULL,
  `program` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pattern` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `program_index_idx` (`program`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;



-- --------------------------------------------------------

--
-- Table structure for table `whitelist_mem`
--

DROP TABLE IF EXISTS `whitelist_mem`;
CREATE TABLE IF NOT EXISTS `whitelist_mem` (
  `host` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `facility` bigint(20) NOT NULL DEFAULT '0',
  `level` bigint(20) NOT NULL DEFAULT '0',
  `program` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pattern` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`host`,`facility`,`level`,`program`,`pattern`),
  INDEX `pattern_index_idx` (`pattern`),
  INDEX `combo_index_idx` (`program`,`pattern`),
  INDEX `program_index_idx` (`program`)
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


DROP TABLE IF EXISTS `syslog_memo`;
CREATE TABLE syslog_memo (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  sender varchar(255) NOT NULL DEFAULT '',
  message VARCHAR(255) NOT NULL DEFAULT '',
  syslog_id BIGINT UNSIGNED DEFAULT NULL,
  archive_id BIGINT DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX syslog_id (syslog_id),
  FOREIGN KEY (syslog_id) REFERENCES syslog(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB COLLATE=utf8_unicode_ci ;
--



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
) ENGINE=InnoDB COLLATE=utf8_unicode_ci;

-- Table structure for table `YiiSession`
--

DROP TABLE IF EXISTS `YiiSession`;
CREATE TABLE IF NOT EXISTS `YiiSession` (
  `id` char(32) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` longblob,
  PRIMARY KEY (`id`),
  KEY `expire` (`expire`),
  KEY `id` (`id`,`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
	id INT primary key auto_increment,
	label varchar(255),
	url varchar(255),
	tag varchar(255),
	visible text,
	active text,
	parent_id int,
	priority int,
	description text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
SET FOREIGN_KEY_CHECKS=1;
