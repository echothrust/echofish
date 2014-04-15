<?php
/* @var $this DefaultController */
$this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name).' Syslog Help',
)); ?>
<?php $this->endWidget();?>
<?php 
/*
switch($section)
{
	case 'logs': 
	  echo $this->renderPartial('logs',array('no'=>1));
	  break;
	case 'archive':
	  echo $this->renderPartial('archive',array('no'=>1));
	  break;
	case 'facility':
	  echo $this->renderPartial('facility',array('no'=>1));
	  break;
	case 'severity':
	  echo $this->renderPartial('severity',array('no'=>1));
	  break;
	  
	default:
	  echo $this->renderPartial('logs',array('no'=>1));
	  echo $this->renderPartial('archive',array('no'=>2));
	  echo $this->renderPartial('facility',array('no'=>3));
	  echo $this->renderPartial('severity',array('no'=>4));
	  break;
}
 */
?>
<section id="toc">
	<h3>Help topics</h3>
	<h4><a href="#logs">1. Logs</a></h4>
	<h4><a href="#archive">2. Archive</a></h4>
	<h4><a href="#facilities">3. Facilities</a></h4>
	<h4><a href="#severities">4. Severities</a></h4>
</section>

<section id="logs">
	<a name="logs"></a>
    <div class="page-header">
        <h1>1. Logs</h1>
    </div>

    <p class="lead">List view of filtered log entries</p>
    
	<p>This view provides a paginated list of log entries, after all filtering takes place. Filtering rules that are defined in Lists/Whitelists will only let irregular events reach this view.</p>

	<p>The number of messages displayed per page can be adjusted by choosing one of the options from the select box on the header row.</p>
	
	<p>Besides the message text (Msg) for each log entry, this view includes:
		<ul>
		<li>(Received Ts) The exact date and time the message was received by Echofish.</li>
		<li>(Host IP) The IP address of the host that sent the message to syslog.</li>
		<li>(Facility) Description of the RFC 5424 facility code, indicating the type of software that generated the message. Default descriptions are provided for all known codes, but they may be customized in Syslog/Facilities.</li>
		<li>(Level) Description of the RFC 5424 severity level assigned to the message by the sending host. Default descriptions may be customized in Syslog/Severities.</li>
		<li>(Program) The name of the application that generated the message.</li>
	</ul>
	</p>

	<p>Sorting by any of the fields is possible simply by clicking on their header titles.</p>
	
	<p>Searching within this view can be accomplished through the text inputs beneath each header title. You may use multiple inputs to refine your search based on any of the fields. You may optionally enter a comparison operator (<, <=, >, >=, <> or =) at the beginning of each of your search values to specify how the comparison should be done. For (Facility) and (Level) searches, the application expects RFC 5424 facility code and severity level respectively.</p>
	
	<p>Acknowledging one or more messages will remove them from this view.
		<ul>
		<li>Use the <i class="icon-ok"></i> button in the search row to massively acknowledge any entries matching the search criteria.</li>
		<li>Clicking the <i class="icon-ok"></i> button that appears on each one of the message rows will acknowledge that entry and any other identical entries (matching facility, level, host and message).</li>
		<li>Alternatively, to massively acknowledge only certain log messages, you may use the checkboxes and the "Mass Acknowledge Selected" button.</li>
		</ul>
		Acknowledged entries will disapear from Syslog/Logs but will remain in Syslog/Archive for future retrieval until next Archive truncation.
	</p>
	
	<p>Use the <i class="icon-eye-close"></i> button next to each event message to create a whitelist filter rule for that event (i.e. to auto-acknowledge future identical or similar events). 
	This will load the "Create Whitelist" form with its	fields filled from that log entry, allowing you to use your own log messages as rule templates, requiring little or no modification. See Lists/Whitelist for more.</p>
	
	<p>In the same manner, the <i class="icon-screenshot"></i> button next to event messages allows creating an Abuser Trigger rule to keep track of Abuser IP addresses from future similar events. 
	This will fill the form "Create Abuser Trigger" with values from that log entry. See Abuser/Triggers for more.</p>
	
</section>
<hr>



<section id="archive">
	<a name="archive"></a>
    <div class="page-header">
        <h1>2. Archive</h1>
    </div>

    <p class="lead">Unfiltered list view of all log entries.</p>

    <p>This view provides a paginated list of all log messages, that your Echofish installation has received since the last truncation. This view is meant to be a centralized store of intact log entries, unaffected by any user-defined filtering rules in Lists/Whitelists.</p> 
    
    <p>The archive may be truncated by clicking on the "Truncate Archive" link from the Operations menu (top-right of the screen).</p>

	<p>The number of messages displayed per page can be adjusted by choosing one of the options from the select box on the header row.</p>
	
	<p>Besides the message text (Msg) for each log entry, this view includes:
		<ul>
		<li>(Received Ts) The exact date and time the message was received by Echofish.</li>
		<li>(Host IP) The IP address of the host that sent the message to syslog.</li>
		<li>(Facility) Description of the RFC 5424 facility code, indicating the type of software that generated the message. Default descriptions are provided for all known codes, but they may be customized in Syslog/Facilities.</li>
		<li>(Level) Description of the RFC 5424 severity level assigned to the message by the sending host. Default descriptions may be customized in Syslog/Severities.</li>
		<li>(Program) The name of the application that generated the message.</li>
	</ul>
	</p>
	
	<p>Sorting by any of the fields is possible simply by clicking on their header titles.</p>
	
	<p>Searching within this view can be accomplished through the text inputs beneath each header title. You may use multiple inputs to refine your search based on any of the fields. You may optionally enter a comparison operator (<, <=, >, >=, <> or =) at the beginning of each of your search values to specify how the comparison should be done. For (Facility) and (Level) searches, the application expects RFC 5424 facility code and severity level respectively.</p>
	
</section>
<hr>




<section id="facilities">
	<a name="facilities"></a>
    <div class="page-header">
        <h1>3. Facilities</h1>
    </div>

    <p class="lead">List of syslog message facilities.</p>

    <p>The facility of a received message specifies what type of software generated the message.</p>
    
    <p>RFC 5424 defines the following facility codes, which are already defined in the default installation:</p>
    
    <table>
	<thead><tr>
	<th>Num</th>
	<th>Name</th>
	<th>Description</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td>0</td>
	<td>kern</td>
	<td>kernel messages</td>
	</tr>
	<tr>
	<td>1</td>
	<td>user</td>
	<td>user-level messages</td>
	</tr>
	<tr>
	<td>2</td>
	<td>mail</td>
	<td>mail system</td>
	</tr>
	<tr>
	<td>3</td>
	<td>daemon</td>
	<td>system daemons</td>
	</tr>
	<tr>
	<td>4</td>
	<td>auth</td>
	<td>security/authorization messages</td>
	</tr>
	<tr>
	<td>5</td>
	<td>syslog</td>
	<td>messages generated internally by syslogd</td>
	</tr>
	<tr>
	<td>6</td>
	<td>lpr</td>
	<td>line printer subsystem</td>
	</tr>
	<tr>
	<td>7</td>
	<td>news</td>
	<td>network news subsystem</td>
	</tr>
	<tr>
	<td>8</td>
	<td>uucp</td>
	<td>UUCP subsystem</td>
	</tr>
	<tr>
	<td>9</td>
	<td>cron</td>
	<td>clock daemon</td>
	</tr>
	<tr>
	<td>10</td>
	<td>authpriv</td>
	<td>security/authorization messages</td>
	</tr>
	<tr>
	<td>11</td>
	<td>ftp</td>
	<td>FTP daemon</td>
	</tr>
	<tr>
	<td>12</td>
	<td>ntp</td>
	<td>NTP subsystem</td>
	</tr>
	<tr>
	<td>13</td>
	<td>logaudit</td>
	<td>log audit</td>
	</tr>
	<tr>
	<td>14</td>
	<td>logalert</td>
	<td>log alert</td>
	</tr>
	<tr>
	<td>15</td>
	<td>cron</td>
	<td>clock daemon</td>
	</tr>
	<tr>
	<td>16</td>
	<td>local0</td>
	<td>local use 0 (local0)</td>
	</tr>
	<tr>
	<td>17</td>
	<td>local1</td>
	<td>local use 1 (local1)</td>
	</tr>
	<tr>
	<td>18</td>
	<td>local2</td>
	<td>local use 2 (local2)</td>
	</tr>
	<tr>
	<td>19</td>
	<td>local3</td>
	<td>local use 3 (local3)</td>
	</tr>
	<tr>
	<td>20</td>
	<td>local4</td>
	<td>local use 4 (local4)</td>
	</tr>
	<tr>
	<td>21</td>
	<td>local5</td>
	<td>local use 5 (local5)</td>
	</tr>
	<tr>
	<td>22</td>
	<td>local6</td>
	<td>local use 6 (local6)</td>
	</tr>
	<tr>
	<td>23</td>
	<td>local7</td>
	<td>local use 7 (local7)</td>
	</tr>
	</tbody></table>
	
	<br />
	
    <p>This view allows modifying the existing facilities via the <i class="icon-pencil"></i> button, deleting them using the <i class="icon-trash"></i> button, or even adding your own facilities via the "Create Facility" link in top-right Operations menu.</p>
    
    <p>Such modifications will not be normally needed, but they are there to cover the non-uniform mapping of facilities between the different syslog implementations that exist over different operating systems.</p>
</section>
<hr>





<section id="severities">
	<a name="severities"></a>
    <div class="page-header">
        <h1>4. Severities</h1>
    </div>

    <p class="lead">List of syslog message severity levels.</p>

    <p>Each message in syslog is assigned with a Severity Level by the sending program. Echofish includes by default the 8 severity levels defined by RFC 5424:</p>
     
	<table>
	<thead><tr>
		<th>Num</th>
		<th>Name</th>
		<th>Description</th>
		<th>General Description</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>0</td>
		<td>emerg (panic)</td>
		<td>System is unusable (Emergency).</td>
		<td>A "panic" condition usually affecting multiple apps/servers/sites. At this level it would usually notify all tech staff on call.</td>
	</tr>
	<tr>
		<td>1</td>
		<td>alert</td>
		<td>Action must be taken immediately.</td>
		<td>Should be corrected immediately, therefore notify staff who can fix the problem. An example would be the loss of a primary ISP connection.</td>
	</tr>
	<tr>
		<td>2</td>
		<td>crit</td>
		<td>Critical conditions.</td>
		<td>Should be corrected immediately, but indicates failure in a secondary system, an example is a loss of a backup ISP connection.</td>
	</tr>
	<tr>
		<td>3</td>
		<td>err (error)</td>
		<td>Error conditions.</td>
		<td>Non-urgent failures, these should be relayed to developers or admins; each item must be resolved within a given time.</td>
	</tr>
	<tr>
		<td>4</td>
		<td>warning (warn)</td>
		<td>Warning conditions.</td>
		<td>Warning messages, not an error, but indication that an error will occur if action is not taken, e.g. file system 85% full - each item must be resolved within a given time.</td>
	</tr>
	<tr>
		<td>5</td>
		<td>notice</td>
		<td>Normal but significant condition.</td>
		<td>Events that are unusual but not error conditions - might be summarized in an email to developers or admins to spot potential problems - no immediate action required.</td>
	</tr>
	<tr>
		<td>6</td>
		<td>info</td>
		<td>Informational messages.</td>
		<td>Normal operational messages - may be harvested for reporting, measuring throughput, etc. - no action required.</td>
	</tr>
	<tr>
		<td>7</td>
		<td>debug</td>
		<td>Debug-level messages.</td>
		<td>Info useful to developers for debugging the application, not useful during operations.</td>
	</tr>
	</tbody></table>
	
	<br />
	
    <p>Messages with a lower numerical SEVERITY value have a higher practical severity than those with a numerically higher value.</p>
    
    <p>This view allows modifying the existing severity levels via the <i class="icon-pencil"></i> button, deleting them using the <i class="icon-trash"></i> button, or even adding your own severities via the "Create Severity" link in top-right Operations menu.</p>
    
    <p>Adding a new severity should not be normally needed, since the syslog message format only allows values (0-7). It is here to cover special use cases, where the sending program does not use syslog and needs severity level(s) of its own.</p>
    
</section>
<hr>
