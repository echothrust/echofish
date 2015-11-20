-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 13, 2012 at 03:54 PM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `echofish`
--

--
-- Dumping data for table `sysconf`
--
insert into sysconf (id,val) VALUES ('archive_activated','yes'),
									('whitelist_archived','no') 
							 ON DUPLICATE KEY UPDATE id=VALUES(id);

--
-- Dumping data for table `syslog_facility`
--

INSERT INTO `syslog_facility` (`name`, `description`, `num`) VALUES
('kern', NULL, 0),
('user', NULL, 1),
('mail', NULL, 2),
('daemon', NULL, 3),
('auth', NULL, 4),
('syslog', NULL, 5),
('lpr', NULL, 6),
('news', NULL, 7),
('uucp', NULL, 8),
('cron', 'Clock daemon', 9),
('authpriv', 'authpriv', 10),
('ftp', 'ftp', 11),
('ntp', 'ntp subsystem', 12),
('logaudit', 'log audit', 13),
('logalert', 'log alert', 14),
('cron', NULL, 15),
('local0', NULL, 16),
('local1', 'local use 1 (local1)', 17),
('local2', NULL, 18),
('local3', NULL, 19),
('local4', NULL, 20),
('local5', NULL, 21),
('local6', NULL, 22),
('local7', NULL, 23);

--
-- Dumping data for table `syslog_severity`
--

INSERT INTO `syslog_severity` (`name`, `description`, `num`) VALUES
('emerg', 'Emergency. System is unusable. A "panic" condition usually affecting multiple apps/servers/sites. At this level it would usually notify all tech staff on call.', 0),
('alert', 'Should be corrected immediately, therefore notify staff who can fix the problem. An example would be the loss of a primary ISP connection.', 1),
('crit', 'Should be corrected immediately, but indicates failure in a primary system, an example is a loss of a backup ISP connection.', 2),
('err', 'Non-urgent failures, these should be relayed to developers or admins; each item must be resolved within a given time.', 3),
('warn', 'Warning messages, not an error, but indication that an error will occur if action is not taken, e.g. file system 85% full - each item must be resolved within a given time.', 4),
('notice', 'Events that are unusual but not error conditions - might be summarized in an email to developers or admins to spot potential problems - no immediate action required.', 5),
('info', 'Normal operational messages - may be harvested for reporting, measuring throughput, etc. - no action required.', 6),
('debug', 'Info useful to developers for debugging the application, not useful during operations.', 7);

--
-- Dumping data for table `users`
--

INSERT INTO `user` (`id`, `username`, `firstname`, `lastname`, `password`, `email`, `activkey`, `created_at`, `lastvisit_at`, `superuser`, `status`, `level`) VALUES
(1, 'admin', '', '', md5('admin'), 'noc@echothrust.net', '138df2b208e7fa01f9584e0cd50d3575', '2012-11-05 14:06:25', '2012-11-06 12:35:59', 1, 1, 0),
(2, 'demo', '', '', md5('demo'), 'demo@example.com', '099f825543f7850cc038b90aaff39fac', '2012-11-05 14:06:25', '0000-00-00 00:00:00', 0, 1, 0);

INSERT INTO message_parser (ptype,name) value ("any","abuser_parser");

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
