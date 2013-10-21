<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>'Welcome to '.CHtml::encode(Yii::app()->name),
)); ?>

<p>Syslog filtering simplified.</p>

<?php $this->endWidget(); ?>

<p>Log Management Made Easy.</p>

<p>Echofish takes the hassle out of log management:</p>

<ol>
	<li>Monitor system and application events with the <a href="?r=syslog/logs/admin">Syslog module</a> using filters and comparison operators.</li>
	<li>Within the <a href="?r=syslog/logs/admin">Syslog module</a>, create whitelists <i class="icon-eye-close"></i> for events that do not require attention.</li>
	<li>Whitelist rules may be modified through the <a href="?r=syslog/logs/admin">Whitelist module</a>.</li>
	<li>Let Echofish filter all the noise for you!</li>
</ol>

<p>For more details on echofish, please visit the project page <a href="https://github.com/echothrust/echofish">github.com/echothrust/echofish</a>.</p>
