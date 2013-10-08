$(document).ready(function () {
	    $("#usortable thead").addClass("ui-widget-header");
        $("#usortable tbody").addClass("ui-widget-content");
    	$('.popupable').CreateBubblePopup({ 
    		position : 'right',
			align	 : 'center',
			divStyle : { margin: '0 0 0 10px' },
			innerHtml: 'Please wait while loading...',
			innerHtmlStyle: {
							color:'#FFFFFF', 
							'text-align':'left'
							},
																		
			themeName: 	'all-black',
			themePath: 	'js/jquery/jquerybubblepopup-theme'
		});
});


