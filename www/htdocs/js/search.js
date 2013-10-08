$(document).ready(function () {
   		$("#search_options").dialog({
                modal: true,
                autoOpen: false,
                draggable: true,
                resizeable: false,   
                title: 'Advanced Search',
                scroll: false,
            	buttons: {
				"Search": function() {
					document.searchForm.submit();
				}}
			
            });
            $('.search').click(
                function() {
                    $("#search_options").dialog("open");
                    return false;
            });
 });