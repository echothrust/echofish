<!--
$Id: html_body.tpl,v 1.6 2011/09/02 10:17:14 proditis Exp $
-->
<body>
    <div id="header">
        <div class="wrap">
            <a href="." title=""><img src="themes/myoxygen/images/logo.png" id="logo"  alt="Logo" /></a>
{if substr($MODULE_ACTION,0,4) == 'list' && $site.ADMIN eq true}
			<input id="headsearch" name="headsearch" autocomplete="off" type="text" size="50" style="font-size: 9px; float: right; margin-right:110px;" value="quick edit..." align="right" onfocus="javascript:this.value='';"/>
    		<div id="globlive" class="livesearch"></div>
{/if}
            <div id="top">
                {if $site.username neq ""}
                 <p>{"Hello,"|_d} {$site.username}! {'Feel free to '|_d}<a href="?container=home&amp;module=home&amp;action=logout">{'logout_l'|_d}</a> {'anytime.'|_d}</p>
                {else}
                 <p>Login to use the system!</p>
                {/if}
            </div>
{include file="html_menu.tpl"}
        </div>
    </div>
{if $site.username != ""}
	<div class="breadcrumbs">
		You are here <a href="?container=home">/home</a> &gt; <a href="?container={$CONTAINER}">{$CONTAINER}</a> 
		{if $MODULE_NAME!=''}&gt; <a href="?container={$CONTAINER}&amp;module={$MODULE_NAME}">{$MODULE_NAME}</a> {/if}
		{if $MODULE_ACTION!=''}&gt; <a href="?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$MODULE_ACTION}">{$MODULE_ACTION}</a>{/if}
	</div>
{/if}
    <div class="wrap">

        <div id="left">
          {foreach item=MESSAGE from=$smarty.session.OPERATION_MESSAGES}
			<div class="ui-widget">
				<div class="ui-state-{if $MESSAGE.TYPE eq "OK"}highlight{else}error{/if} ui-corner-all" style="padding: 0pt 0.7em; margin-top: 20px;"> 
					<p><span class="ui-icon ui-icon-{if $MESSAGE.TYPE eq "OK"}info{else}error{/if}" style="float: left; margin-right: 0.3em;"></span>
					<strong>[{$MESSAGE.TYPE} Code: {$MESSAGE.CODE}] </strong>{$MESSAGE.MSG}</p>
				</div>
			</div>
          {/foreach}
{php}
    global $OPERATION_MESSAGES;
    $_SESSION['OPERATION_MESSAGES']=NULL;
    unset($OPERATION_MESSAGES);
{/php}

			<h2>{$FORM_HEADING}</h2>
            {if $MODULE_NAME neq ''}
				{if $INLINE_BUTTON.alt neq '' && $site.username neq ''}
					<a href="?container={$CONTAINER}&amp;module={$MODULE_NAME}&amp;action={$INLINE_ACTION}" class="fg-button ui-state-default ui-corner-all">{$INLINE_BUTTON|_d}</a>
					<h2> </h2>
				{/if}
                {include file="CONTENT/$CONTAINER/$MODULE_NAME/$MODULE_ACTION.tpl"}
            {/if}
{if isset($PAGER)}
            {if $PAGER->haveToPaginate()}
                <br style="clear: both" />
                <fieldset style="padding-top: 10px;border: 0px;">
                <table width="99%" style="padding:0px;" cellspacing="0">
                  <tr>
                    {capture name=next}Next Page{/capture}
                    {capture name=prev}Previous Page{/capture}
                    {capture name=mid}Pages Between{/capture}
                    <th width="20%" height="32px" class="pager" style="text-align: left;" align="left" valign="top">{if $PAGER->getPage() != '1'}<a href="{$PAGER->getPreviousPage()|build_page_url}">PREV</a>{/if}</th>
                    <th width="59%" class="pager middle"  align="center">
                    Pages:
                    {section name=pages start=$PAGER->getFirstPage() loop=$PAGER->getLastPage()+1 step=1}
                    {if $smarty.section.pages.index == $PAGER->getPage()}
                    	[{$smarty.section.pages.index}]
                    {else}
                    	<a href="{$smarty.section.pages.index|build_page_url}">[{$smarty.section.pages.index}]</a>
                    {/if}
                    {/section}
                    
                    <!--{$PAGER->getPage()}/{$PAGER->getLastPage()}--></th>
                    <th width="20%" class="pager"  valign="top" align="right">{if $PAGER->getLastPage() != $PAGER->getPage()}<a href="{$PAGER->getNextPage()|build_page_url}">NEXT</a>{/if}</th>
                  </tr>
                 </table>
                </fieldset>
            {/if}
{/if}        
        </div>
            {if is_array($ACTIONMENUS) || $site.username eq ""}
            <div id="right">
            {if $site.username eq ""}
			{literal}<script type="text/javascript"> var $j = jQuery; $j(document).ready(function(){ $j("#login_form").validate();});
</script>{/literal}
                 <form id="login_form" method="post" action="?container={$CONTAINER}&amp;module=home&amp;action=login">
                 <p>
                    <label for="login_username">Username</label><br/>
                    <input id="login_username" name="username" type="text" class="required" /><br/>
                    <label for="login_password">Password</label><br/>
                    <input id="login_password" name="password" type="password"  class="required"/><br/>
                    <input type="submit" name="Submit" value="Login" />
                  </p>
                 </form>
            {else}
              <ul id="menu_left">
                {if is_array($ACTIONMENUS)}
                {foreach key=key item=menu from=$ACTIONMENUS}<li>{if $menu==''}<hr>{else}<a href="{$menu|escape:'htmlall'}" class="actionmenu" title="{$key}">{$key|_d}</a>{/if}</li>{/foreach}
                {/if}
              </ul>
              {if $BOOKMARKS neq NULL}
              <hr>
              <center>Search Bookmarks</center>
              <ul>
{foreach item=bm from=$BOOKMARKS}
				<li><a href="{$bm->getmylink()}" title="{$bm->name}">{$bm->name}</a>
{/foreach}
              </ul>
              {/if}
            {/if}
            </div>
            {/if}
        </div>
    </div>

    <div id="sections">
        <div class="wrap">
        <div class="half">
        	{if $LEFT_TAB_TITLE neq ''}
            <h3><a>{$LEFT_TAB_TITLE}</a></h3>
            {$LEFT_TAB_MSG}
           {/if}
        </div>
        
        <div class="half last">
            {if $RIGHT_TAB_TITLE neq ''}
            <h3><a>{$RIGHT_TAB_TITLE}</a></h3>
            {$RIGHT_TAB_MSG}
            {/if}
        </div>
        <div class="clear"></div>
    </div>
    <div id="footer">
        <div class="wrap">

            <p style="float: right; text-align: right;">{$footer.poweredby}</p>
            <p>{$footer.copyright}</p>
        </div>
    </div>