Setting up a syslog concentrator with syslog-ng
=

In order to be able to get all logs into the database tables and avoid logging error-neus entries

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
                table("archive") 
                columns("host", "facility", "priority", "level", "received_ts", "program", "msg","pid","tag")
                values("$HOST", "$FACILITY_NUM", "$PRIORITY","$LEVEL_NUM", "$YEAR-$MONTH-$DAY $HOUR:$MIN:$SEC", "$PROGRAM", "$MSG","$PID", "$TAG" )
        );
};

destination d_mysql_local {
        sql(
                type(mysql)
                host("DATABASEHOST") username("USERNAME") password("PASSWORD")
                database("ETS_echofish")
                table("archive") 
                columns("host", "facility", "priority", "level", "received_ts", "program", "msg","pid","tag")
# change the following 172.20.1.254 with the ip of the concentrator
                values("172.20.1.254", "$FACILITY_NUM", "$PRIORITY","$LEVEL_NUM", "$YEAR-$MONTH-$DAY $HOUR:$MIN:$SEC", "$PROGRAM", "$MSG","$PID", "$TAG" )
        );
};

```