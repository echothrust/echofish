<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'whitelist-form',
  'type'=>'horizontal',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<?php echo $form->textFieldRow($model,'host',array('class'=>'span5'),array('maxlength'=>40,'hint'=>'e.g. "%" for any, "172.20.%.%" for partial, "172.20.20.20" for exact match.')); ?>

	<?php echo $form->textFieldRow($model,'facility',array('class'=>'span5','maxlength'=>20),array('hint'=>'Use "%" for any or RFC 5424 facility number (0-23).')); ?>

	<?php echo $form->textFieldRow($model,'level',array('class'=>'span5','maxlength'=>20),array('hint'=>'Use "%" for any or RFC 5424 severity level number (0-7).')); ?>

	<?php echo $form->textFieldRow($model,'program',array('class'=>'span5','maxlength'=>50),array('hint'=>'Use "%" for any, "smtp%" for pattern or "dhcpd" for exact match.')); ?>

	<?php echo $form->textAreaRow($model,'pattern',array('rows'=>6, 'cols'=>50,'class'=>'span5','maxlength'=>512),array('hint'=>'')); ?>

	<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span5'),array('hint'=>'A meaningful description')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
