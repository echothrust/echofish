# init.sql

create database ETS_echofish;
use ETS_echofish;
source /db/00_echofish-schema.sql
DELIMITER ;
source /db/echofish-dataonly.sql
DELIMITER ;
source /db/echofish-functions.sql
DELIMITER ;
source /db/echofish-triggers.sql
DELIMITER ;
source /db/echofish-events.sql
DELIMITER ;
source /db/echofish-procedures.mariadb10.sql
DELIMITER ;

create user echofish identified by "dbpass";
grant all privileges on ETS_echofish.* to echofish@'%';

