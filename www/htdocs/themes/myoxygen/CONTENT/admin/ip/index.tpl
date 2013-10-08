<!-- $Id: index.tpl,v 1.6 2011/09/01 17:48:22 proditis Exp $ -->
{include file="CONTENT/search_skel.tpl"}
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table id="usortable" width="100%" cellspacing="0">
<thead>
<tr style="padding: 2px;">
	<th>Address</th>
	<th>FQDN</th>
	<th>Description</th>
	<th>Tags</th>
{include file="CONTENT/thead.tpl"}
</tr>
</thead>
<tbody>
{foreach item=entities from=$CONTENTLIST}
<tr valign="top" class="{cycle values="odd,even"}">
	<td>{$entities.address}</td>
	<td>{$entities.fqdn}</td>
	<td>{$entities.description|escape:'htmlall'}</td>
	<td>{$entities.Tags}</td>
{include file="CONTENT/edit_delete.tpl"}
</tr>
{/foreach}
</tbody>
</table>
</fieldset>
