<!-- $Id: add.tpl,v 1.3 2011/09/01 16:55:38 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
{if $MODULE_ACTION=='edit'}
	<input type="hidden" name="id" id="id" value="{$FORM.id}"/>
{/if}
	<label for="name">Name</label><br/>
	<input  autocomplete="off" type="text" id="name" name="name" size="32"  value="{$FORM.name|escape:'htmlall'}" /><br/>

	<label for="code">Code</label><br/>
	<input  autocomplete="off" type="text" id="code" name="code" size="32"  value="{$FORM.code|escape:'htmlall'}" /><br/>

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	