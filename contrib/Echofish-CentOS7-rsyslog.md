## Install Echofish On CentOS 7.3

The following guide walks you through the installation of Echofish on CentOS 7.3 with rsyslog + nginx + php_fpm.

### Requirements

Starting from a CentOS minimal install, we need a few additions:

#### Packages installation

Install Extra Packages for Enterprise Linux (EPEL) for nginx:

```sh
yum -y install epel-release
```

For MariaDB 10.1 create `/etc/yum.repos.d/MariaDB.repo`:

```
# MariaDB 10.1 CentOS repository list
# http://downloads.mariadb.org/mariadb/repositories/
[mariadb]
name = MariaDB
baseurl = http://yum.mariadb.org/10.1/centos7-amd64
gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
gpgcheck=1
```

Install required packages:

```sh
yum -y install rsyslog-mysql php php-pdo php-mysqlnd php-mbstring php-fpm nginx MariaDB-server MariaDB-client
```

#### Echofish sources

Download and extract [Echofish](https://github.com/echothrust/echofish)

```sh
cd /usr/share/nginx
curl -L https://github.com/echothrust/echofish/archive/master.tar.gz | tar zx
mv echofish-master echofish
```

#### MariaDB

Echofish requires MariaDB builtin scheduler to be enabled, so add `event_scheduler=ON` in the `[mysqld]` section of `/etc/my.cnf.d/server.cnf`.

Start MariaDB as the backend database:

```sh
systemctl enable mariadb.service
systemctl start mariadb.service
```

#### Create and configure a database

Run `mysql -p -u root` to connect to your mysql server as administrator and execute the following SQL (change {{{echofish-pass-here}}} as you see fit):

```sql
INSTALL PLUGIN BLACKHOLE SONAME 'ha_blackhole.so';
CREATE DATABASE ETS_echofish CHARACTER SET utf8 COLLATE utf8_unicode_ci;
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish'@'localhost' IDENTIFIED BY '{{{echofish-pass-here}}}' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

#### Import database schema

Import the provided schema files into the database:

```sh
cd /usr/share/nginx/echofish
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
cd /usr/share/nginx/echofish/htdocs/protected/config/
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

We are going to use rsyslog as a collector; Create the file `/etc/rsyslog.d/echofish.conf` as follows (change {{{echofish-pass-here}}} to match your setup): 

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
$template dbFormat,"INSERT INTO archive_bh (host, facility, priority, level, received_ts, program, msg, tag) VALUES ( '%fromhost-ip%', '%syslogfacility%', '%syslogpriority%','%syslogseverity%', '%timereported:::date-mysql%', TRIM('%programname%'), TRIM('%msg%'), '%syslogtag%' );\n",sql

*.* :ommysql:127.0.0.1,ETS_echofish,echofish,{{{echofish-pass-here}}};dbFormat
```

This will load the mysql shared object that was installed with the "rsyslog-mysql" package and will log syslog in table 'archive_bh' of mysql database 'ETS_echofish'.

#### nginx

Create the file `/etc/nginx/default.d/echofish.conf`:

```
location /echofish {
        index index.php;
        # First attempt to serve request as file, then
        # as directory, then fall back to displaying a 404.
        try_files $uri $uri/ =404;
}

location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
}
```

This will configure nginx to serve php content through the php-fastcgi.

#### php-fpm

In the `[Date]` section of your `/etc/php.ini` set PHP date.timezone (a list of available timezone settings can be found [here](http://uk.php.net/manual/en/timezones.php)). Replace with your server timezone:

```
date.timezone = Europe/Athens
```

#### iptables

Below will open access to HTTP port 80 via local firewall:

```
# allow incoming connections to port 80
firewall-cmd --zone=public --add-port=80/tcp --permanent
```

Then reload the new rules for changes to take effect:

```sh
firewall-cmd --reload
```

### Permissions & SELinux

Set unix permissions and selinux contexts:

```sh
# set write permissions for php-fpm
mkdir /usr/share/nginx/echofish/htdocs/assets
chown -R apache.apache /usr/share/nginx/echofish/htdocs/assets
chcon -R -u system_u -r object_r -t httpd_sys_content_t /usr/share/nginx/echofish/htdocs
chcon -R -u system_u -r object_r -t httpd_sys_content_rw_t /usr/share/nginx/echofish/htdocs/assets
# allow mysql connections from fpm
setsebool httpd_can_network_connect_db on
# set correct contexts for rsyslog included config
chcon system_u:object_r:syslog_conf_t:s0 /etc/rsyslog.d/echofish.conf
```

### Test your installation

Enable and restart rsyslog daemon:

```sh
systemctl enable rsyslog.service
systemctl restart rsyslog.service
```

Start nginx service:

```sh
systemctl enable nginx.service
systemctl restart nginx.service
```

Start php-fpm service:

```sh
systemctl enable php-fpm.service
systemctl restart php-fpm.service
```

Create a symlink for echofish:

```sh
cd /usr/share/nginx/html
ln -s ../echofish/htdocs echofish
```

Point your browser to `http://{{{echofish-host-here}}}/echofish/` and login with uid/pwd admin/admin.
