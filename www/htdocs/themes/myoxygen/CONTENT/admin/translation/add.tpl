<!-- $Id: add.tpl,v 1.4 2011/08/31 16:33:45 proditis Exp $ -->
<script type="text/javascript" src="js/form-styler.js"></script>
<script type="text/javascript" src="js/tooltip-addedit.js"></script>
<script>
{literal}
	$(function() { $("form").form(); });
{/literal}
</script>
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
<table>
{if $MODULE_ACTION == 'edit'}
	<input type="hidden" name="id" id="id" value="{$FORM.id}"/>
{/if}
<tr>
	<td>
	<label for="msgid">msgid</label><br/>
	<input  autocomplete="off" type="text" id="msgid" name="msgid" size="20"  value="{$FORM.msgid|_eh}" />
	</td>
</tr>
<tr>
	<td>
	<label for="msgstr">msgstr</label><br/>
	<textarea name="msgstr" cols="12">{$FORM.msgstr|_eh}</textarea><br/>
	</td>
</tr>

<tr>
	<td>
	<label for="language_id">Language</label><br>
	<select name="language_id">
{foreach from=$LANGUAGES item=language}
		<option value="{$language.id}">{$language.name|_eh}</option>
{/foreach}		
	</select>
	</td>
</tr>
</table>

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	