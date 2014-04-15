<h1>Reports per Trigger<h1>

<?php foreach($model as $entry):?>
<h2><?php printf("[ID:%d] %s",$entry->id,$entry->description)?></h2>

<pre>
<?php printf("%16s Incidents\n","Abuser's IP");?>
<?php foreach($entry->abuserIncidents as $incident):?>
<?php printf("%16s %d\n",$incident->ipstr,$incident->counter);
?>
<?php endforeach;/*incidents*/?>
</pre>
<hr>

<?php endforeach;?>
<small>Generated at <?php echo date('l jS \of F Y h:i:s A');?>