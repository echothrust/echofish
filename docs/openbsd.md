# DRAFT FOR Echofish-yii

## Install Echofish On OpenBSD 5.3

The following guide walks you through the installation of Echofish on OpenBSD 5.3. 

### Required packages

* Syslog-ng+mysql+nginx+php_fpm

```
export PKG_PATH=ftp://ftp.openbsd.org/pub/OpenBSD/$(uname -r)/packages/$(uname -m)
pkg_add -vvi syslog-ng libdbi-drivers-mysql mysql-server 
pkg_add -vvi php-fpm.5.3.21 php-pdo_mysql-5.3.21 pecl-APC-3.1.9p3 pcre-8.31
mysql_install_db
```

  * Start mysql `/etc/rc.d/mysqld -f start`
 
### Yii Framework

  * Download and extract [Yii Framework](https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.tar.gz)

```
ftp https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.tar.gz
tar zxf  yii-1.1.14.f0fee9.tar.gz -C /var/www/
ln -sf yii-1.1.14.f0fee9 /var/www/yii
```

### Echofish web-ui

  * Download and extract echofish into your desired folder (the following example extracts into /var/www/htdocs

```
ftp https://gitlab.echothrust.com/echothrust/echofish/download/echofish-XXX-FIXME-XXX.tgz
tar zxf echofish-XXX-FIXME-XXX.tgz -C /var/www/htdocs/
```

  * Create and configure a database on MySQL, by connecting as administrator to your mysql and executing the following set of commands.

```
CREATE DATABASE ETS_echofish
GRANT ALL PRIVILEGES ON ETS_echofish.* TO 'echofish_user'@'echofish_host' IDENTIFIED BY 'echofish_password' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

  * Import the provided schema files into the database

```
cd /var/www/htdocs/echofish
mysql ETS_echofish < protected/data/00_echofish-schema.sql
mysql ETS_echofish < protected/data/echofish-dataonly.sql
mysql ETS_echofish < protected/data/echofish-functions.sql
mysql ETS_echofish < protected/data/echofish-procedures.sql
mysql ETS_echofish < protected/data/echofish-triggers.sql
mysql ETS_echofish < protected/data/echofish-events.sql
```

  * Edit the echofish configuration file located at `/var/www/htdocs/echofish/protected/config/main.php` and find the following section and change the values to reflect your setup

```
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=ETS_echofish',
			'emulatePrepare' => true,
			'username' => 'dbuser',
			'password' => 'dbpass',
			'charset' => 'utf8',
		),
```
 
  * Edit /var/www/htdocs/echofish/index.php and change the `$yii` variable path 

```
FROM
$yii=dirname(__FILE__).'/../yii/framework/yii.php'; 
INTO
$yii=dirname(__FILE__).'/../../yii/framework/yii.php'; 
```

  * Configure nginx to serve php content through the php-fastcgi uncomment the following section into `/etc/nginx/nginx.conf`

```
        #location ~ \.php$ {
        #    root           /var/www/htdocs;
        #    fastcgi_pass   127.0.0.1:9000;
        #    fastcgi_index  index.php;
        #    fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        #    include        fastcgi_params;
        #}
``` 

### Configure startup services


  * Now make sure the required services are started at system boot by updating the file `/etc/rc.conf.local` to include the newly installed package daemons

```
syslogd_flags=NO
nginx_flags=""
pkg_scripts="syslog_ng mysqld php_fpm"
```


  * Start the remaining services
  
```
/etc/rc.d/syslogd stop
/etc/rc.d/syslog_ng start 
/etc/rc.d/php_fpm start
/etc/rc.d/nginx start
```
