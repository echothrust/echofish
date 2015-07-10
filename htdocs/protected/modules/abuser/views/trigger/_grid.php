<?php $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>
<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('body').on('change','.change-pagesize', function() {
        $.fn.yiiGridView.update('abuser-trigger-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>
<?php $this->widget('bootstrap.widgets.TbExtendedGridView', array(
  'id'=>'abuser-trigger-grid',
  'fixedHeader' => true,
  'headerOffset' => 40,
  'responsiveTable' => true,
  'type' => 'striped bordered',
  'dataProvider'=>$model->search(),
  'template'=>'{summary}{pager}<br/>{items}{pager}',
  'enableHistory'=>false,
  'filter'=>$model,
	'columns'=>array(
		'id',
		'facility',
		'severity',
		'program',
		'msg',
		'pattern',
		'grouping',
		'capture',
		'description',
		'occurrence',
		'priority',
		'active',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
      'header'=>CHtml::dropDownList(
                'pageSize',
                $pageSize,
                array(0=>'All',5=>5,10=>10,20=>20,50=>50,100=>100),
                array('class'=>'change-pagesize','style'=>'width:80px;')
            ),
		),
	),
)); ?>