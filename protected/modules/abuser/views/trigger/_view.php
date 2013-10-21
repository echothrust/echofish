<?php
/* @var $this TriggerController */
/* @var $data AbuserTrigger */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('facility')); ?>:</b>
	<?php echo CHtml::encode($data->facility); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('severity')); ?>:</b>
	<?php echo CHtml::encode($data->severity); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('program')); ?>:</b>
	<?php echo CHtml::encode($data->program); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('msg')); ?>:</b>
	<?php echo CHtml::encode($data->msg); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pattern')); ?>:</b>
	<?php echo CHtml::encode($data->pattern); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('grouping')); ?>:</b>
	<?php echo CHtml::encode($data->grouping); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('capture')); ?>:</b>
	<?php echo CHtml::encode($data->capture); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('occurrence')); ?>:</b>
	<?php echo CHtml::encode($data->occurrence); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('priority')); ?>:</b>
	<?php echo CHtml::encode($data->priority); ?>
	<br />

	*/ ?>

</div>