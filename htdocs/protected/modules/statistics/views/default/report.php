<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	ucfirst($this->module->id),
);
 $this->pageTitle='Echofish Daily Report';
?>

<h1>Echofish Daily Report</h1>
<section id="top10-hosts-per-messages">
  <div class="page-header">
    <h2>Top10 Host per Messages</h2>
  </div>
  <h3>Top10 Hosts per messages on Archive</h3>
<?php 
$c=new CDbCriteria;
$c->condition='val>0';
$c->order='val desc';
$c->limit=10;
$dp=new CArrayDataProvider(ArchiveCountersDaily::model()->todays_hosts()->findAll($c),array(
    'id'=>'name',
    'keyField'=>'name',
    'pagination'=>false,
));
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'top10-hosts-per-messages-archive',
	'dataProvider'=>$dp,
  'summaryText'=>'' ,
    'columns'=>array(
    array(
      'name'=>'IP',
      'value'=>'$data->name',
    ),
    array(
      'name'=>'DNS',
      'value'=>'gethostbyaddr($data->name)',
    ),

		array(
      'name'=>'Messages',
      'value'=>'number_format($data->val)',
    )
	),
)); ?>


 <h3>Top10 Hosts per messages on Syslog</h3>
<?php
$dp=new CArrayDataProvider(SyslogCountersDaily::model()->todays_hosts()->findAll($c),array(
    'id'=>'name',
    'keyField'=>'name',
    'pagination'=>false,
));
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'top10-hosts-per-messages-syslog',
	'dataProvider'=>$dp,
  'summaryText'=>'' ,
	'columns'=>array(
    array(
      'name'=>'IP',
      'value'=>'$data->name',
    ),
    array(
      'name'=>'DNS',
      'value'=>'gethostbyaddr($data->name)',
    ),

		array(
      'name'=>'Messages',
      'value'=>'number_format($data->val)',
    )
	),
)); ?>
 
</section>
<hr>
<section id="top10-hosts-per-messages">
  <div class="page-header">
    <h2>Top10 Programs</h2>
  </div>
  <h3>Top10 Programs per messages on Archive</h3>
<?php 
$c=new CDbCriteria;
$c->condition='val>0';
$c->order='val desc';
$c->limit=10;
$dp=new CArrayDataProvider(ArchiveCountersDaily::model()->todays_programs()->findAll($c),array(
    'id'=>'name',
    'keyField'=>'name',
    'pagination'=>false,
));
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'top10-hosts-per-messages-archive',
	'dataProvider'=>$dp,
  'summaryText'=>'' ,
	'columns'=>array(
    array(
      'name'=>'Program',
      'value'=>'$data->name',
    ),
		array(
      'name'=>'Messages',
      'value'=>'number_format($data->val)',
    )
	),
)); ?>


  <h3>Top10 Programs per messages on Syslog</h3>
<?php
$dp=new CArrayDataProvider(SyslogCountersDaily::model()->todays_programs()->findAll($c),array(
    'id'=>'name',
    'keyField'=>'name',
    'pagination'=>false,
));
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'top10-hosts-per-messages-syslog',
	'dataProvider'=>$dp,
  'summaryText'=>'', 
	'columns'=>array(
    array(
      'name'=>'Program',
      'value'=>'$data->name',
    ),

		array(
      'name'=>'Messages',
      'value'=>'number_format($data->val)',
    )
	),
)); ?>
 
</section>
<hr>

<section id="top10-facilities">
  <div class="page-header">
    <h2>Top10 Facilities</h2>
  </div>
  <h3>Top10 Facilities on Archive</h3>
<?php 
$c=new CDbCriteria;
$c->condition='val>0';
$c->order='val desc';
$c->limit=10;
$dp=new CArrayDataProvider(ArchiveCountersDaily::model()->todays_facilities()->findAll($c),array(
    'id'=>'name',
    'keyField'=>'name',
    'pagination'=>false,
));
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'top10-archive-facilities',
	'dataProvider'=>$dp,
  'summaryText'=>'' ,
	'columns'=>array(
    array(
      'name'=>'Facility',
      'value'=>'Facility::model()->findByAttributes(array("num"=>$data->name))->name',
    ),
		array(
      'name'=>'Messages',
      'value'=>'number_format($data->val)',
    )
	),
)); ?>


  <h3>Top10 Facilities on Syslog</h3>
<?php
$dp=new CArrayDataProvider(SyslogCountersDaily::model()->todays_facilities()->findAll($c),array(
    'id'=>'name',
    'keyField'=>'name',
    'pagination'=>false,
));
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'top10-hosts-per-messages-syslog',
	'dataProvider'=>$dp,
  'summaryText'=>'', 
	'columns'=>array(
    array(
      'name'=>'Facility',
      'value'=>'Facility::model()->findByAttributes(array("num"=>$data->name))->name',
    ),

		array(
      'name'=>'Messages',
      'value'=>'number_format($data->val)',
    )
	),
)); ?>
 
</section>
<hr>