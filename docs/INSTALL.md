## INSTALLING Echofish

### 1 - Requirements

Requirements to deploy and run Echofish include a syslog service logging to a mysql database backend and a web server for the frontend.

#### 1.1 - Web server

Echofish frontend should run well on most web servers capable of serving PHP content. For lighter footprint, nginx with php-fpm is recommended.

#### 1.2 - MySQL database

Echofish backend was developed on MySQL 5.1.68, but other reasonably recent versions should work as well.

##### 1.2.1 - MySQL User Defined Functions

Echofish is dependent on [lib_mysql_udf_preg](https://github.com/mysqludf/lib_mysqludf_preg/) for PCRE pattern matching.

#### 1.3 - Syslog

Echofish has been tested to work well with syslog-ng and rsyslog, but other syslog daemons that support MySQL logging should do it.

* Check the page [syslog-ng concentrator in OpenBSD](OpenBSD-syslog-concentrator.md) for a working example.

#### 1.4 - Yii

Echofish frontend is written in PHP, using Yii. To run the PHP frontend, it is required to download and extract [Yii Framework](https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.tar.gz) on the web server, preferably outside the web root (htdocs) directory. 

### 2 - Obtaining Echofish

Download and unpack [latest Echofish](https://github.com/echothrust/echofish/archive/master.tar.gz) into your web root (htdocs).

### 3 - Installation

#### 3.1 - Echofish backend

Connect to your mysql server as administrator, create a database for Echofish and grant privileges to a user connecting from your web server's IP:

```sql
CREATE DATABASE ETS_echofish
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish'@'127.0.0.1' IDENTIFIED BY 'place-your-passwd-here' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

From the extracted `echofish` directory, import the provided schema files into the database:

```sh
cd echofish/
mysql ETS_echofish < protected/data/00_echofish-schema.sql
mysql ETS_echofish < protected/data/echofish-dataonly.sql
mysql ETS_echofish < protected/data/echofish-functions.sql
mysql ETS_echofish < protected/data/echofish-procedures.sql
mysql ETS_echofish < protected/data/echofish-triggers.sql
mysql ETS_echofish < protected/data/echofish-events.sql
```

For events to run, make sure tou set `event_scheduler=on` somewhere under the [mysqld] section in the default mysql config file, usually /etc/my.cnf

#### 3.2 - Echofish frontend

Edit the echofish configuration file located at `echofish/protected/config/main.php` and find the following section and change the values to reflect the backend database configuration:

```php
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=ETS_echofish',
			'emulatePrepare' => true,
			'username' => 'dbuser',
			'password' => 'dbpass',
			'charset' => 'utf8',
		),
```

Make sure the `$yii` variable path in `echofish/index.php` points to the location where Yii Framework was extracted.

### 4 - Secure frontend

Log on to the web frontend with default username/password `admin`/`admin`, and change default credentials from the 'Settings' module.

### 5 - Installation complete

You may now view your syslog events using a web browser.
To take full advantage of Echofish, create "whitelist" rules from the web-frontend to auto-acknowledge events in the backend. This way, recurring events that are "whitelisted" as normal system behaviour are auto-archived and will not appear as noise, leaving the sysadmin with important events only to acknowledge (: Enjoy.

### 6 - Troubleshooting

* MySQL's builtin event scheduler is expected to be enabled, or else whitelisting will not work consistently. Make sure you have `event_scheduler=ON` in the `[mysqld]` section of `/etc/my.cnf`.
