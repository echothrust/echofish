### Echofish on docker (dev setup instructions)

First clone echofish and switch to this directory:

```sh
git clone https://github.com/echothrust/echofish.git
cd echofish/docker
```

with docker-compose:

```sh
vi docker-compose.yml # optional, to adjust settings
docker-compose up
```

or with plain docker:

```sh
# Note: build context directory is .. (echofish project root)
docker build -t ETS_echofish-web -f Dockerfile-web ..
docker run --name echofish-db \
  -p 127.0.0.1:3306:3306 \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=ETS_echofish \
  -e MYSQL_USER=echofish \
  -e MYSQL_PASSWORD=dbpass \
  -v `pwd`/mycnf-event-scheduler.conf:/etc/mysql/conf.d/event-scheduler.conf \
  -v `pwd`/../schema/00_echofish-schema.sql:/docker-entrypoint-initdb.d/00-schema.sql \
  -v `pwd`/../schema/echofish-dataonly.sql:/docker-entrypoint-initdb.d/01-data.sql \
  -v `pwd`/../schema/echofish-functions.sql:/docker-entrypoint-initdb.d/02-function.sql \
  -v `pwd`/../schema/echofish-triggers.sql:/docker-entrypoint-initdb.d/03-trigger.sql \
  -v `pwd`/../schema/echofish-events.sql:/docker-entrypoint-initdb.d/04-event.sql \
  -v `pwd`/../schema/echofish-procedures.mariadb10.sql:/docker-entrypoint-initdb.d/05-mariadb-proc.sql \
  -d mariadb:10.1
docker run --name echofish-web \
  --link echofish-db:db \
  -p 127.0.0.1:80:80 \
  -v `pwd`/php-timezone.ini:/usr/local/etc/php/conf.d/php-timezone.ini \
  -v /etc/timezone:/etc/timezone:ro \
  -v /etc/localtime:/etc/localtime:ro \
  -d ETS_echofish-web
```

