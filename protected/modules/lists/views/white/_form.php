<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'whitelist-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<?php echo $form->textFieldRow($model,'host',array('class'=>'span5','maxlength'=>20,'hint'=>'e.g. "%" for any, "172.20.%.%" for partial, "172.20.20.20" for exact match.')); ?>

	<?php echo $form->textFieldRow($model,'facility',array('class'=>'span5','maxlength'=>20,'hint'=>'Use "%" for any or RFC 3164 facility number (0-23).')); ?>

	<?php echo $form->textFieldRow($model,'level',array('class'=>'span5','maxlength'=>20,'hint'=>'Use "%" for any or RFC 3164 severity level number (0-7).')); ?>

	<?php echo $form->textFieldRow($model,'program',array('class'=>'span5','maxlength'=>50,'hint'=>'Use "%" for any, "smtp%" for pattern or "dhcpd" for exact match.')); ?>

	<?php echo $form->textAreaRow($model,'pattern',array('rows'=>6, 'cols'=>50,'class'=>'span5','maxlength'=>512,'hint'=>'Pattern to match against msg of the syslog. Regular MySQL LIKE patterns are accepted.<table summary="Table listing wildcard characters used with MySQL LIKE with a description of each
character." border="0" class="help-block"><tbody><tr><td scope="row"><code class="literal">%</code></td><td>Matches any number of characters, even zero characters</td></tr><tr><td scope="row"><code class="literal">_</code></td><td>Matches exactly one character</td></tr></tbody></table>')); ?>

	<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span5','hint'=>'A meaningful description')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
