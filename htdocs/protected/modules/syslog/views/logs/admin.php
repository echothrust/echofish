<?php
$this->breadcrumbs=array(
	'Syslog'=>array('/syslog'),
	'Logs'=>array('admin'),
);

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/themes/bootstrap/js/jquery-pt-linkify.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('syslog-grid', {
		data: $(this).serialize()
	});
	return false;
});
$('body').on('click','#massackfilter',function(){
	data=$( 'table :input' ).serialize();
	lnk=$(this).attr('href');
	$.post(lnk,data,function(resp){ $.fn.yiiGridView.update('syslog-grid');window.location.href=resp;});
  //
	return false;
});
");
?>
<h1>Manage Syslog Messages</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
<?php echo CHtml::link('Help!',$this->createUrl('/'.Yii::app()->controller->module->id.'/default/help'/*,array('section'=>Yii::app()->controller->id)*/));?>

</p>
<?php $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>

<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
  'id'=>'syslog-grid',
  'fixedHeader' => true,
  'headerOffset' => 40,
  'responsiveTable' => true,
  'type' => 'striped bordered',
  'dataProvider'=>$model->search(),
  'template'=>'{summary}{pager}<br/>{items}{pager}',
  'enableHistory'=>false,
  'filter'=>$model,
	'bulkActions' => array(
                'actionButtons' => array(
                    array(
                      'buttonType' => 'button',
                      'type' => 'primary',
                      'size' => 'small',
                      'label' => 'Mass Acknowledge Selected',
                      'click' => 'js:function(checked){
     var values = [];
     checked.each(function(){
         values.push($(this).val());
     });
    $.ajax({
     url:"'.$this->createUrl("massackids").'",
     data: {ids:values.join(",")},
     success:function(data){
         // update the grid now
         $("#syslog-grid").yiiGridView("update");
     }
     });
}'
                        ,
                      'id'=>'ackids'
                    )
                ),
                'checkBoxColumnConfig' => array( 'name' => 'id' ),
   ),
  'columns'=>array(
    array(
        'name'=>'received_ts',
        'type'=>'raw',
        'value'=>'CHtml::link($data->received_ts,array("admin","Syslog[received_ts]"=>"=".$data->received_ts))',
        'htmlOptions'=>array('width'=>'155px'),
        'cssClassExpression'=>'"received_ts"',
        ),
    array(
        'name'=>'hostip',
        'type'=>'raw',
        'value'=>'CHtml::link($data->lHost->DisplayName,array("admin","Syslog[hostip]"=>"=".$data->hostip),array("title"=>$data->lHost->FullDisplayName))',
        'cssClassExpression'=>'"hostip"',
	      'htmlOptions'=>array('width'=>'55px'),
     ),
    array(
      'name'=>'facility',
      'type'=>'raw',
      'value'=>'CHtml::link($data->facil->name,array("admin","Syslog[facility]"=>"=".$data->facility),array("title"=>$data->facility.":".$data->facil->name))',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"facility"',

      ),
    array(
      'name'=>'level',
      'type'=>'raw',
      'value'=>'CHtml::openTag("span", array("class"=>"label label-".$data->sever->label)).CHtml::link($data->sever->name,array("admin","Syslog[level]"=>"=".$data->level),array("title"=>$data->level.":".$data->sever->name))."</span>"',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"level"',
      ),
    array(
      'name'=>'program',
      'type'=>'raw',
      'value'=>'CHtml::link($data->program,array("admin","Syslog[program]"=>"=".$data->program))',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"program"',
      ),
      array(
        'name'=>'msg',
        'cssClassExpression'=>'"msg"',
    ),
    array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
      'header'=>CHtml::dropDownList(
                'pageSize',
                $pageSize,
                array(0=>'All',5=>5,10=>10,20=>20,50=>50,100=>100),
                array('class'=>'change-pagesize','style'=>'width:80px;')
            ),

      'template'=>'{acknowledge}{whitelist}{abuser}',
      'buttons'=>array(
      	'headerAction'=>array
      	(
      		'label'=>'Acknowledge based on filter',
      		'icon'=>'ok',
          'url'=>Yii::app()->createUrl("syslog/logs/massack"),
          'options'=>array('rel'=>'tooltip','title'=>'ack based on filt','id'=>'massackfilter'),
      	),
        'acknowledge' => array
        (
            'label'=>'Acknowledge this entry and others like it',
            'icon'=>'ok',
            'url'=>'Yii::app()->createUrl("syslog/logs/acknowledge", array("id"=>$data->id))',
        ),
        'whitelist' => array
        (
            'label'=>'Create a whitelist rule from this entry',
            'icon'=>'eye-close',
            'url'=>'Yii::app()->createUrl("lists/white/fromsyslog", array("syslog_id"=>$data->id))',
        ),
      	'abuser' => array
      	(
      		'label'=>'Create abuser trigger pattern from this entry',
      		'icon'=>'icon-screenshot',
      		'url'=>'Yii::app()->createUrl("abuser/trigger/fromsyslog", array("syslog_id"=>$data->id))',
      	),

      )
      )
  ),
)); ?>
<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('body').on('change','.change-pagesize', function() {
       $.fn.yiiGridView.update('syslog-grid',{ data:{ pageSize: $(this).val()}})
    });
EOD
,CClientScript::POS_READY); ?>