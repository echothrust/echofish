$(function(){
		$("form").form();
		var innerTEXT='';
		$('.tooltips').mouseover(function() {
			innerTEXT=$(this).attr('tooltip');
			$(this).SetBubblePopupInnerHtml(innerTEXT, true);
        });

		$('.tooltips').CreateBubblePopup({ 
    		position : 'right',
			align	 : 'center',
			width    : '200px',
			divStyle : { margin: '0 0 0 10px' },
			innerHtml: '...',
			innerHtmlStyle: {
							color:'#FFFFFF', 
							'text-align':'left'
							},
																		
			themeName: 	'all-black',
			themePath: 	'js/jquery/jquerybubblepopup-theme'
		});
});