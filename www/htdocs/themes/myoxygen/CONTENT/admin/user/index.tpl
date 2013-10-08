<!-- $Id: index.tpl,v 1.4 2011/08/30 17:39:01 proditis Exp $ -->
<script>
var uri='index.php?container={$CONTAINER}&module={$MODULE_NAME}';
{literal}
$(document).ready(function () {
		$('.popupable').mouseover(function(){
			var button = $(this);
			var ID=button.parents('tr').find('.ID').html();
			//load data asynchronously when mouse is over...
			$.get(uri+'&action=online_details&ID='+ID, function(data) {	button.SetBubblePopupInnerHtml(data, false); }); 
		});

    });
</script>
{/literal}
{include file="CONTENT/search_skel.tpl"}
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table id="usortable" class="ui-widget" width="100%" cellspacing="0">
<thead>
<tr style="padding: 2px;">
	<th>ID</th>
	<th>Username</th>
	<th>Full Name</th>
	<th>Group</th>
	<th>Perm</th>
{include file="CONTENT/thead.tpl"}
</tr>
</thead>
<tbody>
{foreach item=entities from=$CONTENTLIST}
<tr class="{cycle values="odd,even"}">
	<td class="ID">{$entities.id|intval}</td>
	<td align="left" valign="middle">{if $entities->Online->exists()}<a href="?container={$CONTAINER}&module={$MODULE_NAME}&action=logout&ID={$entities->id}">{$ONLINE_IMG}</a>{else}{$OFFLINE_IMG}{/if} {if $entities->Online->exists()}<a class="popupable" href="#">{/if}{$entities->username|_eh}{if $entities->Online->exists()}</a>{/if}</td>
	<td>{$entities.lastname|_eh}, {$entities.firstname|_eh}</td>
	<td>{$entities.Groups[0]->name|_eh}</td>
	<td>{$entities.Groups[0]->perm|_eh}</td>
{include file="CONTENT/edit_delete.tpl"}

</tr>
{/foreach}
</tbody>
</table>
</fieldset>
