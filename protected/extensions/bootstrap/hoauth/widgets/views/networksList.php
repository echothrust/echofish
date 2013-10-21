<?php
/**
 * @var $sns array of array('provider' => $network->provider, 'profileUrl' => $network->profile->profileURL, 'deleteUrl' => $deleteUrl)
 */
?>
<ul class="hoauthSNList">
	<?php
	foreach($sns as $sn)
	{
		list($provider, $profileUrl, $deleteUrl) = array_values($sn);
    echo '<li>' . CHtml::link($provider, $profileUrl, array('target' => '_blank')) . ' ' . CHtml::ajaxLink('(Remove)', $deleteUrl, array(
      'type' => 'post', 
      'context' => new CJavaScriptExpression('this'),
      'beforeSend' => new CJavaScriptExpression("function() {return confirm('If you remove this social network account, you will you will not be able to login with it.\\n\\nDo you realy want to remove this account?');}"),
      'success' => new CJavaScriptExpression('function() {$(this).parent().remove();}'),
    ), array('class'=>"hoauthSNUnbind")) . '</li>';
	}
	?>
</ul>
