/*
 * General purpose Javascript Functions
 * $Id: functions.js,v 1.1 2011/08/25 10:13:50 proditis Exp $
 */
var help_state="hidden";

function confirm_submit()
{
	if (confirm("Are you sure you want to submit this form"))
		return true;
	return false;
}

function confirm_delete()
{
	if (confirm("Are you sure you want to delete this record"))  
		return true;
	return false;
}

function modify_size(skip,elem) {
  var txtarea = document.getElementById(elem);
   txtarea.rows = txtarea.rows + (skip);
}

function toggleBox(szDivID, iState) // 1 visible, 0 hidden
{
   var obj = document.layers ? document.layers[szDivID] :
   document.getElementById ?  document.getElementById(szDivID).style :   document.all[szDivID].style;
   obj.visibility = document.layers ? (iState ? "show" : "hide") : (iState ? "visible" : "hidden");
   obj.display= iState ? "block" : "none" ;
}

function reverseState(szDivID) 
{
   var obj = document.layers ? document.layers[szDivID] : document.getElementById ?  document.getElementById(szDivID).style : document.all[szDivID].style;
   if(help_state=="hidden" || help_state=="hide") {
   		toggleBox(szDivID,1)
	   	help_state="visible";
	}
   else {
	   	toggleBox(szDivID,0);
	   	help_state="hidden";
   	}
}




function intval (mixed_var, base) {
    // Get the integer value of a variable using the optional base for the conversion  
    // 
    // version: 1004.2314
    // discuss at: http://phpjs.org/functions/intval
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: stensi
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   input by: Matteo
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)    

    var type = typeof( mixed_var );
 
    if (type === 'boolean') {        return (mixed_var) ? 1 : 0;
    } else if (type === 'string') {
        tmp = parseInt(mixed_var, base || 10);
        return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;
    } else if (type === 'number' && isFinite(mixed_var) ) {
            return Math.floor(mixed_var);
    } else {
        return 0;
    }
} 

function php_serialize(obj)
{
    var string = '';

    if (typeof(obj) == 'object') {
        if (obj instanceof Array) {
            string = 'a:';
            tmpstring = '';
            count = 0;
            for (var key in obj) {
                tmpstring += php_serialize(key);
                tmpstring += php_serialize(obj[key]);
                count++;
            }
            string += count + ':{';
            string += tmpstring;
            string += '}';
        } else if (obj instanceof Object) {
            classname = obj.toString();

            if (classname == '[object Object]') {
                classname = 'StdClass';
            }

            string = 'O:' + classname.length + ':"' + classname + '":';
            tmpstring = '';
            count = 0;
            for (var key in obj) {
                tmpstring += php_serialize(key);
                if (obj[key]) {
                    tmpstring += php_serialize(obj[key]);
                } else {
                    tmpstring += php_serialize('');
                }
                count++;
            }
            string += count + ':{' + tmpstring + '}';
        }
    } else {
        switch (typeof(obj)) {
            case 'number':
                if (obj - Math.floor(obj) != 0) {
                    string += 'd:' + obj + ';';
                } else {
                    string += 'i:' + obj + ';';
                }
                break;
            case 'string':
                string += 's:' + obj.length + ':"' + obj + '";';
                break;
            case 'boolean':
                if (obj) {
                    string += 'b:1;';
                } else {
                    string += 'b:0;';
                }
                break;
        }
    }

    return string;
}

function fix_checkboxes()
{
	element=document.actionForm;
	var acounter=[];
	// zero out our array counter;
	for(i=0;i<element.length; i++)
	{
		// element is checkboxed and array
		if(element[i].type=='checkbox' && element[i].name.substr(-4)=='[]' && acounter[element[i].name]!==0)
		{
			acounter[element[i].name]=0;
		}
	}

	// now set the index on our checkboxes
	for(i=0;i<element.length; i++)
	{
		if(element[i].type=='checkbox' && element[i].name.substr(-4)=='[]')
		{
			index=acounter[element[i].name];
			name=element[i].name;
			document.actionForm.elements[i].name=element[i].name.replace('[]','['+index+']');
			index++;
			acounter[name]=index;
		}
	}

	
	return true;
}

// Delete one item from the list
function del_one(classname)
{
  $("."+classname+"wrap>br:last").remove();
  $("."+classname+":last").remove();
  
}
// append an item to the list
function add_one(classname,kv)
{
  var obj=$("."+classname+":first").clone();//.appendTo("#"+classname+"wrap");
  obj.val('');
  obj.removeAttr("id");
  obj.appendTo("#"+classname+"wrap");
  
  $('br:first').clone().appendTo("#"+classname+"wrap");
  $("form").form();
  if(kv==true)
  {
	obj.autocomplete({
						minLength: 2,
						source: uri+'&action=ajax_get_'+classname+'s',
						focus: function( event, ui ) {
							$( this ).val( ui.item.title );
							return false;
						},
						select: function( event, ui ) {
							$( this ).val( ui.item.title );
							return false;
						}
					})
					.data( "autocomplete" )._renderItem = function( ul, item ) {
						return $( "<li></li>" ).data( "item.autocomplete", item ).append( "<a>" + item.title + "</a>" ).appendTo( ul );
					};
  }
}

function getval(obj)
{
	if(obj==false || obj=='undefined' || obj==null)
		return '';
	else return obj;
		
}

function unserialize(data){
    // Takes a string representation of variable and recreates it  
    // 
    // version: 810.114
    // discuss at: http://phpjs.org/functions/unserialize
    // +     original by: Arpad Ray (mailto:arpad@php.net)
    // +     improved by: Pedro Tainha (http://www.pedrotainha.com)
    // +     bugfixed by: dptr1988
    // +      revised by: d3x
    // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // %            note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %            note: Aiming for PHP-compatibility, we have to translate objects to arrays 
    // *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
    // *       returns 1: ['Kevin', 'van', 'Zonneveld']
    // *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');
    // *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
    
    var error = function (type, msg, filename, line){throw new window[type](msg, filename, line);};
    var read_until = function (data, offset, stopchr){
        var buf = [];
        var chr = data.slice(offset, offset + 1);
        var i = 2;
        while(chr != stopchr){
            if((i+offset) > data.length){
                error('Error', 'Invalid');
            }
            buf.push(chr);
            chr = data.slice(offset + (i - 1),offset + i);
            i += 1;
        }
        return [buf.length, buf.join('')];
    };
    var read_chrs = function (data, offset, length){
        buf = [];
        for(var i = 0;i < length;i++){
            var chr = data.slice(offset + (i - 1),offset + i);
            buf.push(chr);
        }
        return [buf.length, buf.join('')];
    };
    var _unserialize = function (data, offset){
        if(!offset) offset = 0;
        var buf = [];
        var dtype = (data.slice(offset, offset + 1)).toLowerCase();
        
        var dataoffset = offset + 2;
        var typeconvert = new Function('x', 'return x');
        var chrs = 0;
        var datalength = 0;
        
        switch(dtype){
            case "i":
                typeconvert = new Function('x', 'return parseInt(x)');
                var readData = read_until(data, dataoffset, ';');
                var chrs = readData[0];
                var readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case "b":
                typeconvert = new Function('x', 'return (parseInt(x) == 1)');
                var readData = read_until(data, dataoffset, ';');
                var chrs = readData[0];
                var readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case "d":
                typeconvert = new Function('x', 'return parseFloat(x)');
                var readData = read_until(data, dataoffset, ';');
                var chrs = readData[0];
                var readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case "n":
                readdata = null;
            break;
            case "s":
                var ccount = read_until(data, dataoffset, ':');
                var chrs = ccount[0];
                var stringlength = ccount[1];
                dataoffset += chrs + 2;
                
                var readData = read_chrs(data, dataoffset+1, parseInt(stringlength));
                var chrs = readData[0];
                var readdata = readData[1];
                dataoffset += chrs + 2;
                if(chrs != parseInt(stringlength) && chrs != readdata.length){
                    error('SyntaxError', 'String length mismatch');
                }
            break;
            case "a":
                var readdata = {};
                
                var keyandchrs = read_until(data, dataoffset, ':');
                var chrs = keyandchrs[0];
                var keys = keyandchrs[1];
                dataoffset += chrs + 2;
                
                for(var i = 0;i < parseInt(keys);i++){
                    var kprops = _unserialize(data, dataoffset);
                    var kchrs = kprops[1];
                    var key = kprops[2];
                    dataoffset += kchrs;
                    
                    var vprops = _unserialize(data, dataoffset);
                    var vchrs = vprops[1];
                    var value = vprops[2];
                    dataoffset += vchrs;
                    
                    readdata[key] = value;
                }
                
                dataoffset += 1;
            break;
            default:
                error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
            break;
        }
        return [dtype, dataoffset - offset, typeconvert(readdata)];
    };
    return _unserialize(data, 0)[2];
}

function getSelected() {
  if(window.getSelection) { return window.getSelection(); }
  else if(document.getSelection) { return document.getSelection(); }
  else {
    var selection = document.selection && document.selection.createRange();
    if(selection.text) { return selection.text; }
    return false;
  }
  return false;
}
