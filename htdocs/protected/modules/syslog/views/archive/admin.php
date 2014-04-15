<?php
$this->breadcrumbs=array(
	'Archive'=>array('/syslog'),
	'Archives'=>array('admin'),
	'Manage'
);

$this->menu=array(
	array('label'=>'Truncate Archive','url'=>array('truncate')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('archive-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Archives</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
<?php echo CHtml::link('Help!',$this->createUrl('/'.Yii::app()->controller->module->id.'/default/help'/*,array('section'=>Yii::app()->controller->id)*/));?>

</p>
<?php $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>

<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
  'id'=>'archive-grid',
  'fixedHeader' => true,
  'headerOffset' => 40,
  'responsiveTable' => true,
  'type' => 'striped bordered',
  'dataProvider'=>$model->search(),
  'template'=>'{summary}{pager}<br/>{items}{pager}',
  'enableHistory'=>false,
  'filter'=>$model,
  'columns'=>array(
    array(
        'name'=>'received_ts',
        'type'=>'raw',
        'value'=>'CHtml::link($data->received_ts,array("admin","Archive[received_ts]"=>"=".$data->received_ts))',
        'htmlOptions'=>array('width'=>'155px'),
        'cssClassExpression'=>'"received_ts"',
        ),
    array(
        'name'=>'hostip',
        'type'=>'raw',
        'value'=>'CHtml::link($data->hostip,array("admin","Archive[hostip]"=>"=".$data->hostip),array("title"=>$data->lHost->short))',
        'cssClassExpression'=>'"hostip"',
	      'htmlOptions'=>array('width'=>'55px'),
     ),
    array(
      'name'=>'facility',
      'type'=>'raw',
      'value'=>'CHtml::link($data->facil->name,array("admin","Archive[facility]"=>"=".$data->facility),array("title"=>$data->facility.":".$data->facil->name))',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"facility"',
      
      ),
    array(
      'name'=>'level',
      'type'=>'raw',
      'value'=>'CHtml::openTag("span", array("class"=>"label label-".$data->sever->label)).CHtml::link($data->sever->name,array("admin","Archive[level]"=>"=".$data->level),array("title"=>$data->level.":".$data->sever->name))."</span>"',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"level"',
      ),
    array(
      'name'=>'program',
      'type'=>'raw',
      'value'=>'CHtml::link($data->program,array("admin","Archive[program]"=>"=".$data->program))',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"program"',
      ),
      'msg',
    array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
      'header'=>CHtml::dropDownList(
                'pageSize',
                $pageSize,
                array(0=>'All',5=>5,10=>10,20=>20,50=>50,100=>100),
                array('class'=>'change-pagesize','style'=>'width:80px;')
            ),

      'template'=>'',
      )
  ),
)); ?>
<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('archive-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>

