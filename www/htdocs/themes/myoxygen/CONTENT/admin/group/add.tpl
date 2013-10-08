<!-- $Id: add.tpl,v 1.4.2.1 2011/09/02 12:03:30 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
{if $MODULE_ACTION == 'edit'}
	<input type="hidden" name="id" id="id" value="{$FORM.id}"/>
{/if}
	<label for="name">{'Name'|_d}</label><br/>
	<input  autocomplete="off" type="text" id="name" name="name" size="32"  value="{$FORM.name|escape:'htmlall'}" /><br/>

	<label for="perm">{'Perm'|_d}</label><br/>
	<select name="perm">
{foreach item=permid key=name from=$PERMS}	
		<option {if $FORM.perm==$name}selected="selected"{/if}>{$name|_d}</option>
{/foreach}
	</select><br/>
<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	