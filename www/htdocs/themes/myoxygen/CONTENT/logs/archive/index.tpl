<script>
var uri='index.php?container={$CONTAINER}&module={$MODULE_NAME}';
{literal}
    $(document).ready(function () {
   		$("#search_options_mod").dialog({
                modal: true,
                autoOpen: false,
                draggable: true,
                resizeable: false,   
                title: 'Advanced Search',
                scroll: false,
            	buttons: {
					"Search": function() {
						document.searchForm.submit();
					}
				}
			
            });
            $('.search').click(
                function() {
                    $("#search_options_mod").dialog("open");
                    return false;
            });


  var selectionImage;
	// we need to add td.received_ts in the future.
  $('td.host, td.program, td.msg').mouseup(function(e) {
    var selection = getSelected();
    var typeofselection=$(this).attr('class');
    if(selection && (selection = new String(selection).replace(/^\s+|\s+$/g,''))) {
      if(!selectionImage) {
        selectionImage = $('<a>').attr({
          href: '#',
          title: 'Click here to learn more about this '+typeofselection,
          id: 'selection-image'
        }).hide();
        $(document.body).append(selectionImage);
      }
      selectionImage.css({
        top: e.pageY - 30,  //offsets
        left: e.pageX - 13 //offsets
      }).fadeIn().click(function() {
               var elem=document.getElementById(typeofselection);
               if(typeofselection=='host')
	               elem.value=selection;
               else
	               elem.value='%'+selection+'%';
               $("#search_options_mod").dialog("open");
               return false;
     });
    }
  });

  $(document.body).mousedown(function() {
    if(selectionImage) { selectionImage.fadeOut(); }
  });
    });
</script>
{/literal}
{include file="CONTENT/logs/syslog/search_local.tpl"}
<fieldset><!--<legend>{$FORM_TITLE}</legend>-->
<table id="usortable" class="ui-widget" width="100%" cellspacing="0">
<thead>
<tr style="padding: 2px;">
	<th nowrap="true">Date</th>
	<th>Host</th>
	<th width="60px">Priority</th>
	<th>Program</th>
	<th>Msg</th>
{include file="CONTENT/thead.tpl"}
</tr>
</thead>
<tbody>
{foreach item=entities from=$CONTENTLIST}
<tr valign="top" class="{cycle values="odd,even"}">
	<td class="received_ts">{$entities.received_ts|date_format:"%H:%M:%S %d/%m/%Y"}</td>
	<td class="host"><abbr title="{$entities.host}">{$entities.host|gethostbyaddr}</abbr></td>
	<td class="facility"><img class="ICON" src="themes/myoxygen/images/syslog/{$FACILITIES[$entities.facility]}.png" title="{$FACILITIES[$entities.facility]}"><img class="ICON" src="themes/myoxygen/images/syslog/{$LEVELS[$entities.level]}.png" title="{$LEVELS[$entities.level]}"></td>
	<td class="program" width="100px">{$entities.program|escape:'htmlall'}</td>
	<td class="msg">{$entities.msg|escape:'htmlall'}</td>
{include file="CONTENT/edit_delete.tpl"}
</tr>
{/foreach}
</tbody>
</table>
</fieldset>
