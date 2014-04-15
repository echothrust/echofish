<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	ucfirst($this->module->id),
);
 $this->pageTitle='Echofish Statistics';
?>
<h1>Echofish Statistics</h1>
<p>
<?php
$alogs=$slogs=$labels=array();
$facs=$archive_daily->todays_facilities()->findAll();

foreach($facs as $fac)
	$alogs[$fac->name]=intval($fac->val);
$facs=$syslog_daily->todays_facilities()->findAll();
foreach($facs as $fac)
	$slogs[$fac->name]=intval($fac->val);

foreach($alogs as $key => $val)
	if(!isset($slogs[$key]))
		$slogs[$key]=0;
$archive_logs=ksort($alogs);
$system_logs=ksort($slogs);
foreach($alogs as $key =>$val)
	$labels[]=Facility::model()->findByPk($key)->name;

$this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
   		'chart'=>array('type'=>'bar',),
			'credits' => array('enabled' => false),
      'title' => array('text' => 'Messages per Facility Today'),
      'xAxis' => array(
         'categories' => $labels,
      ),
      'yAxis' => array(
         'title' => array('text' => 'Messages Today')
      ),
      'series' => array(
      	 array('name' => 'Archive', 'data' => array_values($alogs)),
         array('name' => 'Syslog', 'data' => array_values($slogs))
      )
   )
));

$alogs=$slogs=$labels=array();
$facs=$archive_daily->todays_severities()->findAll();

foreach($facs as $fac)
	$alogs[$fac->name]=intval($fac->val);
$facs=$syslog_daily->todays_severities()->findAll();
foreach($facs as $fac)
	$slogs[$fac->name]=intval($fac->val);

foreach($alogs as $key => $val)
	if(!isset($slogs[$key]))
		$slogs[$key]=0;
$archive_logs=ksort($alogs);
$system_logs=ksort($slogs);
foreach($alogs as $key =>$val)
	$labels[]=Severity::model()->findByPk($key)->name;

$this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
   		'chart'=>array('type'=>'bar',),
			'credits' => array('enabled' => false),
      'title' => array('text' => 'Messages per Severity Today'),
      'xAxis' => array(
         'categories' => $labels,
      ),
      'yAxis' => array(
         'title' => array('text' => 'Messages Today')
      ),
      'series' => array(
      	 array('name' => 'Archive', 'data' => array_values($alogs)),
         array('name' => 'Syslog', 'data' => array_values($slogs))
      )
   )
));

$alogs=$slogs=$labels=array();
$facs=$archive_daily->todays_hosts()->findAll();

foreach($facs as $fac)
	$alogs[$fac->name]=intval($fac->val);
$facs=$syslog_daily->todays_hosts()->findAll();
foreach($facs as $fac)
	$slogs[$fac->name]=intval($fac->val);

foreach($alogs as $key => $val)
	if(!isset($slogs[$key]))
		$slogs[$key]=0;
$archive_logs=ksort($alogs);
$system_logs=ksort($slogs);
foreach($alogs as $key =>$val)
	$labels[]=$key;

$this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
   		'chart'=>array('type'=>'bar','height'=>'800'),
			'credits' => array('enabled' => false),
      'title' => array('text' => 'Messages per Host Today'),
      'xAxis' => array(
         'categories' => $labels,
      ),
      'yAxis' => array(
         'title' => array('text' => 'Messages Today')
      ),
      'series' => array(
      	 array('name' => 'Archive', 'data' => array_values($alogs)),
         array('name' => 'Syslog', 'data' => array_values($slogs))
      )
   )
));

$alogs=$slogs=$labels=array();
$c=new CDbCriteria;
$c->limit=10;
$facs=$archive_daily->todays_programs()->findAll($c);

foreach($facs as $fac)
	$alogs[$fac->name]=intval($fac->val);
$facs=$syslog_daily->todays_programs()->findAll($c);
foreach($facs as $fac)
	$slogs[$fac->name]=intval($fac->val);

$labels=array_keys($alogs);

$this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
   		'chart'=>array('type'=>'bar','height'=>'400'),
			'credits' => array('enabled' => false),
      'title' => array('text' => 'Messages per Program Today on Archive'),
      'xAxis' => array(
         'categories' => $labels,
      ),
      'yAxis' => array(
         'title' => array('text' => 'Messages Today')
      ),
      'series' => array(
      	 array('name' => 'Archive', 'data' => array_values($alogs)),
//         array('name' => 'Syslog', 'data' => array_values($slogs))
      )
   )
));

$labels=array_keys($slogs);

$this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
   		'chart'=>array('type'=>'bar','height'=>'400'),
			'credits' => array('enabled' => false),
      'title' => array('text' => 'Messages per Program Today on Syslog'),
      'xAxis' => array(
         'categories' => $labels,
      ),
      'yAxis' => array(
         'title' => array('text' => 'Messages Today')
      ),
      'series' => array(
      	 //array('name' => 'Archive', 'data' => array_values($alogs)),
         array('name' => 'Syslog', 'data' => array_values($slogs))
      )
   )
));
?>
</p>
