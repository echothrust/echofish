            <ul id="menu">
{foreach key=icon item=menuitem from=$MAINMENUS}
		{if ("$CONTAINER" eq $menuitem.title) || ($MODULE_NAME eq "home" and $menuitem.title eq "?")}
				<li  class="active">
				
					<a id="{$icon}" class="active" href="{$menuitem.URL|escape:'htmlall'}">
						<img class="menuicon" src="themes/myoxygen/images/menu/crystal/{$menuitem.title}.png" alt="{$menuitem.title|capitalize:true|_d}">
						<p>{$menuitem.title|replace:"_":" "|capitalize|_d}</p>
					</a>
				</li>
		{else}
				<li>
					<a href="{$menuitem.URL|escape:'htmlall'}">
						<img id="{$icon}" class="menuicon" src="themes/myoxygen/images/menu/crystal/{$menuitem.title}.png" alt="{$menuitem.title|capitalize:true}">
					<p>{$menuitem.title|replace:"_":" "|capitalize}</p>
					</a>
				</li>
				
		{/if}
{/foreach}
            </ul>
