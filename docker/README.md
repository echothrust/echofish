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
docker build -t ETS_echofish-db -f Dockerfile-db ..
docker build -t ETS_echofish-web -f Dockerfile-web ..
docker run --name echofish-db \
         -e MYSQL_ROOT_PASSWORD=root \
         -p 127.0.0.1:3306:3306 \
         -d ETS_echofish-db
docker run --name echofish-web \
         --link echofish-db:db \
         -p 127.0.0.1:80:80 \
         -v `pwd`/php-timezone.ini:/usr/local/etc/php/conf.d/php-timezone.ini \
         -v /etc/timezone:/etc/timezone:ro \
         -v /etc/localtime:/etc/localtime:ro \
         -d a1
```

