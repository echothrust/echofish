<?php
/* @var $this DefaultController */
$this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name).' Lists Help',
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
  */ ?>
<section id="white">
    <div class="page-header">
        <h1>White Lists</h1>
    </div>

    <p class="lead">List view of whitelist rules.</p>

    <p>Whitelisting enables you to have custom rules that screen-out certain messages from reaching the Syslog/Logs view. They are designed to be configurable in filtering messages that don't require any sort of administrative attention (e.g. messages that are just the audit trail of normal operations).</p>

    <p>This view provides a paginated list of all these rules that are used to filter-out the "noise", including the following fields:
    <ul> 
    <li>ID: The unique ID of each rule.</li>
    <li>Description: A meaningful description each whitelisting rule.</li>
    <li>Host: Specifies the IP address of the sending host that each rule applies for.</li>
    <li>Facility: The facility code that each rule is configured to filter.</li>
    <li>Level: The severity level that each rule is configured to filter.</li>
    <li>Program: The sending program that this rule applies.</li>
    </ul>
    </p>
    
    <p>The number of whitelist rules displayed per page can be adjusted by choosing one of the options from the select box on the header row.</p>
	
	<p>Sorting by any of the fields is possible simply by clicking on their header titles.</p>
	
	<p>Searching within this view can be accomplished through the text inputs beneath each header title. You may use multiple inputs to refine your search based on any of the fields. You may optionally enter a comparison operator (<, <=, >, >=, <> or =) at the beginning of each of your search values to specify how the comparison should be done. For (Facility) and (Level) searches, the application expects RFC 5424 facility code and severity level respectively.</p>
    
	<p>This view provides a more detailed view of existing rules through the <i class="icon-eye-open"></i> button, allows modifying them via the <i class="icon-pencil"></i> button and deleting them using the <i class="icon-trash"></i> button.</p>
	
	<p>The top-right menu includes the following operations:
	<ul>
	<li>Create Whitelist: Allows adding your own whitelist rules from scratch.
	<li>Optimise Whitelist: Optimises ruleset for performance by detecting overlapping rules. This process removes old rules that are now also reflected by more generalised (newly-introduced) rules.</li>
	<li>Export Whitelists Backup: Allows you to download a backup of all your user-defined whitelist rules.</li>
	<li>Import Whitelists Backup: Allows you to upload a (previously exported) backup file.</li>
	</ul>

	<p>It is generaly easier to create your whitelist rules from within the Syslog/Logs view, using the <i class="icon-eye-close"></i> button next to each log entry, which pre-fills the form fields with those of your actual message.</p>
	
    <p>Creating a Whitelist rule requires the following fields:
    <ul>
    <li>Host: Specifies the IP of the sending host that this rule applies. Besides literal IP addresses ("172.20.20.20"), you may also use MySQL LIKE syntax ("%" for any, "172.20.%.%" for partial).</li>
    <li>Facility: Specifies the RFC 5424 facility code of messages that this rule will filter. Use "%" for any, or a value (0-23). More on help Syslog/Facilities.</li>
    <li>Level: Specifies the RFC 5424 severity level of messages to be filtered. Use "%" for any, or a value (0-7).More on help Syslog/Severities.</li>
    <li>Program: Specifies the sending program that this rule applies. Use "%" for any, "smtp%" for pattern or "dhcpd" for exact match.
    <li>Pattern: Specifies the pattern to match the (Msg) part of syslog messages that this rule will filter. You can whitelist exact matches (e.g. "Job `cron.daily' started"), but you may also use regular MySQL LIKE wildcards to specify parts of the message that change (e.g."Anonymous TLS connection established from %[%]: TLSv1 with cipher ECDHE-RSA-RC4-SHA (128/128 bits)").</li>
    <li>Description: A meaningful description for this whitelisting rule.</li>
    </ul>
    </p>
    
    <p>Upon receiving a message, Echofish stores it intact in Syslog/Archive and will subsequently inspect the message parameters (Host, Facility, Level, Program) and the message text (Msg) to decide -based on these whitelist rules- whether the message should be promoted to the Syslog/Logs view.</p>
    
    <p>While keeping the integrity of your logs archive intact, Whitelists enable you to filter-out messages based on common syslog fields (Host, Facility, Severity Level, Program), but also based on the messages' free-form textual part (Msg) that provides actual information about the event.</p> 
	
</section>
<hr>




