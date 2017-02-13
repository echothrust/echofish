### Setting up a syslog concentrator with syslog-ng

In order to insert logs into the database tables and avoid logging erroneous entries:

```
# syslog-ng configuration file for echofish on OpenBSD.

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
source s_local {
        internal();
        udp(ip("127.0.0.1") port(514));
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
log { source(s_local); rewrite(r_local_sethost); destination(d_mysql_local); };

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

```

Provided the database exists, and the web-ui is installed, after restarting
the `syslog_ng` service, your setup should be working:

```sh
rcctl restart syslog_ng
```

Source `s_local` is configured to accept messages from localhost; we instruct
OpenBSD's stock syslogd(8) to relay all logs from the OS to the syslog-ng
instance, listening on 127.0.0.1:514:

```sh
echo '*.* @127.0.0.1' >> /etc/syslog.conf && rcctl restart syslogd
```

