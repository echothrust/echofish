{php}
ob_end_clean();
ob_start();
header ("Content-Type:text/xml; charset=utf-8");
{/php}
{literal}<?xml version="1.0" encoding="UTF-8"?>{/literal}
<records>
{foreach item=object from=$CONTENTLIST}
	<record>
	{foreach item=val key=field from=$object}
		<{$field}>{$val|escape:'html'}</{$field}>
	{/foreach}
	</record>
{/foreach}{* END OF CONTENTLIST *}
</records>
{php}
ob_end_flush();
exit;
{/php}