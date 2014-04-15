<?php
/*
 * Helper command, that will allow us to export IP's into 
 * bgp AS:community pairs.
 */
class BgpCommand extends CConsoleCommand {
    public function actionExport($AS,$zero=false) {
        $myAS=intval(escapeshellcmd($AS));
        if($myAS<=0)
          exit("Error you need to provide the AS number for the routes to be published\neg. php cron.php cron 65001\n\n");
    		Yii::import('application.modules.abuser.models.*');
	      $criteria=new CDbCriteria;
    		$criteria->condition='abuserIncidents.counter>=occurrence';
    		$criteria->order="abuserIncidents.counter DESC";
        $AT=AbuserTrigger::model()->with('abuserIncidents')->findAll($criteria);
        foreach($AT as $trigger)
        {
          foreach($trigger->abuserIncidents as $incident)
          {
            $ret=shell_exec(sprintf("/usr/sbin/bgpctl network add %s/32 community %d:%d",$incident->ipstr,$myAS,$incident->trigger_id,$incident->counter));
            $incident->zero($zero);
            // log operation
          }
            
        }
    }
}
/*
 * bgpd.conf
 * server
myAS="65000"

AS $myAS
router-id 192.168.1.1
fib-update no
nexthop qualify via default

socket "/var/www/logs/bgpd.rsock" restricted

group RS {
        announce all
        set nexthop no-modify
        enforce neighbor-as no
        multihop 64
        ttl-security no

        holdtime min 60
        softreconfig in no

        neighbor 0.0.0.0/0 {
                passive
        }
}
deny from any
allow to any

# Set my own community, so clients have an easy way to filter
match to group RS community $myAS:1  set pftable "echofish-trigger-1"
match to group RS community $myAS:2  set pftable "echofish-trigger-2"
match to group RS community $myAS:3  set pftable "echofish-trigger-3"
match to group RS community $myAS:4  set pftable "echofish-trigger-4"
*/