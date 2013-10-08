<form id="searchForm" name="searchForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action=search' method="post">
<fieldset>
<legend>Advanced Search</legend>
	<label for="username">Username</label><br/>
	<input autocomplete="off" type="text" id="username" name="username" size="32"  value="{$FORM.username|_eh}" /><br/>

	<label for="email">Email</label><br/>
	<input  autocomplete="off" type="text" id="email" name="email" size="32" value="{$FORM.email|_eh}"/><br/>

	<label for="firstname">Firstname</label><br/>
	<input  autocomplete="off" type="text" id="firstname" name="firstname" size="32"  value="{$FORM.firstname|_eh}" /><br/>

	<label for="lastname">Lastname</label><br/>
	<input  autocomplete="off" type="text" id="lastname" name="lastname" size="32"  value="{$FORM.lastname|_eh}" /><br/>

</fieldset>
</form>