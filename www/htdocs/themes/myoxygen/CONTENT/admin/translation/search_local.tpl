<form id="searchForm" name="searchForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action=search' method="post">
<fieldset>
<legend>Advanced Search</legend>
	<label for="msgid">msgid</label><br/>
	<input  autocomplete="off" type="text" id="msgid" name="msgid" size="32"  value="{$FORM.msgid|escape:'htmlall'}" /><br/>

	<label for="msgid">msgstr</label><br/>
	<input  autocomplete="off" type="text" id="msgstr" name="msgstr" size="32"  value="{$FORM.msgstr|escape:'htmlall'}" /><br/>

	<label for="language_id">Language</label><br>
	<select name="language_id">
{foreach from=$LANGUAGES item=language}
		<option value="{$language.id}">{$language.name|_eh}</option>
{/foreach}		
	</select>
</fieldset>
</form>