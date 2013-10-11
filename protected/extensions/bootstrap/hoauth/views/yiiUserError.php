<?php
/**
 * View to display `yii-user` authentication errors
 * 
 * @var $errorCode error code from `yii-user` module UserIdentity class
 * @var $user current user model
 */

switch($errorCode)
{
case UserIdentity::ERROR_STATUS_NOTACTIV:
  $error = 'must be activated! Check your email for details!';
  break;
case UserIdentity::ERROR_STATUS_BAN:
  $error = 'is banned';
  break;
}
?>
<div class="form">
  <div class="errorSummary">
    <p><b>Sorry, but your account <?php echo $error; ?>!</b></p>
    <p>
    <?php
    echo CHtml::link('Return to main page', '/') .
      ' | ' .
      CHtml::link('Return to login page', Yii::app()->getModule('user')->loginUrl);
    ?>
    </p>
  </div>
</div>
