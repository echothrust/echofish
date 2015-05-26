<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'archive-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'host',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'facility',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'priority',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'level',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'program',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'pid',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'tag',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textAreaRow($model,'msg',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

	<?php echo $form->textFieldRow($model,'received_ts',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'created_at',array('class'=>'span5')); ?>


	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
