<?php
/**
 * @var HOAuthWidget $this
 * @var string $provider name of provider
 */

$invitation = Yii::app()->user->isGuest ? 'Sign in' : 'Connect';
?>
<p>
  <a href="<?php echo Yii::app()->createUrl($this->route . '/oauth', array('provider' => $provider)); ?>" class="zocial <?php  echo strtolower($provider) ?>"><?php  echo "$invitation with $provider"; ?></a>
</p>
