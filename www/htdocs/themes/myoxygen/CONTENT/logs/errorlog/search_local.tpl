<form id="searchForm" name="searchForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action=search' method="post">
<fieldset>
<legend>Advanced Search</legend>
	<label for="errno">Errno</label><br/>
	<input  autocomplete="off" type="text" id="errno" name="errno" size="10"  value="{$FORM.errno}" /><br/>

	<label for="errstr">Errstr</label><br/>
	<input  autocomplete="off" type="text" id="errstr" name="errstr" size="10"  value="{$FORM.errstr}" /><br/>

	<label for="errline">Errline</label><br/>
	<input  autocomplete="off" type="text" id="errline" name="errline" size="10"  value="{$FORM.errline}" /><br/>

	<label for="errfile">Errfile</label><br/>
	<input  autocomplete="off" type="text" id="errfile" name="errfile" size="10"  value="{$FORM.errfile}" /><br/>

</fieldset>
</form>