<!-- $Id: missing.tpl,v 1.3 2011/08/30 17:39:00 proditis Exp $ -->
{literal}
<script>
    $(document).ready(function () {
        $( "#usortable thead" ).addClass("ui-widget-header");
        $( "#usortable tbody" ).addClass("ui-widget-content");
    });
</script>
{/literal}
<form id="actionForm" name="actionForm" enctype="multipart/form-data" action='?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}' method="post">
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table  id="usortable" width="100%" cellspacing="0" >
<thead>
<tr style="padding: 2px;">
	<th width="250px">msgid</th>
	<th>msgstr</th>
	<th>Translation</th>
</tr>
</thead>
<tbody>
<input type="hidden" name="language_id" value="{$smarty.get.language_id}">
{foreach item=entities from=$CONTENTLIST}
<tr class="{cycle values="odd,even"}">
	<td class="msgid">{$entities.msgid|_eh}</td>
	<td class="msgstr">{$entities.msgstr|_eh}</td>
	<td class="msgstr"><input type="text" size="60" name="translation[{$entities.msgid|_eh}]" value="{$entities.translation|_eh}" /></td>
</tr>
{/foreach}
</tbody>
</table>
</fieldset>
<input type="submit" value="" style="visibility: hidden;"/>
</form>