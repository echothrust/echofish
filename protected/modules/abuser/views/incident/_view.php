<?php
/* @var $this IncidentController */
/* @var $data AbuserIncident */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ip')); ?>:</b>
	<?php echo CHtml::encode($data->ip); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('trigger_id')); ?>:</b>
	<?php echo CHtml::encode($data->trigger_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('counter')); ?>:</b>
	<?php echo CHtml::encode($data->counter); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('first_occurrence')); ?>:</b>
	<?php echo CHtml::encode($data->first_occurrence); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_occurrence')); ?>:</b>
	<?php echo CHtml::encode($data->last_occurrence); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ts')); ?>:</b>
	<?php echo CHtml::encode($data->ts); ?>
	<br />
<pre>
<?php foreach($data->evidence as $arch):?>
<?php echo CHtml::encode($arch->msg)?>
<?php endforeach;?>
</pre>
</div>