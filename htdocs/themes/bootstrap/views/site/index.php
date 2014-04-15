<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>'Welcome to '.CHtml::encode(Yii::app()->name),
)); ?>

<h3>Syslog filtering simplified.</h3>

<?php $this->endWidget(); ?>
<?php 
if (Yii::app()->user->isGuest) {
?>
<h4>Please <?php echo CHtml::link('login', array('site/login')); ?> to access Echofish UI.</h4>
<?php
}
else {
?>
<h4>Log Monitoring Made Easy.</h4>

<p>Echofish takes the hassle out of log monitoring:</p>

<ol>
	<li>Monitor system and application events with the <?php echo CHtml::link('Syslog module', array('syslog/logs/admin')); ?> using filters and comparison operators.</li>
	<li>Within the Syslog module, create whitelists <i class="icon-eye-close"></i> for events that do not require attention.</li>
	<li>Whitelist rules may be modified through the <?php echo CHtml::link('Lists module', array('lists/white/admin')); ?>.</li>
	<li>Let Echofish filter all the noise for you!</li>
</ol>
<?php 
}
?>