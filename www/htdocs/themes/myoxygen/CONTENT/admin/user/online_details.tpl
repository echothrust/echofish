{php}
ob_end_clean();
ob_start();
{/php}
<b>IP:</b> {$CONTENT.Online->ip|long2ip}<br/>
<b>Session ID:</b> {$CONTENT.Online.session}<br/>
<b>Logged:</b> {$CONTENT.Online.created_at}<br/>
<b>Last update:</b> {$CONTENT.Online.updated_at}
{php}
ob_end_flush();
exit;
{/php}