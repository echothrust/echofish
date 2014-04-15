## Install Echofish On OpenBSD

The following guide walks you through the installation of Echofish on OpenBSD 5.4 with syslog-ng+nginx+php_fpm.

### Requirements

Starting from a fresh installation of 5.4 release, Echofish has a few additional requirements:

#### Packages

We are going to use syslog-ng as a collector and MySQL as backend database. php-fpm will be used to run Echofish:

```sh
export PKG_PATH=ftp://ftp.openbsd.org/pub/OpenBSD/$(uname -r)/packages/$(uname -m)
pkg_add -vvi syslog-ng libdbi-drivers-mysql mysql-server 
pkg_add -vvi php-fpm.5.3.27 php-pdo_mysql-5.3.27 pecl-APC-3.1.9p3 pcre-8.33
```

#### Echofish sources

Finally, download and extract Echofish into your desired folder (the following example extracts into /var/www/htdocs)

```sh
ftp https://github.com/echothrust/echofish/archive/master.tar.gz
tar -zxf master.tar.gz -C /var/www/
ln -s /var/www/echofish-master/htdocs /var/www/htdocs/echofish
chown -R www.www /var/www/htdocs/echofish/assets/
```

#### Install MySQL UDF

Install [lib_mysql_udf_preg](https://github.com/mysqludf/lib_mysqludf_preg/), which is used for PCRE pattern matching, following [its guide](https://github.com/mysqludf/lib_mysqludf_preg/blob/lib_mysqludf_preg-1.2-rc2/INSTALL):

```
ftp https://github.com/mysqludf/lib_mysqludf_preg/archive/lib_mysqludf_preg-1.2-rc2.tar.gz
tar zxf lib_mysqludf_preg-1.2-rc2.tar.gz
cd lib_mysqludf_preg-lib_mysqludf_preg-1.2-rc2
./configure
make
make install
make MYSQL="mysql -u root -p" installdb
```

Note: The `installdb` target of make(1) may fail to load lib_mysql_udf_preg with `undefined symbol: my_thread_stack_size`. Read [Can't open shared library lib_mysqludf_preg.so](https://github.com/mysqludf/lib_mysqludf_preg/issues/13).

#### Create and configure a database 

Echofish requires MySQL's builtin scheduler to be enabled, so add `event_scheduler=ON` in the `[mysqld]` section of `/etc/my.cnf`.

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
cd /var/www/echofish-master
mysql ETS_echofish < schema/00_echofish-schema.sql
mysql ETS_echofish < schema/echofish-dataonly.sql
mysql ETS_echofish < schema/echofish-functions.sql
mysql ETS_echofish < schema/echofish-procedures.sql
mysql ETS_echofish < schema/echofish-triggers.sql
mysql ETS_echofish < schema/echofish-events.sql
```

### Echofish setup

This section describes how to configure Echofish for your setup.

#### Configure database access

Database credentials are expected in `/var/www/htdocs/echofish/protected/config/db.php`:

```
cp /var/www/htdocs/echofish/protected/config/db-sample.php /var/www/htdocs/echofish/protected/config/db.php
```

Edit `/var/www/htdocs/echofish/protected/config/db.php` and change the values to reflect your setup:

```php
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=ETS_echofish',
			'emulatePrepare' => true,
			'username' => 'dbuser',
			'password' => 'dbpass',
			'charset' => 'utf8',
		),
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