<?php
/* @var $this TriggerController */
/* @var $model AbuserTrigger */
/* @var $form CActiveForm */
?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'abuser-trigger-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<h5></h5>
	<fieldset>
	    <legend>Trigger Criteria</legend>
			<?php echo $form->textFieldRow($model,'facility',array('class'=>'span5','maxlength'=>20),array('hint'=>'Enter an RFC 5424 facility number (0-23), or use "-1" to match any.')); ?>

			<?php echo $form->textFieldRow($model,'severity',array('class'=>'span5','maxlength'=>20),array('hint'=>'Enter an RFC 5424 severity level number (0-7), or use "-1" for any.')); ?>

			<?php echo $form->textFieldRow($model,'program',array('class'=>'span5','maxlength'=>50),array('hint'=>'Use "%" for any, "smtp%" for pattern or "dhcpd" for exact match.')); ?>

			<?php echo $form->textAreaRow($model,'msg',array('rows'=>6, 'cols'=>50,'class'=>'span5','maxlength'=>512),array('hint'=>'Pattern to match against msg of the syslog. Regular MySQL LIKE patterns are accepted.')); ?>
	</fieldset>

	<fieldset>
	  <legend>Trigger Action</legend>
		<?php echo $form->textAreaRow($model,'pattern',array('rows'=>6, 'cols'=>50,'class'=>'span5','maxlength'=>512),array('hint'=>'PCRE that will run against matched messages to extract abuser IP.')); ?>
		<?php echo $form->textFieldRow($model,'grouping',array('class'=>'span5','maxlength'=>50),array('hint'=>'Specify Grouping from PCRE pattern above.')); ?>
		<?php echo $form->textFieldRow($model,'capture',array('class'=>'span5','maxlength'=>50),array('hint'=>'Specify Capture from PCRE pattern above.')); ?>
	</fieldset>

	<fieldset>
	  <legend>Trigger Details</legend>
		<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50,'class'=>'span5','maxlength'=>512),array('hint'=>'A general description for this trigger.')); ?>
		<?php echo $form->textFieldRow($model,'active',array('class'=>'span5','maxlength'=>50),array('hint'=>'Active = 1, Innactive = 0.')); ?>
		<?php echo $form->textFieldRow($model,'occurrence',array('class'=>'span5','maxlength'=>50),array('hint'=>'Event count threshold for incident escalation.')); ?>
		<?php echo $form->textFieldRow($model,'priority',array('class'=>'span5','maxlength'=>50),array('hint'=>'Priority of the trigger.')); ?>
	</fieldset>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
