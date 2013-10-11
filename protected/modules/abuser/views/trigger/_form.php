<?php
/* @var $this TriggerController */
/* @var $model AbuserTrigger */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'abuser-trigger-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'facility'); ?>
		<?php echo $form->textField($model,'facility'); ?>
		<?php echo $form->error($model,'facility'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'severity'); ?>
		<?php echo $form->textField($model,'severity'); ?>
		<?php echo $form->error($model,'severity'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'program'); ?>
		<?php echo $form->textField($model,'program',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'program'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'msg'); ?>
		<?php echo $form->textField($model,'msg',array('size'=>60,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'msg'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pattern'); ?>
		<?php echo $form->textField($model,'pattern',array('size'=>60,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'pattern'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'grouping'); ?>
		<?php echo $form->textField($model,'grouping'); ?>
		<?php echo $form->error($model,'grouping'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'capture'); ?>
		<?php echo $form->textField($model,'capture'); ?>
		<?php echo $form->error($model,'capture'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'occurrence'); ?>
		<?php echo $form->textField($model,'occurrence'); ?>
		<?php echo $form->error($model,'occurrence'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'priority'); ?>
		<?php echo $form->textField($model,'priority'); ?>
		<?php echo $form->error($model,'priority'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->