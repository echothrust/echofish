<!-- $Id: change_password.tpl,v 1.2 2011/08/30 17:39:02 proditis Exp $ -->

{literal}
<script>
 var $j = jQuery;
 $j(document).ready(function(){
       $j("#actionForm").validate({
  rules: {
    password: "required",
	vpassword: { equalTo: "#password" }
  }}); });
</script>
{/literal}


<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
	<label for="password">Password</label><br/>
    <input id="password" type="password" name="password" value="{$FORM.password}" class="required"><br/>
    
    <label for="vpassword">Verify Password</label><br/>
    <input id="vpassword" type="password" name="vpassword" value="{$FORM.vpassword}" class="required"><br/>


<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	