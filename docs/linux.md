# How To Install Echofish With Nginx On Ubuntu 12.04 
 * Install mysql: `sudo apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql`
 * activate mysql: `sudo mysql_install_db`
 * secure mysql: `sudo mysql_secure_installation`
 * Install nginx: `sudo apt-get install nginx`
 * Start nginx: `sudo service nginx start`
 * Install php: `sudo apt-get install php5-fpm`
 * Install syslog-ng: `sudo apt-get install syslog-ng`
 * Configure syslog-ng:

```
sudo nano /etc/syslog-ng/syslog-ng.conf
at the "source s_src" block add:
udp(port(514));   
```
 * Restart syslog-ng: `sudo service sylog-ng restart`
 * Check that syslog-ng is listening to udp 514: `netstat -an | grep 514`
 * Configure php:

```
sudo nano /etc/php5/fpm/pool.d/www.conf
Find the line, listen = 127.0.0.1:9000 and change the 127.0.0.1:9000 to /var/run/php5-fpm.sock:
listen = /var/run/php5-fpm.sock
sudo service php5-fpm restart
```
 * Download Echofish: 

```
cd /usr/share/nginx/www/
wget <link to echofish>
tar -xzvf echofish.tar.gz
sudo chown www-data:www-data echofish/ -R
```
 * Dowload yii: 

```
cd /usr/share/nginx/www/
wget https://github.com/yiisoft/yii/releases/download/1.1.14/yii-1.1.14.f0fee9.tar.gz (check for the latest version always)
mv yii-1.1.14.f0fee9.tar.gz (or whatever is the latest one) yii
```
 * Create the Echofish Database and User:

```
mysql -u root -p
CREATE DATABASE echofish;
CREATE USER dbuser@localhost;
SET PASSWORD FOR dbuser@localhost= PASSWORD("dbpass");
GRANT ALL PRIVILEGES ON echofish.* TO dbuser@localhost IDENTIFIED BY 'dbpass';
FLUSH PRIVILEGES;
exit
```

Restart nginx:

```
sudo service nginx restart
```

With the above steps Echofish will be available from the http://<ip address>/echofish

Set Up Nginx Server virtual domain:

```
sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/echofish
sudo nano /etc/nginx/sites-available/echofish

server {
        listen   80;


        root /var/www;
        index index.php index.html index.htm;

        server_name <enter your domain or IP address>;

        location / {
                try_files $uri $uri/ /index.php?q=$uri&$args;
        }

        error_page 404 /404.html;

        error_page 500 502 503 504 /50x.html;
        location = /50x.html {
              root /usr/share/nginx/www;
        }

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9$
        location ~ \.php$ {
                #fastcgi_pass 127.0.0.1:9000;
                # With php5-fpm:
                fastcgi_pass unix:/var/run/php5-fpm.sock;
                fastcgi_index index.php;
                include fastcgi_params;
                 }
}
```

 * Activate virtual: `sudo ln -s /etc/nginx/sites-available/echofish /etc/nginx/sites-enabled/echofish`

 * Restart nginx: `sudo service nginx restart`
 * Restart php-fpm: `sudo service php5-fpm restart`


**NOTE** Make sure you also have the following options on your MySQL configuration my.cnf under the [mysqld] section

```
# Make default timezone UTC
default-time-zone='+00:00'
# Without this there is no echofish
event_scheduler=ON
# Allow concatanaged strings to be up to 5k chars (usualy much smaller)
group_concat_max_len=5128
```



