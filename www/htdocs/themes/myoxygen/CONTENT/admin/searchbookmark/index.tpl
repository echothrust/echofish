<!-- $Id: index.tpl,v 1.1 2011/08/25 08:44:52 proditis Exp $ -->
{include file="CONTENT/search_skel.tpl"}
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table id="usortable" class="ui-widget" width="100%" cellspacing="0">
<thead>
<tr style="padding: 2px;">
	<th>Name</th>
	<th>User</th>
	<th>Container</th>
	<th>Module</th>
	<th>Action</th>
	<th>Query</th>
{include file="CONTENT/thead.tpl"}
</tr>
</thead>
<tbody>
{foreach item=entities from=$CONTENTLIST}
<tr class="{cycle values="odd,even"}">
	<td>{$entities.name|_eh}</td>
	<td>{$entities.User->username}</td>
	<td>{$entities.container|_eh}</td>
	<td>{$entities.module|_eh}</td>
	<td>{$entities.action|_eh}</td>
	<td>{$entities.querystring|_eh}</th>
{include file="CONTENT/edit_delete.tpl"}
</tr>
{/foreach}
</tbody>
</table>
</fieldset>
