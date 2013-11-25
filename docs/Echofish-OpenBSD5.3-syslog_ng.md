## Install Echofish On OpenBSD 5.3

The following guide walks you through the installation of Echofish on OpenBSD 5.3 with syslog-ng+nginx+php_fpm.

### Requirements

Starting from a fresh installation of 5.3, Echofish has a few additional requirements:

#### Packages

We are going to use syslog-ng as a collector and MySQL as backend database. php-fpm will be used to run Echofish:

```sh
export PKG_PATH=ftp://ftp.openbsd.org/pub/OpenBSD/$(uname -r)/packages/$(uname -m)
pkg_add -vvi syslog-ng libdbi-drivers-mysql mysql-server 
pkg_add -vvi php-fpm.5.3.21 php-pdo_mysql-5.3.21 pecl-APC-3.1.9p3 pcre-8.31
```

#### Yii Framework

Download and extract [Yii Framework](https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.tar.gz)

```sh
ftp https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.tar.gz
tar zxf  yii-1.1.14.f0fee9.tar.gz -C /var/www/
ln -sf yii-1.1.14.f0fee9 /var/www/yii
```

#### Echofish sources

Finally, download and extract Echofish into your desired folder (the following example extracts into /var/www/htdocs)

```sh
ftp https://github.com/echothrust/echofish/archive/master.tar.gz
tar -zxf master.tar.gz -C /var/www/htdocs/
ln -s /var/www/htdocs/echofish-master /var/www/htdocs/echofish
chown -R www.www /var/www/htdocs/echofish/assets/
```

#### Create and configure a database 

Configure MySQL to start for the first time and start the service:

```
mysql_install_db
/etc/rc.d/mysqld -f start
```

Run `mysql -p -u root` to connect to your mysql server as administrator and execute the following SQL (change {{{echofish-pass-here}}} as you see fit):

```sql
CREATE DATABASE ETS_echofish
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish'@'127.0.0.1' IDENTIFIED BY '{{{echofish-pass-here}}}' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

#### Import database schema

Import the provided schema files into the database:

```sh
cd /var/www/htdocs/echofish
mysql ETS_echofish < protected/data/00_echofish-schema.sql
mysql ETS_echofish < protected/data/echofish-dataonly.sql
mysql ETS_echofish < protected/data/echofish-functions.sql
mysql ETS_echofish < protected/data/echofish-procedures.sql
mysql ETS_echofish < protected/data/echofish-triggers.sql
mysql ETS_echofish < protected/data/echofish-events.sql
```

### Echofish setup

This section describes how to configure Echofish for your setup.

#### Configure database access

Edit the echofish configuration file located at `/var/www/htdocs/echofish/protected/config/main.php` and find the following section and change the values to reflect your setup:

```php
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=ETS_echofish',
			'emulatePrepare' => true,
			'username' => 'dbuser',
			'password' => 'dbpass',
			'charset' => 'utf8',
		),
```

#### Point yii location to echofish

If you followed the guide, yii should be extracted in `/var/www/yii/`. We need to change the `$yii` variable path in `/var/www/htdocs/echofish/index.php` to point to the correct location for the Yii Framework:

FROM

```php
$yii=dirname(__FILE__).'/../yii/framework/yii.php'; 
```

INTO

```php
$yii=dirname(__FILE__).'/../../yii/framework/yii.php'; 
```

### Services

This section described setting up all complementing services, such as a syslog concentrator, a web server, etc.

#### Configure nginx for php-fpm

Uncomment the following section in `/etc/nginx/nginx.conf`:

```
        #location ~ \.php$ {
        #    root           /var/www/htdocs;
        #    fastcgi_pass   127.0.0.1:9000;
        #    fastcgi_index  index.php;
        #    fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        #    include        fastcgi_params;
        #}

```

This will configure nginx to serve PHP pages through php-fastcgi.

#### Configure php date.timezone

In the `[Date]` section of your `/etc/php-5.3.ini` set PHP’s date.timezone (a list of available timezone settings can be found [here](http://uk.php.net/manual/en/timezones.php)). Replace with your server's timezone:

```
date.timezone = Europe/Athens
```

#### Syslog-ng configuration

In order for syslog-ng to get all logs into the database tables and avoid logging erroneous entries, a certain configuration is required. Modify `/etc/syslog-ng/syslog-ng.conf` like the following:

```
# make sure we only log source IPv4 address
options {
        use_dns(no);
        create_dirs(no);
        keep_hostname(no);
        use_fqdn(no);
        check_hostname(no);
        stats_freq(0);
};

# in case of openbsd you can use those to replace default syslog
# if you run other chrooted services that log to syslog you should 
# include them here, e.g. unix-dgram ("/var/nsd/dev/log");
source s_local {
        unix-dgram ("/dev/log");
        unix-dgram ("/var/empty/dev/log");
        unix-dgram ("/var/www/dev/log");
        internal();
};

source s_net {
        udp(port(514));
#      tcp(port(514));
};


log { source(s_net); destination(d_mysql); };
log { source(s_local); destination(d_mysql_local); };

destination d_mysql {
        sql(
                type(mysql)
                host("DATABASEHOST") username("USERNAME") password("PASSWORD")
                database("ETS_echofish")
                table("archive_bh") 
                columns("host", "facility", "priority", "level", "received_ts", "program", "msg","pid","tag")
                values("$HOST", "$FACILITY_NUM", "$PRIORITY","$LEVEL_NUM", "$YEAR-$MONTH-$DAY $HOUR:$MIN:$SEC", "$PROGRAM", "$MSG","$PID", "$TAG" )
        );
};

destination d_mysql_local {
        sql(
                type(mysql)
                host("DATABASEHOST") username("USERNAME") password("PASSWORD")
                database("ETS_echofish")
                table("archive_bh") 
                columns("host", "facility", "priority", "level", "received_ts", "program", "msg","pid","tag")
# change the following '127.0.0.1' with the ip of the concentrator
                values("127.0.0.1", "$FACILITY_NUM", "$PRIORITY","$LEVEL_NUM", "$YEAR-$MONTH-$DAY $HOUR:$MIN:$SEC", "$PROGRAM", "$MSG","$PID", "$TAG" )
        );
};

```


#### OpenBSD service startup

Now make sure the required services are started at system boot by updating the file `/etc/rc.conf.local` to include the newly installed package daemons

```sh
syslogd_flags=NO
nginx_flags=""
pkg_scripts="syslog_ng mysqld php_fpm"
```

Start the remaining services
  
```sh
/etc/rc.d/syslogd stop
/etc/rc.d/syslog_ng start 
/etc/rc.d/php_fpm start
/etc/rc.d/nginx start
```

### Test your installation

Point your browser to `http://echofish_host/echofish/` and login with uid/pwd admin/admin.