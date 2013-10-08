#!/bin/sh
#
# File: syslogng-mysql-pipe.sh
#
# Take input from a FIFO and run execute it as a query for
# a mysql database.
#
# IMPORTANT NOTE:  This could potentially be a huge security hole.
# You should change permissions on the FIFO accordingly.
#

if [ -e /tmp/mysql.syslog-ng.pipe ]; then
        while [ -e /tmp/mysql.syslog-ng.pipe ]
        do
                        #cat < /tmp/mysql.syslog-ng.pipe
                        mysql echofish < /tmp/mysql.syslog-ng.pipe
        done
else
        mkfifo /tmp/mysql.syslog-ng.pipe
fi

