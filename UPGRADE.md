# UPGRADE Echofish from v0.4.x to v0.5.x 
Starting with version 0.4, Echofish has import/export operations for Whitelists
and Abuser Triggers. However, until these features are completed, this 
procedure is will act as the suggested Upgrade Guide.

This guide provides instructions to upgrate an Echofish v0.4 installation to v0.5

## Fetching files
Download [Echofish v0.5.0](https://github.com/echothrust/echofish/archive/echofish-v0.5.0.tar.gz) and extract over your previous installation.

```
ftp https://github.com/echothrust/echofish/archive/echofish-v0.5.0.tar.gz
tar zxf echofish-v0.5.0.tar.gz -C /var/www/echofish
cd /var/www/echofish
```

## Update the database schema
* Disable the event scheduler temporarily by executing the following command,
```sh
mysql -e "SET GLOBAL EVENT_SCHEDULER=off;"
```

* Change the default character set and collation of the database (assuming your 
database is `ETS_echofish`)
```
mysql -e 'ALTER DATABASE ETS_echofish CHARACTER SET utf8 COLLATE utf8_unicode_ci'
```

* Import the updated schema (note that this might take long time to complete 
since it updates the archive and syslog tables)
```sh
mysql ETS_echofish < schema/updates/v0.4-to-v0.5.sql
```

* Import the remaining updated functions, triggers and events
```sh
mysql ETS_echofish < schema/echofish-functions.sql
mysql ETS_echofish < schema/echofish-procedures.sql
mysql ETS_echofish < schema/echofish-triggers.sql
mysql ETS_echofish < schema/echofish-events.sql
```

* Reactivate the MySQL/MariaDB event scheduler
```
mysql -e "SET GLOBAL EVENT_SCHEDULER=on;"
```

Now that you're all set, visit the Echofish web interface and verify that 
everything is working as it should.
