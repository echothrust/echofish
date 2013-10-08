<!-- $Id: add.tpl,v 1.1.1.1 2011/08/11 15:56:45 proditis Exp $ -->
{literal}
<script>
 $(document).ready(function(){
       $("#actionForm").validate({
  rules: {
    name: "required"  }}); });
</script>
{/literal}
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
{if $MODULE_ACTION == 'edit'}
	<input type="hidden" name="id" id="id" value="{$FORM.id}"/>
{/if}
	<label for="name">Name</label><br/>
	<input type="text" name="name" id="name" value="{$FORM.name}"/> <br/>

	<label for="description">Description</label><br/>
	<textarea id="description" name="description"  cols="70" rows="5">{$FORM.description|escape:'htmlall'}</textarea><br/>

	<label for="program">Program</label><br/>
	<input type="text" name="program" id="program" value="{$FORM.program}"/> <br/>

	<label for="pattern">Patern</label><br/>
	<textarea id="pattern" name="pattern"  cols="70" rows="5">{$FORM.pattern|escape:'htmlall'}</textarea><br/>

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	