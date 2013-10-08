<!-- $Id: index.tpl,v 1.3 2011/08/31 18:39:56 proditis Exp $ -->
{include file="CONTENT/search_skel.tpl"}
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table id="usortable" width="100%" cellspacing="0">
<thead>
<tr style="padding: 2px;">
	<th>Name</th>
	<th>Description</th>
	<th>Program</th>
	<th>Pattern</th>
{include file="CONTENT/thead.tpl"}
</tr>
</thead>
<tbody>
{foreach item=entities from=$CONTENTLIST}
<tr valign="top" class="{cycle values="odd,even"}">
	<td>{$entities.name|_eh}</td>
	<td>{$entities.description|_eh}</td>
	<td>{$entities.program|_eh}</td>
	<td>{$entities.pattern|_eh}</td>
{include file="CONTENT/edit_delete.tpl"}
</tr>
{/foreach}
</tbody>
</table>
</fieldset>
