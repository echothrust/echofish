<div name="search_options_mod" id="search_options_mod" style="overflow: hidden;">
<form id="searchForm" name="searchForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action=search' method="post">
<fieldset>
<legend>Advanced Search</legend>
	<label for="host">Host</label><br/>
	<input autocomplete="off" type="text" id="host" name="host" size="32"  value="{$FORM.host|_eh}" /><br/>

	<label for="program">Program</label><br/>
	<input autocomplete="off" type="text" id="program" name="program" size="32" value="{$FORM.program|_eh}"/><br/>

	<label for="msg">Message</label><br/>
	<input  autocomplete="off" type="text" id="msg" name="msg" size="32"  value="{$FORM.msg|_eh}" /><br/>
	<input type="hidden" id="acknowledge" name="acknowledge" value="0"/>

</fieldset>
</form>
</div>
