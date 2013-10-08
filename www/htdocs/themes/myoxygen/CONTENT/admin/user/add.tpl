<!-- $Id: add.tpl,v 1.4 2011/09/01 16:56:11 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
	<label for="firstname">Firstname</label><br/>
	<input  autocomplete="off" type="text" id="firstname" name="firstname" size="32"  value="{$FORM.firstname|_eh}" /><br/>

	<label for="lastname">Lastname</label><br/>
	<input  autocomplete="off" type="text" id="lastname" name="lastname" size="32"  value="{$FORM.lastname|_eh}" /><br/>

	<label for="email">Email</label><br/>
	<input  autocomplete="off" type="text" id="email" name="email" size="32" value="{$FORM.email|_eh}"/><br/>

	<label for="username">Username</label><br/>
	<input  autocomplete="off" type="text" id="username" name="username" size="32"  value="{$FORM.username|_eh}" /><br/>

	<label for="group_id">Group</label><br/>
	<select name="group_id">
{foreach item=group from=$Groups}
		<option value="{$group.id}">{$group.name|escape:'htmlall'}</option>
{/foreach}
</select><br/>

	<label for="password">Password</label><br/>
	<input autocomplete="off" type="password" id="password" name="password" size="32" value="{$FORM.password|escape:'htmlall'}"/><br/>

	<label for="vpassword">Password Again</label><br/>
	<input  autocomplete="off" type="password" id="vpassword" name="vpassword" size="32" value="{$FORM.vpassword|escape:'htmlall'}"/><br/>


	

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	