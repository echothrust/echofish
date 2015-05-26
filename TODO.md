This will serve as a means to communicate some of the things we plan for the 
future of Echofish. 

Feel free to comment with your ideas about features you would like to see in 
Echofish at the initial github issue [Echofish Roadmap issue#18](https://github.com/echothrust/echofish/issues/18) 

* Convert into Yii v2.0
* Make Echofish work with composer for dependency tracking (depends on 
  previous)
* Make lists management more manageable and sharable (abuser trigger lists, 
  white lists)
* Allow counters and trigger presentation for each abuser into a "unified" 
  fashion (Single ip representation for multiple triggers for this IP)
* Allow abuser triggers to decide where to act (on archived or syslog table)
* Introduce methods to import "rules" from other tools (eg logsentry, which has
  exactly the same principles as echofish)
* Introduce trigger categories (security, system, etc)
* Introduce email contacts management for notifications (must be able to 
  connect email address with more than one trigger category)
* Make visible time differences between first/last occurrence on abuser 
  incidents
* Make sure we keep a timestamp for the last time we reseted an abuser incident 
  counter
* Introduce DNSBL checking through MySQL with the use of UDF plugins (will help 
  in automating some abuser operations)
* Introduce OpenBGPd add feature to MySQL with the use of UDF plugins
* Separate documentation from web pages and place them on github WiKi (This 
  doesnt mean that we will remove them, just that we will keep them in sync)
* Add tags/labels on whitelists 
* Add last access field on lists (abuser, whitelists etc). This will help in 
  detecting outdated rules.
* Introduce statistics counter about top triggers
* Introduce view for top abusers above threshold
* Introduce json api for extraction of information from echofish (eg get list 
  of abusers). This depends on Yii v2.0.
* Look into introducing a statsd module (UDF and Yii)
* Look into ssdeep for log message generalizations (UDF and UI management) (http://ssdeep.sourceforge.net/)
* Introduce and follow **[Semantic Versioning](http://semver.org/)**
* Introduce and maintain a proper **[Changelog](http://keepachangelog.com/)**
* Update documentation
* Introduce syslog relay sender/receiver configuration snips
