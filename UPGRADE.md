## UPGRADE Echofish

Starting with v.0.4 Echofish has import/export operations for Whitelists and Abuser Triggers. Until backup is completely implemented, this procedure is provided as an Upgrade Guide.

### 1 - Database changes

In case the database schema has changed, keep a backup of your precious data, at least the tables 'user', 'whitelist' and 'abuser_trigger':

```sh
# Adjust IP, DBUSER 'echofish' and DBNAME 'ETS_echofish' to match yours
# Backup tables containing user data
mysqldump --no-create-info -h mysql.server.ip -u echofish -p ETS_echofish user whitelist abuser_trigger > userdata.sql
# Backup table containing archived logs separately
mysqldump --no-create-info -h mysql.server.ip -u echofish -p ETS_echofish archive > archivedata.sql
``` 

After successfully backing up your valuable data, you can proceed to DROP the database:

```sh
# Make sure you have backed up your data!
# Replace credentials with your mysql admin user. 
mysqladmin -u[username] -p[password] drop 
```
### 2 - Alternative (keeping database)
The procedure is as following:

  * Stop the event scheduler
  * Rename tables
  * Import schema
  * Rename back
  * Populate host table (new table)
  * Proceed with import
  * Activate event schedule again

```
mysql -e "SET GLOBAL EVENT_SCHEDULER=OFF"

mysql ETS_echofish -e "RENAME TABLE archive TO archive_old"
mysql ETS_echofish -e "RENAME TABLE whitelist TO whitelist_old"

mysql ETS_echofish < schema/00_echofish-schema.sql
mysql ETS_echofish < schema/echofish-dataonly.sql

mysql ETS_echofish -e "drop table archive"
mysql ETS_echofish -e "rename table archive_old to archive"
mysql ETS_echofish -e "INSERT INTO host(ip) SELECT DISTINCT host FROM archive"

mysql ETS_echofish < schema/echofish-functions.sql
mysql ETS_echofish < schema/echofish-procedures.sql
mysql ETS_echofish < schema/echofish-triggers.sql
mysql ETS_echofish < schema/echofish-events.sql

mysql  ETS_echofish_prod -e "SET GLOBAL EVENT_SCHEDULER=ON"
```


### 3 - Get the latest code

Download and unpack [latest Echofish](https://github.com/echothrust/echofish/archive/master.tar.gz) to proceed with [regular installation](https://github.com/echothrust/echofish/blob/master/docs/INSTALL.md) (includes instructions for restoring).
