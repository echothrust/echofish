<?php 
$this->breadcrumbs=array(
	'Whitelists'=>array('admin'),
	'Import Backup',
);

$this->menu=array(
	array('label'=>'Create Whitelist','url'=>array('create')),
	array('label'=>'Optimise Whitelist','url'=>array('optimise')),
	array('label'=>'Export Whitelists Backup','url'=>array('export')),
	array('label'=>'Import Whitelists Backup','url'=>array('upload')),
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

