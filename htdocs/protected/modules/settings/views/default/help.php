<?php
/* @var $this DefaultController */
$this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name).' Settings Help',
)); ?>
<?php $this->endWidget();?>
<?php 
/*
switch($section)
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
	  echo $this->renderPartial('user',array('no'=>2));
	  echo $this->renderPartial('sysconfig',array('no'=>3));
	  break;
}
*/
?>
<section id="toc">
	<h3>Help topics</h3>
	<h4><a href="#hosts">1. Hosts</a></h4>
	<h4><a href="#users">2. Users</a></h4>
	<h4><a href="#sysconf">3. System Configuration</a></h4>
</section>


<section id="hosts">
	<a name="hosts"></a>
    <div class="page-header">
        <h1>1. Hosts</h1>
    </div>

    <p class="lead">The list of devices sending syslog messages to Echofish.</p>

    <p>This page holds all the syslog devices (Hosts) from which Echofish has received events, allowing you to manage their (cached) DNS names and provide short names for those devices</p>
    
    <p>For each Host, the following fields are included:
    <ul>
    <li><b>Ipoctet:</b> The IP address of the device (e.g. "172.20.20.20").</li>
    <li><b>Fqdn:</b> The Host's fully qualified domain name (IP resolved to a DNS name e.g. "mailserver.someplace.tld")</li>
    <li><b>Short:</b> A short distinctive name for the device (pops-up when you hover a Host IP in view Syslog/Logs and Syslog/Archive).</li>
    <li><b>Description:</b> A meaningful description for this Host (e.g. "mail server at home (someplace.tld)"</li>
    </ul>
    </p>
    
    <p>The number of Hosts displayed per page can be adjusted by choosing one of the options from the select box on the header row.</p>

	<p>Sorting by any of the fields is possible simply by clicking on their header titles.</p>

	<p>Searching within this view can be accomplished through the text inputs beneath each header title. You may use multiple inputs to refine your search based on any of the fields. You may optionally enter a comparison operator (<, <=, >, >=, <> or =) at the beginning of each of your search values to specify how the comparison should be done.</p>
	
	<p>A more detailed view of a particular Host is available through the <i class="icon-eye-open"></i> button.</p>
	<p>Existing hosts can be modified via the <i class="icon-pencil"></i> button and can be deleted using the <i class="icon-trash"></i> button.</p>
    
    <p>The top-right menu includes the following operations:
	<ul>
	<li><b>Create Host:</b> Allows adding a host manually (although Echofish 0.4 auto-generates entries for new Hosts).</li>
	<li><b>Resolve Hosts:</b> Starts a mass operation on all Host entries to refresh the names cache via DNS.</li>
	</ul>
	</p>
    
    <p>Warning: The "Resolve Hosts" operation will refresh the (Fqdn) and (Short) fields of all Hosts from DNS using reverse lookups. Any custom FQDNs and/or Short names will be lost.</p>
    
</section>


<section id="users">
	<a name="users"></a>
	<div class="page-header">
		<h1>2. Users</h1>
	</div>
    <p class="lead">Who may enter the Echofish realm.</p>
    <p>This page allows management of users who have access to Echofish web ui.</p>
    
    <p>For each User, the following fields are included in this view:
    <ul>
    <li><b>ID:</b> A unique numeric id - automatically assigned by the system.</li>
    <li><b>Username:</b> The user login credential, used to authenticate and enter the Web UI.</li>
    <li><b>Firstname:</b> The user's firstname.</li>
    <li><b>Lastname:</b> The user's surname.</li>
    <li><b>Email:</b> The user's e-mail address.</li>
    <li><b>Superuser:</b> Boolean value for admin status ("1" means the user has admin privileges)</li>
    <li><b>Status:</b> Boolean value for enabled status ("1" means the user is active/enabled)</li>
    <li><b>Level:</b> Level of access of the user. Still unused.</li>
    <li><b>Created At:</b> User creation date (in YYYY-MM-DD hh:mm:ss format).</li>
    <li><b>Lastvisit At:</b> Holds date of user's last visit to the webUI.</li>
    </ul>
    </p>
    
    <p>The number of Users displayed per page can be adjusted by choosing one of the options from the select box on the header row.</p>

	<p>Sorting by any of the fields is possible simply by clicking on their header titles.</p>

	<p>Searching within this view can be accomplished through the text inputs beneath each header title. You may use multiple inputs to refine your search based on any of the fields. You may optionally enter a comparison operator (<, <=, >, >=, <> or =) at the beginning of each of your search values to specify how the comparison should be done.</p>
	
	<p>A more detailed view of a particular User is available through the <i class="icon-eye-open"></i> button.</p>
	<p>Existing users can be modified via the <i class="icon-pencil"></i> button and can be deleted using the <i class="icon-trash"></i> button.</p>
    
    <p>To add a new user, select the "Create User" operation from the top-right menu and provide the following details:
    <ul>
    <li><b>Username:</b> A unique username for this user.</li>
    <li><b>Firstname:</b> The user's firstname.</li>
    <li><b>Lastname:</b> The user's surname.</li>
    <li><b>Password:</b> The password for this username.</li>
    <li><b>Email:</b> The user's e-mail address.</li>
    <li><b>Superuser:</b> Use "1" for admin privileges or "0" for normal users.</li>
    <li><b>Status:</b> Use "1" to enable the user or "0" to deactivate.</li>
    <li><b>Level:</b> Leave as is ("0").</li>
    <li><b>Salt:</b> Enter a salt for this user.</li>
    </ul>
    </p>
        
    <p>Salt note: Echofish allows encrypting each user password with his own salt. Plans are to use this salt to produce secure encrypted exports, but right now it is only used on the encrypted hashes of user passwords.</p>
    
</section>



<section id="sysconf">
	<a name="sysconf"></a>
	<div class="page-header">
	<h1>3. System Configuration</h1>
	</div>
    <p class="lead">Your system configuration panel.</p>
    <p>The System Configuration option allows you to configure aspects of your Echofish installation through internal knobs. These key-value pairs are best left alone.</p>
    <ul>
       <li><code>archive_activated</code> (default:'yes') allows to control different operation modes for the archive ('yes','no'). Using 'no', which disables the archive altogether, may limit functionality of other modules that operate on the `archive` table of mysql (e.g. abuser module)</li>
       <li><code>whitelist_archived</code> (default:'no') You may wish to set this value to 'yes', but <b>only</b> when <code>archive_activated</code> is set to 'no'; with a disabled archive, the table can be used to store all deleted/acknowledged events from the syslog table</li>
       <li><code>archive_rotate</code> (default:'yes') Trim entries older than <code>archive_delete_days</code> days</li>
       <li><code>archive_delete_days</code> (default:7) How many days of archive logs to keep (when <code>archive_rotate</code> is set 'yes'); older entries will be trimmed from the archive table during rotation</li>
       <li><code>archive_delete_use_mem</code> (default:'no') Set this value to 'yes' to use ENGINE=memory for intermediate temporary table that is created during rotation (when <code>archive_rotate</code> is set 'yes')</li>
       <li><code>archive_delete_limit</code> (default:0) Setting a value here will set a maximum limit on the number of entries than can be trimmed during rotation</li>
    </ul>
    
</section>





