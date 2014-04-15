<?php
/* @var $this DefaultController */
$this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name).' Statistics Help',
)); ?>
<?php $this->endWidget();?>
<?php 
/*switch($section)
{
	case 'host': 
	  echo $this->renderPartial('host',array('no'=>1));
	  break;
	case 'sysconfig':
	  echo $this->renderPartial('sysconfig',array('no'=>1));
	  break;
	case 'user':
	  echo $this->renderPartial('user',array('no'=>1));
	  break;
	  
	default:
	  echo $this->renderPartial('host',array('no'=>1));
	  echo $this->renderPartial('users',array('no'=>2));
	  echo $this->renderPartial('sysconf',array('no'=>3));
	  break;
}
  




