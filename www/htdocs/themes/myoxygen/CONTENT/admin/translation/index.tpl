<!-- $Id: index.tpl,v 1.5 2011/08/31 16:33:45 proditis Exp $ -->

<script>
var url='index.php?container={$CONTAINER}&module={$MODULE_NAME}&action=add'
{literal}
    $(document).ready(function () {
		$( "#translate_dialog:ui-dialog" ).dialog( "destroy" );
        $( "#usortable thead" ).addClass("ui-widget-header");
        $( "#usortable tbody" ).addClass("ui-widget-content");
		$("#translate_dialog").dialog({
                modal: true,
                autoOpen: false,
                height: '300',
                width: '200',
                draggable: true,
                resizeable: false,   
                title: '{/literal}Add Translation{literal}',
                scroll: false
            });
            $('.translate').click(
                function() {
					tr=$(this).closest("tr");
					msgid=tr.find('.msgid').html();
					$("#msgid").val(msgid);
                	document.actionForm.action=url;
                	document.actionForm.msgid.value=msgid;
                    $("#translate_dialog").dialog("open");
                    return false;
            });
        
    });
</script>
{/literal}
{include file="CONTENT/search_skel.tpl"}
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table  id="usortable" width="100%" cellspacing="0" >
<thead>
<tr style="padding: 2px;">
	<th width="250px">msgid</th>
	<th>msgstr</th>
	<th width="100px">Language</th>
{include file="CONTENT/thead.tpl"}
</tr>
</thead>
<tbody>
{foreach item=entities from=$CONTENTLIST}
<tr class="{cycle values="odd,even"}">
	<td class="msgid">{$entities.msgid|_eh}</td>
	<td class="msgstr">{$entities.msgstr|_eh}</td>
	<td class="language">{$entities.Language->name|_eh}</td>
{include file="CONTENT/edit_delete.tpl"}
</tr>
{/foreach}
</tbody>
</table>
</fieldset>

<div id="translate_dialog" title="Add a new Translation">
{include file="CONTENT/$CONTAINER/$MODULE_NAME/add.tpl"}
</div>
