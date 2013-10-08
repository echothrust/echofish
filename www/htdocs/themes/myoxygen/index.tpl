{if substr($MODULE_ACTION,0,4) eq 'ajax'}
{$CONTENT}
{else}
{config_load file='icon.conf'}
{assign var='TICK_ICON' value='<img class="ICON" src="themes/myoxygen/images/tick.png">'}
{assign var='MAP_ICON' value='<img width="22px" align="center" src="themes/myoxygen/images/maplink.png">'}
{assign var='HOMEPAGE_ICON' value='<img width="22px" align="center" src="themes/myoxygen/images/homepage.png">'}
{assign var='UNTICK_ICON' value='<img class="ICON"src="themes/myoxygen/images/untick.png">'}
{assign var='ONLINE_IMG' value='<img src="themes/myoxygen/images/user_status_online.png">'}
{assign var='OFFLINE_IMG' value='<img src="themes/myoxygen/images/user_status_offline.png">'}
{assign var='ACK_ICON' value='<img src="themes/myoxygen/images/acknowledge.png" class="ICON"/>'}
{assign var='RM_ICON' value='<img src="themes/myoxygen/images/rmlink.png" />'}
{assign var='VI_ICON' value='<img src="themes/myoxygen/images/viline.png" />'}
{assign var='WHITELIST_ICON' value='<img src="themes/myoxygen/images/actions/whitelist.png" class="ICON"/>'}
{include file='html_header.tpl'}
{include file='html_body.tpl'}
{include file='html_footer.tpl'}
{/if}{* 
 * $Id: index.tpl,v 1.3 2011/08/30 17:39:01 proditis Exp $
 *}