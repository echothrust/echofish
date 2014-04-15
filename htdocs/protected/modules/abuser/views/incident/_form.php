<?php
/* @var $this IncidentController */
/* @var $model AbuserIncident */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'abuser-incident-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'ip'); ?>
		<?php echo $form->textField($model,'ip',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'ip'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'trigger_id'); ?>
		<?php echo $form->textField($model,'trigger_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'trigger_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'counter'); ?>
		<?php echo $form->textField($model,'counter',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'counter'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'first_occurrence'); ?>
		<?php echo $form->textField($model,'first_occurrence',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'first_occurrence'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_occurrence'); ?>
		<?php echo $form->textField($model,'last_occurrence',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'last_occurrence'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ts'); ?>
		<?php echo $form->textField($model,'ts'); ?>
		<?php echo $form->error($model,'ts'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->