## Install Echofish On Ubuntu 16.04 LTS

The following guide walks you through the installation of Echofish on Ubuntu 16.04 LTS with rsyslog + apache + php-fpm.

### Requirements

Starting from a Ubuntu Server minimal install, we need a few additional dependencies.
This guide will use Apache as an example but any httpd supported by php will do.

#### Packages installation

Install required packages:

```sh
sudo apt install php php-mysql php-mbstring php-xml php-fpm mariadb-server mariadb-client apache2 libapache2-mod-php rsyslog rsyslog-mysql curl ca-certificates
```

You can skip the rsyslog-mysql initiated database install, it won't be used.

#### Echofish sources

Download and extract [Echofish](https://github.com/echothrust/echofish)

```sh
cd /var/www
curl -L https://github.com/echothrust/echofish/archive/master.tar.gz | sudo tar zx
sudo mv echofish-master echofish
```

#### MariaDB

##### MariaDB event scheduler and BLACKHOLE Storage Engine

Make sure the BLACKHOLE engine is enabled in MariaDB as a plugin, and that
the built-in Event Scheduler is started, by adding these directives in
the database server config, `/etc/mysql/mariadb.conf.d/50-server.cnf`:

```
[mysqld]
event_scheduler=ON
plugin-load=BLACKHOLE=ha_blackhole.so
blackhole=FORCE
```

##### MariaDB first time start-up

Restart MariaDB to apply the storage engine changes:

```sh
sudo systemctl restart mysql.service
```

##### Set up MariaDB root password
By default there is no root password, execute the following, changing {{{root-pass-here}}}:
```sh
mysqladmin -u root password '{{{root-pass-here}}}'
```

##### Create and configure a database

Run `mysql -p -u root` to connect to your mysql server as administrator and execute the following SQL (change {{{echofish-pass-here}}} as you see fit):

```sql
CREATE DATABASE ETS_echofish CHARACTER SET utf8 COLLATE utf8_unicode_ci;
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish'@'localhost' IDENTIFIED BY '{{{echofish-pass-here}}}' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

##### Import database schema

Import the provided schema files into the database:

```sh
cd /var/www/echofish
mysql ETS_echofish < schema/00_echofish-schema.sql
mysql ETS_echofish < schema/echofish-dataonly.sql
mysql ETS_echofish < schema/echofish-functions.sql
mysql ETS_echofish < schema/echofish-procedures.mariadb10.sql
mysql ETS_echofish < schema/echofish-triggers.sql
mysql ETS_echofish < schema/echofish-events.sql
```

### Echofish setup

This section describes how to configure Echofish for your setup.


#### Configure database access

Copy the echofish configuration sample file located in `echofish/htdocs/protected/config` and change the values to reflect your setup (change {{{echofish-pass-here}}} to match):

```sh
cd /var/www/echofish/htdocs/protected/config/
cp db-sample.php db.php
vi db.php
```

```php
	'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=ETS_echofish',
			'emulatePrepare' => true,
			'username' => 'echofish',
			'password' => '{{{echofish-pass-here}}}',
			'charset' => 'utf8',
		),
```

#### Email reporting

If you intend to receive e-mail alerts about syslog events (via the abuser module), make sure Echofish is configured to send reports:

In order to change the default sender email address for the reports (the From field), simply edit the `echofish/htdocs/protected/config/console.php` and change the following line (located at the end of the file):

```php
    'params'=>array(
      'adminEmail'=>'your@email.address',
    ),
```

You may specify a mailserver other than localhost in `echofish/htdocs/protected/config/mail.php` (fqdn/ipaddr of your outgoing smtp in Host key):

```php
return array(
    'viewPath' => 'application.views.email',
    'layoutPath' => 'application.views.layouts',
    'baseDirPath' => 'webroot.images.mail',
    'layout' => 'mail',
    'Host'=>'localhost',
```

If you are unsure, leave 'localhost' to deliver to the local MTA. Configuring cron for report generation is outside the scope of this recipe, because report data is only produced after configuring the Abuser module. After completing setup learn more about Abuser on the module's help pages within the webui.

### Services

#### rsyslog-mysql

We are going to use rsyslog as a collector.
Remove the standard rsyslog-mysql config:
```sh
sudo rm /etc/rsyslog.d/mysql.conf
```

Create the file `/etc/rsyslog.d/echofish.conf` as follows (change {{{echofish-pass-here}}} to match your setup):

```
# /etc/rsyslog.d/echofish.conf

# Provides UDP syslog reception
$ModLoad imudp
$UDPServerRun 514

# Provides TCP syslog reception
$ModLoad imtcp
$InputTCPServerRun 514

# sql + rules for rsyslog integration with Echofish
# Load rsyslog MySQL plugin
$ModLoad ommysql.so

# Generic template
template(name="dbFormat" type="string" option.sql="on"
        string="INSERT INTO archive_bh (host, facility, priority, level, received_ts, program, msg,tag) VALUES ( '%fromhost-ip%', '%syslogfacility%', '%syslogpriority%','%syslogseverity%', '%timereported:::date-mysql%', TRIM('%programname%'), TRIM('%msg%'), '%syslogtag%' )"
)

*.* :ommysql:127.0.0.1,ETS_echofish,echofish,{{{echofish-pass-here}}};dbFormat
```

This will load the mysql shared object that was installed with the "rsyslog-mysql" package and will log syslog in table 'archive_bh' of mysql database 'ETS_echofish'.

#### php

In the `[Date]` section of `/etc/php/7.0/apache2/php.ini` set PHP date.timezone (a list of available timezone settings can be found [here](http://uk.php.net/manual/en/timezones.php)). Replace with your server timezone:

```
date.timezone = Europe/Athens
```

### Permissions

Set unix permissions:

```sh
# set write permissions for php
mkdir /var/www/echofish/htdocs/assets
chown -R www-data:www-data /var/www/echofish/htdocs/assets
```

### Test your installation

Enable and restart rsyslog daemon:

```sh
sudo systemctl enable rsyslog.service
sudo systemctl restart rsyslog.service
```

Start apache service:

```sh
sudo systemctl enable apache2.service
sudo systemctl restart apache2.service
```

Create a symlink for echofish:

```sh
cd /var/www/html
ln -s ../echofish/htdocs echofish
```

Point your browser to `http://{{{echofish-host-here}}}/echofish/` and login with uid/pwd admin/admin.
