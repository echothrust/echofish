<!-- $Id: edit.tpl,v 1.2 2011/08/30 17:39:01 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
	<input type="hidden" name="id" id="id" value="{$FORM.id}"/>
	<label for="firstname">Firstname</label><br/>
	<input tooltip="Enter the Firstname of the user" class="tooltips" autocomplete="off" type="text" id="firstname" name="firstname" size="32"  value="{$FORM.firstname|_eh}" /><br/>

	<label for="lastname">Lastname</label><br/>
	<input tooltip="Enter the Lastname of the user" class="tooltips" autocomplete="off" type="text" id="lastname" name="lastname" size="32"  value="{$FORM.lastname|_eh}" /><br/>

	<label for="email">Email</label><br/>
	<input tooltip="Enter a mail address for the user" class="tooltips" autocomplete="off" type="text" id="email" name="email" size="32" value="{$FORM.email|_eh}"/><br/>

	<label for="username">Username</label><br/>
	<input tooltip="A username that user will use to login" class="tooltips" autocomplete="off" type="text" id="username" name="username" size="32"  value="{$FORM.username|_eh}" /><br/>

	<label for="group_id">Group</label><br/>
	<select name="group_id">
{foreach item=group from=$Groups}
		<option value="{$group.id}" {if $group.id == $FORM.Groups[0].id}selected="selected"{/if}>{$group.name|escape:'htmlall'}</option>
{/foreach}
</select><br/>

	<label for="password">Password</label><br/>
	<input tooltip="Enter Password. Note the password will show up as *" class="tooltips" autocomplete="off" type="password" id="password" name="password" size="32" value="{$FORM.password|escape:'htmlall'}"/><br/>

	<label for="vpassword">Password Again</label><br/>
	<input tooltip="Verify Password. Make sure password is the same as before." class="tooltips" autocomplete="off" type="password" id="vpassword" name="vpassword" size="32" value="{$FORM.vpassword|escape:'htmlall'}"/><br/>
	

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	