<!-- $Id: edit.tpl,v 1.5 2011/09/02 10:46:55 proditis Exp $ -->
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset>
<legend>{$FORM_TITLE}</legend>

	<label for="name">{'Name'|_d}</label><br/>
	<input  autocomplete="off" type="text" id="name" name="name" size="32"  value="{$FORM.name|_eh}" /><br/>

	<label for="records_per_page">{'Records Per Page'|_d}</label><br/>
	<input  autocomplete="off" type="text" id="records_per_page" name="records_per_page" size="32"  value="{$FORM.records_per_page|_eh}" /><br/>

	<label for="theme">{'Theme'|_d}</label><br/>
	<input  autocomplete="off" type="text" id="theme" name="theme" size="32"  value="{$FORM.theme|_eh}" /><br/>
<br/>
<table id="usortable" class="ui-widget" width="100%" cellspacing="0">
<thead>
<tr style="padding: 2px;">
    <th>{"Status"|_d}</th>
	<th>{"Name"|_d}</th>
	<th>{"Container"|_d}</th>
	<th>{"Category"|_d}</th>
	<th>{"Description"|_d}</th>
</tr>
</thead>
<tbody>
{foreach item=mod from=$MODULES}
<tr class="{cycle values="odd,even"}">
	<td><select name="module[{$mod.container}][{$mod.name}]" {if $mod.category=='SYSTEM'}disabled="disabled"{/if}><option {if $Installer->installed($mod)}selected="selected"{/if}>Yes</option><option {if !$Installer->installed($mod)}selected="selected"{/if}>No</option></select></td>
	<td>{$mod.name}</td>
	<td>{$mod.container}</td>
	<td>{$mod.category}</td>
	<td>{$mod.description}</td>
</tr>
{/foreach}
</tbody>
</table>
<input type="reset" value="" class="buttonReset" />
<input type="submit" value="" class="buttonSubmit" /><br/>
</fieldset>
</form>
	