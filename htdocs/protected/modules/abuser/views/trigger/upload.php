<?php 
$this->breadcrumbs=array(
	'Abuser Triggers'=>array('admin'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Manage Abuser Triggers', 'url'=>array('admin')),
	array('label'=>'Export Abuser Triggers Backup','url'=>array('export')),
);

if (Yii::app()->user->hasFlash('success')): ?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>
<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    //'heading'=>'Welcome to '.CHtml::encode(Yii::app()->name),
)); ?>

<h1>Backup Upload</h1>
<?php echo $form; ?>

<?php $this->endWidget(); ?>

