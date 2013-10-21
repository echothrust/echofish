<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'whitelist-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<?php echo $form->textFieldRow($model,'host',array('class'=>'span5','maxlength'=>20,'hint'=>'You can use "%%",  full IP "172.20.20.20", or partial match "172.20.%.%".')); ?>

	<?php echo $form->textFieldRow($model,'facility',array('class'=>'span5','maxlength'=>20,'hint'=>'Facility number or "-1" for any.')); ?>

	<?php echo $form->textFieldRow($model,'level',array('class'=>'span5','maxlength'=>20,'hint'=>'Level number or "-1" for any.')); ?>

	<?php echo $form->textFieldRow($model,'program',array('class'=>'span5','maxlength'=>50,'hint'=>'Program name or pattern "smtp%".')); ?>

	<?php echo $form->textAreaRow($model,'pattern',array('rows'=>6, 'cols'=>50,'class'=>'span5','maxlength'=>512,'hint'=>'Pattern to match against msg of the syslog.')); ?>

	<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
