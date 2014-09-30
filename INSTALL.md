## INSTALLING Echofish

### 1 - Requirements

Requirements to deploy and run Echofish include a syslog service logging to a mysql database backend and a web server for the frontend.

#### 1.1 - Web server

Echofish frontend should run well on most web servers capable of serving PHP content. For lighter footprint, nginx with php-fpm is recommended.

#### 1.2 - MySQL database

Echofish backend was developed on MySQL 5.1.68 (and works fine on other reasonably recent versions too).

##### 1.2.1 - MySQL User Defined Functions

Echofish is dependent on [lib_mysql_udf_preg](https://github.com/mysqludf/lib_mysqludf_preg/) for PCRE pattern matching.

#### 1.3 - Syslog

Echofish has been tested to work well with syslog-ng and rsyslog, but other syslog daemons that support MySQL logging should do it.

* Check the pages [syslog-ng concentrator in OpenBSD](contrib/OpenBSD-syslog-concentrator.md) and [rsyslog config](contrib/rsyslog-echofish.conf) for working examples.

### 2 - Obtaining Echofish

Download and unpack [latest Echofish](https://github.com/echothrust/echofish/archive/master.tar.gz) into your web root (htdocs).

### 3 - Installation

#### 3.1 - Echofish backend

Connect to your mysql server as administrator, create a database for Echofish and grant privileges to a user connecting from your web server's IP:

```sql
CREATE DATABASE ETS_echofish;
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish'@'127.0.0.1' IDENTIFIED BY 'place-your-passwd-here' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

From the extracted `echofish` directory, import the provided schema files into the database:

```sh
cd echofish/
mysql -u root -p ETS_echofish < schema/00_echofish-schema.sql
mysql -u root -p ETS_echofish < schema/echofish-dataonly.sql
mysql -u root -p ETS_echofish < schema/echofish-functions.sql
mysql -u root -p ETS_echofish < schema/echofish-procedures.sql
```

In case you are upgrading from an older version, this is the right time to import your backed up data (that is before you put any events/triggers in place):

```sh
# Skip these on a fresh install
mysql -u root -p ETS_echofish < /path/to/userdata.sql
mysql -u root -p ETS_echofish < /path/to/archivedata.sql
```

Finally, import the provided mysql events and triggers:

```sh
mysql -u root -p ETS_echofish < schema/echofish-triggers.sql
mysql -u root -p ETS_echofish < schema/echofish-events.sql
```

For events to run, make sure you set `event_scheduler=on` somewhere under the [mysqld] section in the default mysql config file, usually `/etc/my.cnf` or `/etc/mysql/my.cnf`. After setting up the event_scheduler you may also want to run `sudo /etc/init.d/mysql reload`.

#### 3.2 - Echofish frontend

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

#### 3.3 - Echofish reporting

Specify your mailserver in `htdocs/protected/config/mail.php` (fqdn/ipaddr of your outgoing smtp in place of 'localhost' in Host key):

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
30 0 * * * cd /var/www/echofish/htdocs && /usr/local/bin/php-5.3 cron.php alert abuser --email=YOUR-EMAIL --interval=1440 --zero=1
```

On the cronjob above, make sure you change the paths to match your configuration and replace 'YOUR-EMAIL' with your email address.

### 4 - Secure frontend

Log on to the web frontend `https://your-server-ip/echofish/htdocs/index.php` with default username/password `admin`/`admin`, and change default credentials from the 'Settings' module.

### 5 - Installation complete

You may now view your syslog events using a web browser.
Start taking advantage of Echofish, by creating "whitelist" rules from the web-frontend to auto-acknowledge events in the backend. This way, recurring events that are "whitelisted" as normal system behaviour are auto-archived and will not appear as noise, leaving the sysadmin with important events only to acknowledge (: Enjoy.

### 6 - Troubleshooting

* Error 500, CAssetManager.basePath "\var\www\echofish\htdocs\assets" is invalid. Run `mkdir -p \var\www\echofish\htdocs\assets && chown www-data \var\www\echofish\htdocs\assets`
* MySQL's builtin event scheduler is expected to be enabled, or else whitelisting and abuser incident correlation will not work consistently. Make sure you have `event_scheduler=ON` in the `[mysqld]` section of `/etc/my.cnf` or `/etc/mysql/my.cnf`.
* lib_mysql_udf_preg fails to ./configure with WARNING: ‘aclocal-1.13′. Run `aclocal && libtoolize –force && autoreconf` and try again. [lib_mysqludf_preg on CentOS 6.4](http://dragkh.wordpress.com/2013/12/18/how-to-install-mysql-10-0-6-mariadb-and-to-compile-lib_mysqludf_preg-on-centos-6-4/).
* lib_mysql_udf_preg fails to load on some MySQL versions with `undefined symbol: my_thread_stack_size`. Read [Can't open shared library lib_mysqludf_preg.so](https://github.com/mysqludf/lib_mysqludf_preg/issues/13).
