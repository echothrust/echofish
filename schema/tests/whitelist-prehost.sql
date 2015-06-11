
truncate whitelist;
truncate whitelist_mem;
truncate archive;
truncate syslog;
truncate host;
truncate archive_counters;
truncate archive_counters_daily;
truncate archive_unparse;

select 'Populate hosts prior to whitelists' as '';
INSERT INTO `host` (ip,fqdn,short) values
(inet_aton('1.1.1.1'), 'test1.example.com','test1'),
(inet_aton('2.2.2.2'), 'test2.example.com','test2'),
(inet_aton('3.3.3.3'), 'test3.example.com','test3'),
(inet_aton('4.4.4.4'), 'test4.example.com','test4');


-- create the testing whitelists.
select 'Creating testing whitelists' as '';
INSERT INTO `whitelist` (`description`, `host`, `facility`, `level`, `program`, `pattern`) VALUES
('TEST1 IP partial','1.1.%', '1', '1', 'test1', '%'),
('TEST2 IP match','2.2.2.2', '2', '2', 'test2', '%'),
('TEST3 Hostname partial','test3%', '3', '3', 'test3', '%'),
('TEST4 Hostname match','test4','4', '4', 'test4', '%');

--  Syslog entries for each test
select 'Creating testing archive_bh' as '';
INSERT INTO `archive_bh` 
(`host`, 		`facility`, `priority`, `level`, `program`, `pid`, `tag`, `msg`) VALUES 
('1.1.1.1', 	'1', 		'1', 		'1', 	'test1', 	'1',   '1',   'test1 numeric ip'),
('2.2.2.2', 	'2', 		'2', 		'2', 	'test2', 	'2',   '2',   'test2 numeric ip'),
('test1', 	'1', 		'1', 		'1', 	'test1', 	'1',   '1',   'test1 shortname'),
('test2', 	'2', 		'2', 		'2', 	'test2', 	'2',   '2',   'test2 shortname'),
('test3', 		'3', 		'3', 		'3', 	'test3', 	'3',   '3',   '3'),
('test4', 		'4', 		'4', 		'4', 	'test4', 	'4',   '4',   '4');

select 'Waiting 15sec for events to kick in' as '';
select sleep(15) as '' INTO @devnull;

select 'selecting whitelist counts' as '';
select (SELECT count(*) FROM whitelist) as whitelist,(SELECT count(*) FROM whitelist_mem) as whitelist_mem;

select 'selecting host count' as '';
select count(*) from host;

select 'selecting syslog should be empty' as '';
select * from syslog;

select 'selecting archive should have entries' as '';
select * from archive;
