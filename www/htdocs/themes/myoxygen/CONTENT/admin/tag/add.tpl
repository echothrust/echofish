<!-- $Id: add.tpl,v 1.1 2011/08/25 08:44:52 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
{if $MODULE_ACTION=='edit'}
	<input type="hidden" name="id" id="id" value="{$FORM.id}"/>
{/if}
	<label for="name">name</label><br/>
	<input  autocomplete="off" type="text" id="name" name="name" size="32"  value="{$FORM.name|escape:'htmlall'}" /><br/>

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	