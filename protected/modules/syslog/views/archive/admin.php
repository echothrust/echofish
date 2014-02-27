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
</p>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'archive-grid',
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
        'value'=>'CHtml::link($data->hostip,array("admin","Archive[hostip]"=>"=".$data->hostip))',
        'cssClassExpression'=>'"hostip"',
	      'htmlOptions'=>array('width'=>'55px'),
     ),
    array(
      'name'=>'facility',
      'value'=>'$data->facil->name',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"facility"',
      
      ),
    array(
      'name'=>'level',
      'value'=>'$data->sever->name',
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
  ),
)); ?>
