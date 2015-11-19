<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'host-form',
	'enableAjaxValidation'=>false,
)); ?>

<p class="help-block">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>
	<?php echo $form->textFieldRow($model,'ip',array('class'=>'span5','maxlength'=>16,'value' => $model->isNewRecord ? '0.0.0.0' : null)); ?>
	<?php echo $form->textFieldRow($model,'fqdn',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'short',array('class'=>'span5','maxlength'=>50)); ?>

	<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
</div>

<?php $this->endWidget(); ?>
