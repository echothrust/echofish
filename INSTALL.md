# INSTALLING Echofish
This Echofish guide, covers the procedure for a first time installation, if you 
are updating an existing installation and you are interested in keeping your 
data, you should read the UPGRADE.md included with this package.
 
## 1 - Requirements

Requirements to deploy and run Echofish include a syslog service logging to a 
mysql database backend and a web server for the frontend.

### 1.1 - Web server

Echofish frontend should run well on most web servers capable of serving PHP 
content.

PHP with the mbstring extension, the MySQL PDO extension and PCRE-support is pretty much the only hard requirement for the web server environment.

For lighter footprint, nginx with php-fpm is recommended.

### 1.2 - MySQL database

Echofish backend is developed on MariaDB 10.0.25 (and works fine on other 
reasonably recent versions too).

#### 1.2.1 - MariaDB Regexp Functions

Echofish is dependent on MariaDB 10.0.5+ for PCRE pattern matching.

#### 1.2.2 - MariaDB Event Scheduler and BLACKHOLE Storage Engine

Make sure the BLACKHOLE engine is enabled in MariaDB as a plugin, and that
the built-in Event Scheduler is started, by adding a these directives in
the database server config, usually `/etc/my.cnf` or `/etc/mysql/my.cnf`:

```
[mysqld]
event_scheduler=ON
plugin-load=BLACKHOLE=ha_blackhole.so
blackhole=FORCE
```

After setting up the event_scheduler you should also reload the mysql server
process, i.e. `service mysqld reload` or equivalent.

### 1.3 - Syslog

Echofish has been tested to work well with syslog-ng and rsyslog, but other 
syslog daemons that support MySQL logging should do it.

* Check the pages [syslog-ng concentrator in OpenBSD](contrib/OpenBSD-syslog-concentrator.md) 
and [rsyslog config](contrib/rsyslog-echofish.conf) for working examples.

## 2 - Obtaining Echofish

Download and unpack [latest Echofish](https://github.com/echothrust/echofish/archive/master.tar.gz) 
into your web root (htdocs).

## 3 - Installation

### 3.1 - Echofish backend

Connect to your mysql server as administrator, create a database for Echofish 
and grant privileges to a user connecting from your web server's IP:

```sql
CREATE DATABASE ETS_echofish CHARACTER SET utf8 COLLATE utf8_unicode_ci;
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish'@'127.0.0.1' IDENTIFIED BY 'place-your-passwd-here' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

From the extracted `echofish` directory, import the provided schema files into 
the database:

```sh
cd echofish/
mysql -u root -p ETS_echofish < schema/00_echofish-schema.sql
mysql -u root -p ETS_echofish < schema/echofish-dataonly.sql
mysql -u root -p ETS_echofish < schema/echofish-functions.sql
mysql -u root -p ETS_echofish < schema/echofish-procedures.mariadb10.sql
mysql -u root -p ETS_echofish < schema/echofish-triggers.sql
mysql -u root -p ETS_echofish < schema/echofish-events.sql
```

### 3.2 - Echofish frontend

#### 3.2.1 - Yii assets directory

The web-ui expects a directory in `htdocs/assets`, owned by the user running php-fpm.

#### 3.2.2 - Database config

Database credentials are expected in `htdocs/protected/config/db.php`:

```
cp htdocs/protected/config/db-sample.php htdocs/protected/config/db.php
```

Edit `htdocs/protected/config/db.php` and change the values to reflect the backend database configuration:

```php
return array(
			'connectionString' => 'mysql:host=127.0.0.1;dbname=ETS_echofish',
			'emulatePrepare' => true,
			'username' => 'echofish',
			'password' => 'place-your-passwd-here',
			'charset' => 'utf8',
		);
```

### 3.3 - Echofish reporting

Specify your mailserver in `htdocs/protected/config/mail.php` (fqdn/ipaddr of 
your outgoing smtp in place of 'localhost' in Host key):

```php
return array(
    'viewPath' => 'application.views.email',
    'layoutPath' => 'application.views.layouts',
    'baseDirPath' => 'webroot.images.mail',
    'layout' => 'mail',
    'Host'=>'localhost',
```

Schedule a daily summary of Abuser Incidents to run every night at 00:30 through cron: 

```
30 0 * * * cd /var/www/echofish/htdocs && /usr/local/bin/php-5.6 cron.php alert abuser --email=YOUR-EMAIL --interval=1440 --zero=1
```

On the cronjob above, make sure you change the paths to match your 
configuration and replace 'YOUR-EMAIL' with your email address.

## 4 - Secure frontend

Log on to the web frontend `https://your-server-ip/echofish/htdocs/index.php` 
with default username/password `admin`/`admin`, and change default credentials 
from the 'Settings' module.

## 5 - Installation complete

You may now view your syslog events using a web browser.
Start taking advantage of Echofish, by creating "whitelist" rules from the 
web-frontend to auto-acknowledge events in the backend. This way, recurring 
events that are "whitelisted" as normal system behaviour are auto-archived and 
will not appear as noise, leaving the sysadmin with important events only to 
acknowledge 

(: Enjoy.

## 6 - Troubleshooting

* Error 500, CAssetManager.basePath "/var/www/echofish/htdocs/assets" is 
invalid.
```
mkdir -p /var/www/echofish/htdocs/assets
chown www-data /var/www/echofish/htdocs/assets
```

* MySQL's builtin event scheduler is expected to be enabled, or else 
whitelisting and abuser incident correlation will not work consistently. Make 
sure you have `event_scheduler=ON` in the `[mysqld]` section of `/etc/my.cnf` 
or `/etc/mysql/my.cnf`.

* Recent versions of MariaDB ship the BLACKHOLE engine as separate plugin, which
is disabled by default. To fix this issue on an existing MariaDB installation:
```
INSTALL PLUGIN BLACKHOLE SONAME 'ha_blackhole.so';
ALTER TABLE ETS_echofish.archive_bh ENGINE BLACKHOLE;
```

* It has been reported that, in some cases, archive logs are not successfully 
rotated. This can be verified by manually invoking the rotation procedure
with `CALL eproc_rotate_archive();` and receiving **ERROR 1665 (HY000)** from
MariaDB. A workaround for this issue is to adjust the daemon config to include
`binlog_format=row`, under the `[mysqld]` section of my.cnf

