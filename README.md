Echofish
=
Central syslog management console with whitelisting feature and ability to 
generate events from syslog entries.

## Description

Echofish is a web based real-time event log aggregation, monitoring and 
management system, developed in php and mysql, with functionality based on the 
excellent idea/paper of [Marcus J. Ranum, "Artificial Ignorance"](http://www.ranum.com/security/computer_security/papers/ai/). 

Through a simple dashboard, Echofish provides a single view for the 
administrators to monitor system and application events. Echofish allows the 
creation of whitelists and filters to provide administrators with a unique and 
customized view of their systems' event messages. Through detailed reports, 
alerting and notifications, Echofish is able to deliver new levels of 
visibility and insight on your business IT infrastructure.

## How Echofish differs

Echofish focuses around the fact that:

* daily operators need to be able to see what happens on their network.
* syslog messages are events that need to be addressed.
* similar events don't need to be addressed separately.
* there is no need to be worried about events that are known to be good 
  (whitelisted).
* once all the whitelisted entries are gone, we are left with mostly important 
  messages that need our attention.

## Beneath the hood

When a message is received by syslog it gets written into a specific table of 
the echofish database.

Upon INSERT on that table, special triggers and events take over, in order to 
inspect the message parameters and decide -based on your rules- whether the 
message is worthy of your attention or it needs to be discarded.

On top of that, you can create triggers that allow you to achieve certain 
explicit tasks such as:

  * After 10 failed ssh logins issue a syslog warning about brute force 
    attempt and report the attacker's IP
  * If user@example.com usually logs from 1.2.3.x network and now he's 
    appearing from Korea notify me
  * Issue an alert if the ssh logins on the servers are not from the admin's IP


You can find configuration samples, installation recipes and other usefull 
information on the docs folder.

# Credits
  * P.Athaks
  * P.kotsiop

