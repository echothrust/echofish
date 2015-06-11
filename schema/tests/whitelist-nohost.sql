
truncate whitelist;
truncate whitelist_mem;
truncate archive;
truncate syslog;
truncate host;
truncate archive_counters;
truncate archive_counters_daily;
truncate archive_unparse;

-- create the testing whitelists.
select 'Creating testing whitelists' as '';
INSERT INTO `whitelist` (`description`, `host`, `facility`, `level`, `program`, `pattern`) VALUES
('TEST1 IP partial','1.1.%', '1', '1', 'test1', '%'),
('TEST2 IP match','2.2.2.2', '2', '2', 'test2', '%'),
('TEST3 Hostname partial','test3%', '3', '3', 'test3', '%'),
('TEST4 Hostname match','test4','4', '4', 'test4', '%');

--  Syslog entries for each test
select 'Creating testing archive_bh' as '';

select 'Creating testing archive_bh from host 1.1.1.1' as '';
INSERT INTO `archive_bh` (`host`, 		`facility`, `priority`, `level`, `program`, `pid`, `tag`, `msg`) VALUES  
('1.1.1.1', 	'1', 		'1', 		'1', 	'test1', 	'1',   '1',   '1');

select 'Creating testing archive_bh from host 2.2.2.2' as '';
INSERT INTO `archive_bh` 
(`host`, 		`facility`, `priority`, `level`, `program`, `pid`, `tag`, `msg`) VALUES 
('2.2.2.2', 	'2', 		'2', 		'2', 	'test2', 	'2',   '2',   '2');

select 'Creating testing archive_bh from host 3.3.3.3' as '';
INSERT INTO `archive_bh` 
(`host`, 		`facility`, `priority`, `level`, `program`, `pid`, `tag`, `msg`) VALUES 
('test3', 		'3', 		'3', 		'3', 	'test3', 	'3',   '3',   '3');
select 'Creating testing archive_bh from host 4.4.4.4' as '';
INSERT INTO `archive_bh` 
(`host`, 		`facility`, `priority`, `level`, `program`, `pid`, `tag`, `msg`) VALUES 
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
