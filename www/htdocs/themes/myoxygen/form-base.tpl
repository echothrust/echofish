<!-- $Id: form-base.tpl,v 1.2 2011/08/30 17:39:01 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?module={$MODULE_NAME}&amp;action={$MODULE_ACTION|substr:4}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>
{if $FORM.ID neq ""}
<input type="hidden" name="ID" value="{$FORM.ID}" />
{/if}
{php}
	$TEMP_FORM=str_replace('edit','add',$this->get_template_vars('MODULE_ACTION'));
	$this->assign('TEMP_FORM',$TEMP_FORM);
{/php}
{include file="FORMS/$MODULE_NAME-$TEMP_FORM.tpl"}
<br/>

<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
