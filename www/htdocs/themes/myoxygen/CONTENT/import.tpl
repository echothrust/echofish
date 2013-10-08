<!-- $Id: add.tpl,v 1.1 2011/08/25 08:44:54 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>

	<label for="xml_file">XML File</label><br/>
	<input type="file" name="xml_file" id="xml_file" /><br/>

	<input type="hidden" name="import" value="import" />
<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>