<!-- $Id: edit_delete.tpl,v 1.6 2011/08/31 16:33:46 proditis Exp $ -->
<td nowrap="nowrap" align="right">
{foreach item=pa from=$PAGE_ACTIONS}
	<a class="{$pa->taction}" href="{$pa->getURI()}&amp;ID={$entities.id}" title="{$pa->title}"><img src="themes/myoxygen/images/actions/{$pa->taction}.png" class="ICON"/></a>
{/foreach}
</td>