/**
 * password_strength_plugin.js
 * Copyright (c) 20010 myPocket technologies (www.mypocket-technologies.com)
 * @author Darren Mason (djmason9@gmail.com)
 * @date 3/13/2009
 * @projectDescription Password Strength Meter is a jQuery plug-in provide you smart algorithm to detect a password strength. Based on Firas Kassem orginal plugin - http://phiras.wordpress.com/2007/04/08/password-strength-meter-a-jquery-plugin/
 * @version 1.0.1
 *
 * @requires jquery.js (tested with 1.3.2)
 * @param shortPass:    "shortPass",    //optional
 * @param badPass:              "badPass",              //optional
 * @param goodPass:             "goodPass",             //optional
 * @param strongPass:   "strongPass",   //optional
 * @param baseStyle:    "testresult",   //optional
 * @param userid:               "",                             //required override
 * @param messageloc:   1                               //before == 0 or after == 1
 *
*/

(function($){
	$.fn.shortPass = 'Too short';
	$.fn.badPass = 'Weak';
	$.fn.goodPass = 'Good';
	$.fn.strongPass = 'Strong';
	$.fn.samePassword = 'Username and Password identical.';
	$.fn.resultStyle = "";

	$.fn.passStrength = function(options) {  
         
    var defaults = {
            shortPass:              "shortPass",    //optional
            badPass:                "badPass",      //optional
            goodPass:               "goodPass",     //optional
            strongPass:             "strongPass",   //optional
            baseStyle:              "testresult",   //optional
            userid:                 "",   //required override
            messageloc:             1     //before == 0 or after == 1
           };
                        var opts = $.extend(defaults, options);  
                     
                        return this.each(function() {
                                 var obj = $(this);
                               
                                $(obj).unbind().keyup(function()
                                {
                                       
                                        var results = $.fn.teststrength($(this).val(),$(opts.userid).val(),opts);
                                       
                                        if(opts.messageloc === 1)
                                        {
                                                $(this).next("." + opts.baseStyle).remove();
                                                $(this).after("<span class=\""+opts.baseStyle+"\"><span></span></span>");
                                                $(this).next("." + opts.baseStyle).addClass($(this).resultStyle).find("span").text(results);
                                        }
                                        else
                                        {
                                                $(this).prev("." + opts.baseStyle).remove();
                                                $(this).before("<span class=\""+opts.baseStyle+"\"><span></span></span>");
                                                $(this).prev("." + opts.baseStyle).addClass($(this).resultStyle).find("span").text(results);
                                        }
                                 });
                                 
                                //FUNCTIONS
                                $.fn.teststrength = function(password,username,option){
                                                var score = 0;
                                           
                                            //password < 5
                                            if (password.length < 5 ) { this.resultStyle =  option.shortPass;return $(this).shortPass; }
                                           
                                            //password == user name
                                            if (password.toLowerCase()==username.toLowerCase()){this.resultStyle = option.badPass;return $(this).samePassword;}
                                           
                                            //password length
                                            score += password.length * 4;
                                            score += ( $.fn.checkRepetition(1,password).length - password.length ) * 1;
                                            score += ( $.fn.checkRepetition(2,password).length - password.length ) * 1;
                                            score += ( $.fn.checkRepetition(3,password).length - password.length ) * 1;
                                            score += ( $.fn.checkRepetition(4,password).length - password.length ) * 1;
                       
                                            //password has 3 numbers
                                            if (password.match(/(.*[0-9].*[0-9].*[0-9])/)){ score += 5;}
                                           
                                            //password has 2 symbols
                                            if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)){ score += 5 ;}
                                           
                                            //password has Upper and Lower chars
                                            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){  score += 10;}
                                           
                                            //password has number and chars
                                            if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)){  score += 15;}
                                            //
                                            //password has number and symbol
                                            if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/)){  score += 15;}
                                           
                                            //password has char and symbol
                                            if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/)){score += 15;}
                                           
                                            //password is just a numbers or chars
                                            if (password.match(/^\w+$/) || password.match(/^\d+$/) ){ score -= 10;}
                                           
                                            //verifying 0 < score < 100
                                            $("#score").val(score);
                                            
                                            if ( score < 0 ){score = 0;}
                                            if ( score > 100 ){  score = 100;}
                                           
                                            if (score < 34 ){ this.resultStyle = option.badPass; return $(this).badPass;}
                                            if (score < 68 ){ this.resultStyle = option.goodPass;return $(this).goodPass;}
                                           
                                           this.resultStyle= option.strongPass;
                                            return $(this).strongPass;
                                           
                                };
                 
                  });  
         };  
})(jQuery);

jQuery.fn.checkRepetition = function(pLen,str) {
	 var res = "";
	 for (var i=0; i<str.length ; i++ ){
	   var repeated=true;
	    
	 for (var j=0;j < pLen && (j+i+pLen) < str.length;j++){
	  repeated=repeated && (str.charAt(j+i)==str.charAt(j+i+pLen));
	 }

	 if (j<pLen){repeated=false;}
	    if (repeated) {
	     i+=pLen-1;
	      repeated=false;
	     }
	     else {res+=str.charAt(i);}
	     }
	 return res;
	 };

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

jQuery(document).ready(function(){
	
	jQuery.tablesorter.addWidget({
	// give the widget a id
	id: "sortPersist",
	// format is called when the on init and when a sorting has finished
		format: function(table) {
		var COOKIE_NAME = 'spinledenperpersoon';
		var cookie = jQuery.cookie(COOKIE_NAME);
		var options = {path: '/'};
		var data = [];
		var sortList = table.config.sortList;
		var id = jQuery(table).attr('id');

			// If the existing sortList isn't empty, set it into the cookie and get out
			if (sortList.length > 0) {
				if (typeof(cookie) == "undefined" || cookie == null) {
					data = {id: sortList};
				} else {
					data = jQuery.evalJSON(cookie);
					data[id] = sortList;
				}
				jQuery.cookie(COOKIE_NAME, jQuery.toJSON(data), options);
				//$.cookie(COOKIE_NAME, JSON.stringify(data), options);
				
			}
			// Otherwise...
			else {
				if (typeof(cookie) != "undefined" && cookie != null) {
					// Get the cookie data
					var data = jQuery.evalJSON(jQuery.cookie(COOKIE_NAME));
					// If it exists
						if (typeof(data[id]) != "undefined" && data[id] != null) {
							// Get the list
							sortList = data[id];
							// And finally, if the list is NOT empty, trigger the sort with the new list
								if (sortList.length > 0) {
									//table.config.sortList = sortList;
									jQuery(table).trigger("sorton", [sortList]);
								}
						}
				}
			}
	}
});
		
jQuery("#persons").tablesorter({
	dateFormat: 'uk' ,
	widgets: ['zebra','sortPersist'],
	textExtraction: myTextExtraction
});
	
jQuery("#addresses").tablesorter({
	dateFormat: 'uk' ,
	widgets: ['zebra','sortPersist'],
	textExtraction: myTextExtraction
});

jQuery(".password1").passStrength({
	userid:	"#sn_username"
});

/*
$("#user_id").keyup(function() {
    directory.val( this.value );
});
*/

jQuery("#gem").autocomplete({
	source: function( request, response ) {
	cc = escape(document.getElementById("countrycode").value);
		url = "https://my.scoutnet.be/service/postcode.php?str=" + escape(request.term) + "&cc=" + cc;
        
		jQuery.getJSON(url + '&callback=?', function(data) {
            response(data);
        });
    }
});



}
);


var myTextExtraction = function(node){  
    //return node.childNodes[0].childNodes[0].innerHTML;
	return node.childNodes[0].innerHTML;
}

if (!Array.prototype.forEach){
 Array.prototype.forEach = function(fun /*, thisp*/){
 var len = this.length;
 if (typeof fun != "function")
 throw new TypeError();
 var thisp = arguments[1];
 for (var i = 0; i < len; i++){
  if (i in this)
    fun.call(thisp, this[i], i, this);
  }
 };
}

function setCheck(chk,id){
	f=document.getElementById(id);
	if (chk.checked){f.disabled=false;}else{f.disabled=true;}
}


function exp(tel) {
	el = document.getElementById('item'+tel)
	if (el.style.display=='none'){
	el.style.display='';
	document.getElementById('state'+tel).src=templateDir + 'img/minus.gif';
	}
	else{
	el.style.display='none';
	document.getElementById('state'+tel).src=templateDir + 'img/plus.gif';
	}
	}

function toonNLcode(code){
	if (code=="NL"){
		document.getElementById('nlcode').style.visibility='visible';
		document.getElementById('nlcode').style.display='inline';
		}else{
		document.getElementById('nlcode').style.visibility='hidden';
		document.getElementById('nlcode').style.display='none';
	}
}

function dele(id){
	agree=confirm('Ok to delete person ' + id + ' ?' + '\n');
	if(agree){eval("document.frmlidwijzigen.del.value='y'");eval("document.frmlidwijzigen.submit()");return true;}
	else{return false;}
}

function getRandomArbitary (min, max) {
	return Math.random() * (max - min) + min;
}

function generate_password(){
	var password_characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	pwdLen=getRandomArbitary (15, 20);
	var password='';
	var len=0;
	for(var i=0;i<pwdLen;i++){
	password+=password_characters.charAt(Math.floor(Math.random()*password_characters.length))
	}
	document.getElementById("sn_password").value=password;
}

function generateSalutation(locationid,sid){
// disabled for wordpress plugin
}
