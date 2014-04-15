/*
 * Copyright (c) 2012 Planet Telex Inc. all rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *         http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * @fileOverview Contains the Linkify plugin code.
 * @author <a href="mailto:dan@planettelex.net">Dan Hansen</a>
 */

/**
 * See (<a href="http://jquery.com">http://jquery.com</a>).
 * @name $
 * @class 
 * <p>See the jQuery Library API (<a href="http://api.jquery.com/jQuery/">http://api.jquery.com/jQuery/</a>)
 * for full details.  This documentation only covers the functions and classes that are added to
 * jQuery by the Linkify plugin.</p>
 */

/**
 * See (<a href="http://jquery.com">http://jquery.com</a>).
 * @name $.fn
 * @class 
 * <p>The Linkify plugin interface. See jQuery (<a href="http://docs.jquery.com/Plugins/Authoring">http://docs.jquery.com/Plugins/Authoring</a>)
 * for the full details on authoring plugins.  This documentation only covers the functions and classes that are added to
 * jQuery by the Linkify plugin.</p>
 * 
 * @memberOf $
 */

(function($) {
	
	var IP2 = /(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/g;
	var IP1 = /((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))/g;
	var MAC1 = /([0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0+-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f])/g;

    /**
	 * <p>
	 * Converts any existing URLs in the selected text to clickable links.
	 * </p>
	 * 
	 * <b>Example usage:</b>
	 * 
	 * @example $("#Linkify").linkify();
	 * @example jQuery("#Linkify").linkify();
	 * @example $("#Linkify").linkify({target:"_blank", className:"link"});
	 * 
	 * @param {object} [settings] The configuration for Linkify. Defaults as follows <p>className: "linkified", target: "_self", nofollow: "true"</p>
	 */
	$.fn.linkify = function(settings) {
		settings = $.extend({
			className: "linkified",
			target: "_self",
			nofollow: false
		}, settings);
		
		var nofollow = settings.nofollow ? ' rel="nofollow"' : "";
		
		linkifyThis = function() {
			var childNodes = this.childNodes, i = childNodes.length;
			while (i--) {
				var n = childNodes[i];
				if (n.nodeType == 3) {
					var html = $.trim(n.nodeValue);
					if (html) {
						var replaceUrl = String.format(
								'<a href="$1" target="{0}" class="{1}"{2}>$1</a>',
								settings.target,
								settings.className,
								nofollow
						);
								
						html = html.replace(IP1, replaceUrl)
								   .replace(MAC1,replaceUrl);
						$(n).after(html).remove();
					}
				}
				else if (n.nodeType == 1 && !/^(a|button|textarea)$/i.test(n.tagName)) {
					linkifyThis.call(n);
				}
			}
		};
		
		String.format = function(text) {
		    // check if there are two arguments in the arguments list
		    if (arguments.length <= 1) {
		        // if there are not 2 or more arguments there’s nothing to replace
		        // just return the original text
		        return text;
		    }

		    // decrement to move to the second argument in the array
		    var tokenCount = arguments.length - 2;

		    for (var token = 0; token <= tokenCount; token++) {
		        // iterate through the tokens and replace their placeholders from
				// the original text in order
		        text = text.replace(new RegExp("\\{" + token + "\\}", "gi"), arguments[ token + 1 ]);
		    }
		   
		    return text;
		};
		
		return this.each(linkifyThis);
	};

})(jQuery);