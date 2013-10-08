    <th align="right" class="adminactions" nowrap="nowrap" width="20px">
{foreach item=adminaction from=$PAGE_THEAD_ACTIONS}
    <a class="{$adminaction->taction}" id="{$adminaction->taction}" href="{$adminaction->getURI()}" title="{$adminaction->title}"><img class="ICON" src="themes/myoxygen/images/actions/{$adminaction->taction}.png" /></a>
{/foreach}
    </th>
