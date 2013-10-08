<div name="search_options" id="search_options" style="overflow: hidden;">
<form id="searchForm" name="searchForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action=search' method="post">
<fieldset>
<legend>Advanced Search</legend>
	<label for="name">name</label><br/>
	<input autocomplete="off" type="text" id="name" name="name" size="32"  value="{$FORM.name|_eh}" /><br/>
	<label for="program">Program</label><br/>
	<input autocomplete="off" type="text" id="program" name="program" size="32"  value="{$FORM.program|_eh}" /><br/>
	<label for="pattern">Pattern</label><br/>
	<input autocomplete="off" type="text" id="pattern" name="pattern" size="32"  value="{$FORM.pattern|_eh}" /><br/>
</fieldset>
</form>
</div>
