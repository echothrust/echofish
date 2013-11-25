<?php
$this->breadcrumbs=array(
	'Syslog'=>array('/syslog'),
	'Logs'=>array('admin'),
);

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
$('#massackfilter').live('click',function(){
	data=$( 'table :input' ).serialize();
	lnk=$(this).attr('href');
	$.post(lnk,data,function(){ $.fn.yiiGridView.update('syslog-grid'); });
	return false;
});
");
?>
<h1>Manage Sylog Messages</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php $this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'syslog-grid',
  'dataProvider'=>$model->search(),
  'template'=>'{summary}{pager}<br/>{items}{pager}',
  //'afterAjaxUpdate'=>'js:function(id){init_grid_hook();}',
  'enableHistory'=>false,
  'filter'=>$model,
  'columns'=>array(
    array(
        'name'=>'received_ts',
        'value'=>'$data->received_ts',
        'htmlOptions'=>array('width'=>'155px'),
        'cssClassExpression'=>'"received_ts"',
        ),
    array(
        'name'=>'hostip',
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
      'value'=>'$data->program',
      'htmlOptions'=>array('width'=>'55px'),
      'cssClassExpression'=>'"program"',
      ),
      'msg',
    array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
      'template'=>'{acknowledge}{whitelist}',
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

      )
      )
  ),
)); ?>
