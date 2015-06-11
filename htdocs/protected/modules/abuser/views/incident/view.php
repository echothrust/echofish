<?php
/* @var $this IncidentController */
/* @var $model AbuserIncident */

$this->breadcrumbs=array(
		'Abuser Incidents'=>array('admin'),
		$model->id,
);

$this->menu=array(
		array('label'=>'Manage Abuser Incidents', 'url'=>array('admin')),
		array('label'=>'Whois Abuser IP',
				'url'=>array('whois','id'=>$model->id),
				'linkOptions'=>array(
						'id'=>"whois_action",
						'confirm'=>'This might take a while...')),
		array('label'=>'Check through DNSBL', 'url'=>array('checkbl','id'=>$model->id),'linkOptions'=>array('id'=>"checkbl_action",'confirm'=>'This might take a while...')),
		array('label'=>'Reset Abuser Incident Counter', 'url'=>array('reset', 'id'=>$model->id)),
		array('label'=>'Delete AbuserIncident', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
Yii::app()->clientScript->registerScript('whois_hook', "
		$('#whois_action').click(function(){
		$.get($('#whois_action').attr('href'),function(data){ $('#whois').replaceWith(data);});
		return false;
});
		$('#checkbl_action').click(function(){
		$.get($('#checkbl_action').attr('href'),function(data){ $('#checkbl').replaceWith(data);});
		return false;
});

		");
?>

<div id="incident-view">
	<h1>
		View AbuserIncident #
		<?php echo $model->id; ?>
	</h1>
	<?php $this->widget('bootstrap.widgets.TbDetailView',array(
			'data'=>$model,
			'attributes'=>array(
					'id',
					'ipstr',
					'trigger_id',
					'counter',
					'first_occurrence',
					'last_occurrence',
					'ts',
			),
)); ?>
	<div id="checkbl"></div>
	<div id="whois"></div>

	<pre>
<?php foreach($model->evidence as $arch):?>
<?php printf("%s %s %s[%d]: %s\n",$arch->received_ts,CHtml::link($arch->hostip,Yii::app()->createUrl('/settings/host/view',array('id'=>$arch->host)),array( 'data-toggle'=>"tooltip",'title'=>$arch->lHost->short)),$arch->program,$arch->pid,CHtml::encode($arch->msg))?>
<?php endforeach;?>
</pre>

</div>
