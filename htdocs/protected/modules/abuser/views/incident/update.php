<?php
/* @var $this IncidentController */
/* @var $model AbuserIncident */

$this->breadcrumbs=array(
	'Abuser Incidents'=>array('admin'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Manage AbuserIncident', 'url'=>array('admin')),
  array('label'=>'View AbuserIncident', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<h1>Update AbuserIncident <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>