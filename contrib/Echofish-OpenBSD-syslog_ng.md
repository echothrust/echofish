## Install Echofish On OpenBSD

The following guide walks you through the installation of Echofish on 
OpenBSD 6.0 with syslog-ng+nginx+php_fpm.

### Requirements

Starting from a fresh installation of 6.0 release, Echofish has a few
 additional requirements:

#### Packages

We are going to use syslog-ng as a collector and MariaDB as backend database. 
php-fpm will be used to run Echofish:

```sh
export PKG_PATH=http://ftp.openbsd.org/pub/OpenBSD/$(uname -r)/packages/$(uname -m)
pkg_add -vvi syslog-ng libdbi-drivers-mysql mariadb-server mariadb-client 
pkg_add -vvi nginx-1.10.1 php-5.6.23p0 php-pdo_mysql-5.6.23p0 
```

#### Echofish sources

Finally, download and extract Echofish into your desired folder (the following 
example extracts into /var/www/htdocs)

```sh
ftp https://github.com/echothrust/echofish/archive/master.tar.gz
tar -zxf master.tar.gz -C /var/www/
ln -s /var/www/echofish-master/htdocs /var/www/htdocs/echofish
install -d -g www -o www /var/www/htdocs/echofish/assets/
```

#### Create and configure a database 

Echofish requires MySQL's builtin scheduler to be enabled, so add 
`event_scheduler=ON` in the `[mysqld]` section of `/etc/my.cnf`.

Configure MySQL to start for the first time and start the service:

```
mysql_install_db
rcctl -f start mysqld
```

Run `mysql -p -u root` to connect to your mysql server as administrator and 
execute the following SQL (change {{{echofish-pass-here}}} as you see fit):

```sql
INSTALL PLUGIN BLACKHOLE SONAME 'ha_blackhole.so';
CREATE DATABASE ETS_echofish CHARACTER SET utf8 COLLATE utf8_unicode_ci;
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
mysql ETS_echofish < schema/echofish-procedures.mariadb10.sql
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

#### Email reporting

If you intend to receive e-mail alerts about syslog events (via the abuser 
module), make sure Echofish is configured to send reports:

In order to change the default sender email address for the reports (the From 
field), simply edit the `/var/www/htdocs/protected/config/console.php` and 
change the following line (located at the end of the file):

```php
    'params'=>array(
      'adminEmail'=>'your@email.address',
    ),    
```

You may specify a mailserver other than localhost in 
`/var/www/htdocs/protected/config/mail.php` (fqdn/ipaddr of your outgoing smtp 
in Host key):

```php
return array(
    'viewPath' => 'application.views.email',
    'layoutPath' => 'application.views.layouts',
    'baseDirPath' => 'webroot.images.mail',
    'layout' => 'mail',
    'Host'=>'localhost',
```

If you are unsure, leave 'localhost' to deliver to the local MTA. Configuring 
cron for report generation is outside the scope of this guide, because report 
data is only produced after configuring the Abuser module. After completing 
setup learn more about Abuser on the module's help pages within the webui.

### Services

This section described setting up all complementing services, such as a syslog 
concentrator, a web server, etc.

#### Configure nginx for php-fpm

Uncomment the following section in `/etc/nginx/nginx.conf`:

```
        #location ~ \.php$ {
        #    try_files      $uri $uri/ =404;
        #    fastcgi_pass   unix:run/php-fpm.sock;
        #    fastcgi_index  index.php;
        #    fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        #    include        fastcgi_params;
        #}

```

This will configure nginx to serve PHP pages through php-fastcgi.

#### Configure php date.timezone

In the `[Date]` section of your `/etc/php-5.3.ini` set PHP’s date.timezone (a 
list of available timezone settings can be found 
[here](http://uk.php.net/manual/en/timezones.php)). Replace with your server's 
timezone:

```
date.timezone = Europe/Athens
```

#### Syslog-ng configuration

In order for syslog-ng to get all logs into the database tables and avoid 
logging erroneous entries, a certain configuration is required. Modify 
`/etc/syslog-ng/syslog-ng.conf` like the following:

```
@version: 3.7

# make sure we only log source IPv4 address
options {
        use_dns(no);
        create_dirs(no);
        keep_hostname(no);
        use_fqdn(no);
        check_hostname(no);
        stats_freq(0);
};

# This source includes internal syslog_ng messages and local system logs, as
# forwarded by OpenBSD's stock syslogd(8) 
source s_local{ 
        udp(ip("127.0.0.1") port(514));
        internal(); 
};

# This source is for logs coming to the concentrator from other hosts.
# Since 514/UDP is already occupied by syslogd(8) either listen on high port
# or specify LAN ip address to listen on default port (514) on the specific
# LAN interface
source s_net {

        udp(port(60514));
# or   udp(ip("lan.ip.add.res") port(514));
#      tcp(port(514));
};

# change the following '127.0.0.1' with the ip of the concentrator
rewrite r_local_sethost { set("127.0.0.1", value("HOST"));};

log { source(s_net); destination(d_mysql); };
log { source(s_local); rewrite(r_local_sethost); destination(d_mysql); };

destination d_mysql {
        sql(
                type(mysql)
                host("127.0.0.1") username("echofish") password("{{{echofish-pass-here}}}")
                database("ETS_echofish")
                table("archive_bh") 
                columns("host", "facility", "priority", "level", "received_ts", "program", "msg","pid","tag")
                values("$HOST", "$FACILITY_NUM", "$PRIORITY","$LEVEL_NUM", "$YEAR-$MONTH-$DAY $HOUR:$MIN:$SEC", "$PROGRAM", "$MSG","$PID", "$TAG" )
        );
};

```

#### Stock OpenBSD syslogd configuration

To additionally forward logs from the concentrator's OS, the syslogd(8) daemon
is configured to relay all messages to the syslog-ng daemon.

Add the following lines to `/etc/syslog.conf`:

```
# Log all messages to local syslog-ng listening on 127.0.0.1
*.*    @127.0.0.1
```

#### OpenBSD service startup

Now make sure the required services are started at system boot:

```sh
rcctl enable mysqld syslog_ng php56_fpm nginx
```

Start the remaining services
  
```sh
rcctl start syslog_ng
rcctl start php56_fpm
rcctl start nginx
```

Restart local syslogd:

```sh
rcctl restart syslogd
```

### Test your installation

Point your browser to `http://echofish_host/echofish/` and login with uid/pwd 
admin/admin.
