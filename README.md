Echofish - syslog web console for small networks
=

Echofish is a php and mysql application based on the excellent idea/paper of [Marcus J. Ranum - Artificial Ignorance](http://www.ranum.com/security/computer_security/papers/ai/).

When a message is received by syslog it gets written into a specific table of the echofish database.
Upon INSERT on that table, special triggers and events take over, in order to inspect the message parameters and decide if the message is worthy of your attention or if -based on your rules- needs to be discarded.

On top of that, you can create triggers that allow you to achieve certain explicit tasks such as:

  * After 10 failed ssh logins issue a syslog warning about brute force attempt and report the attacker's IP
  * If user@example.com usually logs from 1.2.3.x network and now he's appearing from Korea notify me
  * Issue an alert if the ssh logins on the servers are not from the admin's IP


You can find configuration samples, installation recipies and other usefull information on the docs folder.
