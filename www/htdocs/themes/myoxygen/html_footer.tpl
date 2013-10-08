</body>
<script type="text/javascript">
{if substr($MODULE_ACTION,0,4) == 'list'}
var SEARCH_URI='index.php?module={$MODULE_NAME}&action={$MODULE_ACTION|replace:"list_":"get_edit_"}&ID='
{literal}
        new Ajax.Autocompleter("headsearch", "globlive", 
        {/literal}"index.php?module=ajax&action={$MODULE_ACTION|replace:"list_":"search_"}",{literal}{paramName: "text", minChars: 3, afterUpdateElement:function(text,li){window.location=SEARCH_URI+li.id;}});

{/literal}
{/if}
</script>

</html>

<!--
$Id: html_footer.tpl,v 1.2 2011/08/30 17:39:01 proditis Exp $
-->
