<!-- $Id: index.tpl,v 1.5 2011/08/30 17:39:02 proditis Exp $ -->
{include file="CONTENT/search_skel.tpl"}
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table id="usortable"  class="ui-widget"  width="100%" cellspacing="0">
<thead>
<tr style="padding: 2px;">
	<th width="80px">Error No</th>
	<th>Error</th>
	<th width="40px">Line</th>
	<th>File</th>
	<th>Request</th>
{include file="CONTENT/thead.tpl"}
</tr>
</thead>
<tbody>
{foreach item=entities from=$CONTENTLIST}
<tr valign="top" class="{cycle values="odd,even"}">
	<td>{$entities.errno|escape:'htmlall'}</td>
	<td>{$entities.errstr|escape:'htmlall'}</td>
	<td>{$entities.errline|escape:'htmlall'}</td>
	<td>{$entities.errfile|escape:'htmlall'}</td>
	<td>{if isset($entities.request.container)}CONTAINER: {$entities.request.container}{/if}
	{if isset($entities.request.module)}MODULE: {$entities.request.module}{/if}
	{if isset($entities.request.action)}ACTION: {$entities.request.action}{/if}</td>
{include file="CONTENT/edit_delete.tpl"}    
</tr>
{/foreach}
</tbody>
</table>
</fieldset>
