<?php
$this->breadcrumbs=array(
	'Severities'=>array('admin'),
	'Manage',
);

$this->menu=array(
array('label'=>'Create Severity','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
$('.search-form').toggle();
return false;
});
$('.search-form form').submit(function(){
$.fn.yiiGridView.update('severity-grid', {
data: $(this).serialize()
});
return false;
});
");
?>

<h1>Manage Severities</h1>

<p>
	You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>
		&lt;&gt;</b>
	or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
<?php echo CHtml::link('Help!',$this->createUrl('/'.Yii::app()->controller->module->id.'/default/help'/*,array('section'=>Yii::app()->controller->id)*/));?>
	</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>
<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('severity-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>
<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
  'id'=>'severity-grid',
  'fixedHeader' => true,
  'headerOffset' => 40,
  'responsiveTable' => true,
  'type' => 'striped bordered',
  'dataProvider'=>$model->search(),
  'template'=>'{summary}{pager}<br/>{items}{pager}',
  'enableHistory'=>false,
  'filter'=>$model,
'columns'=>array(
		'name',
		'description',
		'num',
array(
'class'=>'bootstrap.widgets.TbButtonColumn',
),
),
)); ?>
