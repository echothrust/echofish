<div id="checkbl">
<pre>
<?php foreach($dnsbl as $bl):?>
Checking <?php echo $model->ipstr?> against <?php echo $bl,":\t",gethostbyname($revip.'.'.$bl),"\n"?>
<?php endforeach;?>
</pre>
</div>