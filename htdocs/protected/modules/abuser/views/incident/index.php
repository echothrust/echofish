<?php
/* @var $this IncidentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Abuser Incidents',
);

$this->menu=array(
	array('label'=>'Manage AbuserIncident', 'url'=>array('admin')),
);
?>

<h1>Abuser Incidents</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
