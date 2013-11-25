## Install Echofish On CentOS 6.4

The following guide walks you through the installation of Echofish on CentOS 6.4 with rsyslog + nginx + php_fpm.

### Requirements

Starting from a CentOS minimal install, we need a few additions:

#### Packages installation

Download and install the rpm for Extra Packages for Enterprise Linux (EPEL) for nginx:

```sh
curl -L -O http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
yum -y install epel-release-6-8.noarch.rpm
```

Install required packages:

```sh
yum -y install rsyslog-mysql nginx php-fpm mysql-server php-pdo php-mysql
```

#### Yii Framework

Download and extract [Yii Framework](http://www.yiiframework.com/):

```sh
cd ; curl -L -O https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.tar.gz
cd /usr/share/nginx/
tar -zxvf ~/yii-1.1.14.f0fee9.tar.gz
ln -sf yii-1.1.14.f0fee9 yii
```

#### Echofish sources

Download and extract [Echofish](https://github.com/echothrust/echofish)

```sh
cd /usr/share/nginx/html
curl -L https://github.com/echothrust/echofish/archive/master.tar.gz | tar zx
mv echofish-master echofish
```

#### MySQL

Echofish requires MySQL's builtin scheduler to be enabled, so add `event_scheduler=ON` in the `[mysqld]` section of `/etc/my.cnf`.
Next, you may configure & start MySQL as the backend database:

```sh
chkconfig mysqld on
service mysqld start
/usr/bin/mysql_secure_installation
```

#### Create and configure a database 

Run `mysql -p -u root` to connect to your mysql server as administrator and execute the following SQL (change {{{echofish-pass-here}}} as you see fit):

```sql
CREATE DATABASE ETS_echofish;
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish'@'localhost' IDENTIFIED BY '{{{echofish-pass-here}}}' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

#### Import database schema

Import the provided schema files into the database:

```sh
cd /usr/share/nginx/html/echofish/protected/data
for i in 00_echofish-schema echofish-dataonly echofish-functions echofish-procedures echofish-triggers echofish-events ;
do
mysql -u echofish -p ETS_echofish < $i.sql || exit;
done
```

### Echofish setup

This section describes how to configure Echofish for your setup.

#### Permissions & SELinux

Set unix permissions and selinux contexts:

```sh
# set write permissions for php-fpm
chown -R apache.apache /usr/share/nginx/html/echofish/assets
chcon -R -u system_u -r object_r -t httpd_sys_content_t /usr/share/nginx/html/echofish
chcon -R -u system_u -r object_r -t httpd_sys_content_rw_t /usr/share/nginx/html/echofish/assets
```


#### Configure database access

Edit the echofish configuration file located at `/usr/share/nginx/html/echofish/protected/config/main.php` and find the following section and change the values to reflect your setup (change {{{echofish-pass-here}}} to match):

```php
    	'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=ETS_echofish',
			'emulatePrepare' => true,
			'username' => 'echofish',
			'password' => '{{{echofish-pass-here}}}',
			'charset' => 'utf8',
		),
```

#### Point yii location to echofish

Change the `$yii` variable path to point to the correct location for the Yii Framework in `/usr/share/nginx/html/echofish/index.php`

FROM

```php
$yii=dirname(__FILE__).'/../yii/framework/yiilite.php'; 
```

INTO

```php
$yii=dirname(__FILE__).'/../../yii/framework/yiilite.php'; 
```

### Services

#### rsyslog-mysql

We are going to use rsyslog as a collector; Create the file `/etc/rsyslog.d/echofish.conf` as follows (change {{{echofish-pass-here}}} to match your setup): 

```
# /etc/rsyslog.d/echofish.conf
# sql + rules for rsyslog integration with Echofish

# Load rsyslog MySQL plugin
$ModLoad ommysql.so

# Generic template
$template dbFormat,"INSERT INTO archive_bh (host, facility, priority, level, received_ts, program, msg,pid,tag) VALUES ( '%fromhost-ip%', '%syslogfacility%', '%syslogpriority%','%syslogseverity%', '%timereported:::date-mysql%', TRIM('%programname%'), TRIM('%msg%'),'', '%syslogtag%' );\n",sql

# Specific template for loghost (127.0.0.1)
# To avoid logging as 127.0.0.1 uncomment the following lines and change A.B.C.D to the loghost's IP addr.
#$template dbFormatLocal,"INSERT INTO archive_bh (host, facility, priority, level, received_ts, program, msg,pid,tag) VALUES ( 'A.B.C.D', '%syslogfacility%', '%syslogpriority%','%syslogseverity%', '%timereported:::date-mysql%', TRIM('%programname%'), TRIM('%msg%'),'', '%syslogtag%' );\n",sql
#if $fromhost-ip != '127.0.0.1' then :ommysql:127.0.0.1,ETS_echofish,echofish,{{{echofish-pass-here}}};dbFormat
#& ~
#if $fromhost-ip == '127.0.0.1' then :ommysql:127.0.0.1,ETS_echofish,echofish,{{{echofish-pass-here}}};dbFormat

# If you enabled loghost template above, comment-out the following line.
*.* :ommysql:127.0.0.1,ETS_echofish,echofish,{{{echofish-pass-here}}};dbFormat
```

This will load the mysql shared object that was installed with the "rsyslog-mysql" package and will log syslog in table 'archive_bh' of mysql database 'ETS_echofish'.

Restart rsyslog daemon: `service rsyslog restart`

#### nginx

Adapt the `location /` section of your `/etc/nginx/conf.d/default.conf` to:

```
        root   /usr/share/nginx/html;
        index  index.php index.html index.htm;
```

Uncomment the `location ~` section in the same file and edit it to match the example bellow:

```
    location ~ \.php$ {
        root           html;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
```

This will configure nginx to serve php content through the php-fastcgi.

Start nginx service:

```sh
chkconfig nginx on
service nginx start
```

#### php-fpm

In the `[Date]` section of your `/etc/php.ini` set PHP’s date.timezone (a list of available timezone settings can be found [here](http://uk.php.net/manual/en/timezones.php)). Replace with your server's timezone:

```
date.timezone = Europe/Athens
```

Start php-fpm service:

```sh
chkconfig php-fpm on
service php-fpm start
```

#### iptables

Below will open access to HTTP port 80 via local firewall: Edit `/etc/sysconfig/iptables` and add a rule for 80 in the rules section

```
# allow incoming connections to port 80 on all interfaces
-A INPUT -m state --state NEW -m tcp -p tcp --dport 80 -j ACCEPT
```

Then restart the service to enable the new rule:

```sh
service iptables restart
```

### Test your installation

Point your browser to `http://{{{echofish-host-here}}}/echofish/` and login with uid/pwd admin/admin.