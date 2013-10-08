<div name="search_options" id="search_options" style="overflow: hidden;">
<form id="searchForm" name="searchForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action=search' method="post">
<fieldset>
<legend>Advanced Search</legend>
	<label for="description">Description</label><br/>
	<input autocomplete="off" type="text" id="description" name="description" size="32"  value="{$FORM.description|_eh}" /><br/>
	<label for="address">Address</label><br/>
	<input autocomplete="off" type="text" id="address" name="address" size="32"  value="{$FORM.address}" /><br/>
	<label for="fqdn">FQDN</label><br/>
	<input autocomplete="off" type="text" id="fqdn" name="fqdn" size="32"  value="{$FORM.fqdn}" /><br/>
</fieldset>
</form>
</div>
