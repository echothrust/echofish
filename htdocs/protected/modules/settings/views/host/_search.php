<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

		<?php echo $form->textFieldRow($model,'ip',array('class'=>'span5','maxlength'=>10)); ?>

		<?php echo $form->textFieldRow($model,'fqdn',array('class'=>'span5','maxlength'=>255)); ?>

		<?php echo $form->textFieldRow($model,'short',array('class'=>'span5','maxlength'=>50)); ?>

		<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
