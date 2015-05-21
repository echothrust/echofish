<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('ip')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->ip),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fqdn')); ?>:</b>
	<?php echo CHtml::encode($data->fqdn); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('short')); ?>:</b>
	<?php echo CHtml::encode($data->short); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />


</div>