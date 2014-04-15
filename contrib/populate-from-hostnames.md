Populate echofish tables from hostnames db
=


```sql
INSERT INTO ETS_echofish.archive_bh(host,facility,level,received_ts,program,pid,msg) 
SELECT t2.ip AS host, t1.facility, t1.level, t1.datetime, t1.program, t1.pid, t1.msg
FROM mail AS t1
LEFT JOIN hosts_map AS t2 ON t1.host = t2.hostname
```