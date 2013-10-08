// JavaScript Document 


$.widget("ui.form",{
		 _init:function(){
			 var object = this;
			 var form = this.element;
			 var inputs = form.find("input:text, input:password , select, textarea");
			 
			  form.find("fieldset").addClass("ui-widget-content");
			  form.find("legend").addClass("ui-widget-header ui-corner-all");
			  form.addClass("ui-widget");
			
			  $.each(inputs,function(){
				$(this).addClass('ui-state-default ui-corner-all');
				//$(this).wrap("<label />");
				
				if($(this).is("legend"))
				object.labels(this);
				else if($(this).is(":checkbox"))
				object.checkboxes(this);
				else if($(this).is("input[type='text']")||$(this).is("textarea")||$(this).is("input[type='password']"))
				object.textelements(this);
				else if($(this).is(":radio"))
				object.radio(this);
				
				// Add date picker on date classes.
				if($(this).hasClass("date"))
				{
					$(this).datepicker({ dateFormat: 'dd-mm-yy'});
				}
				
				
				});
			 $(".hover").hover(function(){
						  $(this).addClass("ui-state-hover"); 
						   },function(){ 
						  $(this).removeClass("ui-state-hover");  
						   });
			 
			 },
		 textelements:function(element){
			
			$(element).bind({
  			
 			  focusin: function() {
 			   $(this).toggleClass('ui-state-focus');
 				 },
			   focusout: function() {
 			    $(this).toggleClass('ui-state-focus');
 				 }	 
			  });
			 
			 },
		 labels:function(element){
		 	$(element).addClass("ui-state-default ui-corner-all");
		 },
		 
		 checkboxes:function(element){
		  $(element).parent("label").after("<span />");
		  var parent =  $(element).parent("label").next();
			 $(element).addClass("ui-helper-hidden");
				parent.css({width:16,height:16,display:"block"});
				
				 parent.wrap("<span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>");
			 
			 parent.parent().addClass('hover');
		  
			 if($(element).is(':checked'))
			 {
						 $(this).toggleClass("ui-state-active");
						 parent.toggleClass("ui-icon ui-icon-check");
						$(element).click();
			 }
			 parent.parent("span").click(function(event){
						 $(this).toggleClass("ui-state-active");
						 parent.toggleClass("ui-icon ui-icon-check");
						$(element).click();
					
						});
			 },
		 radio:function(element){
			   $(element).parent("label").after("<span />");
		  var parent =  $(element).parent("label").next();
			 $(element).addClass("ui-helper-hidden");
			 parent.addClass("ui-icon ui-icon-radio-off");
				 parent.wrap("<span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>");
			 
			 parent.parent().addClass('hover');
		  
			 
			 parent.parent("span").click(function(event){
						 $(this).toggleClass("ui-state-active");
						 parent.toggleClass("ui-icon-radio-off ui-icon-bullet");
						$(element).click();
						
						});
			 },
	 
		 
		 });

