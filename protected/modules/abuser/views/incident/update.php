<?php
/* @var $this IncidentController */
/* @var $model AbuserIncident */

$this->breadcrumbs=array(
	'Abuser Incidents'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AbuserIncident', 'url'=>array('index')),
	array('label'=>'Create AbuserIncident', 'url'=>array('create')),
	array('label'=>'View AbuserIncident', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AbuserIncident', 'url'=>array('admin')),
);
?>

<h1>Update AbuserIncident <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>