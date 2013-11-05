/*
 * menuBox plugin pour jQuery développé par Mandchou
 * http://www.mandchou.com/
 *
 * Copyright (c) 2009 Charly BELLE
 * Dual licensed under the MIT and GPL licenses.
 * http://docs.jquery.com/License
 *
 * Date: 2010-01-13 13:45:21 -0500 (Wed, 13 Jan 2010)
 * Revision: 1
 * --------
 * Date: 2012-02-29 02:37:22 +0100 (Wed, 29 Feb 2012)
 * modified options to take a callback for <a class=liLeaf> 
 *
 */

(function($) {  
$.fn.menuBox = function (options){ // réglages par défaut
	options = jQuery.extend({
		speedIn:200, 
		speedOut:100, 
		menuWi:200,
		align:'vertical',
		//align:'horizontal',
		leafClick: function(){ return false;}
},options);	
$.fn.findPos = function() {
	obj = jQuery(this).get(0);
	var curleft = obj.offsetLeft || 0;
	var curtop = obj.offsetTop || 0;
	while (obj) {
	    curleft += obj.offsetLeft;
	    curtop += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return {x:curleft,y:curtop};
};
this.each(function(){
	var _self = $(this);
	//var globalWi = parseInt($('html').width());
	_self.find('ul').css({width:options.menuWi+'px',position:'absolute'});
	_self.find('ul').addClass('ulFirstChild');
	_self.find('ul').find('ul').css({marginLeft:options.menuWi+'px'});
	_self.find('ul').find('ul').removeClass('ulFirstChild');
	
	var firstAlign;
	var debugAlign;
	if (options.align=='vertical'){
		firstAlign = parseInt(_self.width(),10);
		debugAlign = 10;
	} else {
		firstAlign = 0;
		debugAlign = 5;
	}
	
	$(this).find('.ulFirstChild').css({marginLeft:firstAlign+'px'});
	
	$(this).find('ul').hide();
	$(this).find('a[class~="liLeaf"]').bind('click', options.leafClick);
	$(this).find('li').bind('mouseenter',function(){
		var curObj = $(this).find('ul:first');
		var globalWi = parseInt($('html').width(),10);
		var pos = $(this).findPos();
		var curMargin;
		if ((globalWi - pos.x)-options.menuWi < options.menuWi){
			curMargin = parseInt(curObj.css('marginLeft'),10);
			if (curMargin !==0){
			curObj.css({marginLeft:'-'+options.menuWi+'px'});	
			}
			
			var diffMargin = (globalWi - pos.x)-options.menuWi;
			
			if ((globalWi - pos.x) < options.menuWi){
			$(this).find('.ulFirstChild').css({marginLeft:diffMargin-firstAlign-debugAlign+'px'});
			}	
		}
		else
		{
			curMargin = parseInt(curObj.css('marginLeft'),10);
			if (curMargin !==0){
			curObj.css({marginLeft:options.menuWi+'px'});
			$(this).find('.ulFirstChild').css({marginLeft:firstAlign+'px'});
			}
		}
		curObj.stop();
		curObj.css({opacity:1});
		curObj.fadeIn(options.speedIn);
	});
	$(this).find('li').bind('mouseleave',function(){
		var curObj = $(this).find('ul:first');
		curObj.stop();
		curObj.fadeOut(options.speedOut);
	});
});
};})(jQuery);
