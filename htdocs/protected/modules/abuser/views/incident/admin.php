<?php
/* @var $this IncidentController */
/* @var $model AbuserIncident */

$this->breadcrumbs=array(
	'Abuser Incidents'=>array('admin'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List AbuserIncident', 'url'=>array('admin')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('abuser-incident-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Abuser Incidents</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
<?php echo CHtml::link('Help!',$this->createUrl('/'.Yii::app()->controller->module->id.'/default/help'/*,array('section'=>Yii::app()->controller->id)*/));?>

</p>



<?php $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>
<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('body').on('change','.change-pagesize', function() {
		$.fn.yiiGridView.update('abuser-incident-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>
<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
  'id'=>'abuser-incident-grid',
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
          'label' => 'Zero Counters',
          'click' => 'js:function(checked){
			     var values = [];
			     checked.each(function(){
			         values.push($(this).val());
			     }); 
			    $.ajax({
			     url:"'.$this->createUrl("zeromass").'", 
			     data: {ids:values.join(",")},
			     success:function(data){ 
			         // update the grid now
			         $("#abuser-incident-grid").yiiGridView("update"); 
			     }
			     });
		}',
        'id'=>'zeromass')
	 ),
     'checkBoxColumnConfig' => array( 'name' => 'id' ),
   	),
  'columns'=>array(
    'id',
		'ipstr',
		'trigger_id',
		'trigger.description',
		'counter',
		'first_occurrence',
		'last_occurrence',
		'ts',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
      'template'=>'{view}{delete}',
      'header'=>CHtml::dropDownList(
                'pageSize',
                $pageSize,
                array(0=>'All',5=>5,10=>10,20=>20,50=>50,100=>100),
                array('class'=>'change-pagesize','style'=>'width:80px;')
            ),
		),
	),
)); ?>
