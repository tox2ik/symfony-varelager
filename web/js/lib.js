// count() for objects
function count(obj) {
	var count = 0;
	if (typeof obj === 'object') {
		for (var prop in obj) {
			if (obj.hasOwnProperty(prop)) {
					++count;
			}
		}
	} else if ( typeof obj === 'array'){
		count = obj.length;
	}
	return count;
}

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}



// cookies ttp://www.w3schools.com/js/js_cookies.asp
function setCookie(c_name,value,exdays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays===null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
function getCookie(c_name) {
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++) {
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name) {
			return unescape(y);
		}
	}
	return null;
}



/**
 * quicksort 
 * http://en.literateprograms.org/Quicksort_%28JavaScript%29
 */

if (!Array.prototype.swap) {
	Array.prototype.swap = function(a, b) {
		var tmp=this[a];
		this[a]=this[b];
		this[b]=tmp;
	};
}

if (!Array.prototype.quickSort) {


	//function partition(array, begin, end, pivot) {
	Array.prototype.quicksortPartition = function( begin, end, pivot, getValue) {

		if (typeof getValue === "undefined") {
			getValue = function(idx) {
				return this[idx];
			};
		}
		//var piv=array[pivot];
		//var piv=this[pivot];
		var piv=getValue.call(this, pivot);
		this.swap(pivot, end-1);
		var store=begin;
		var ix;
		for (ix=begin; ix<end-1; ++ix) {
			if (getValue.call(this, ix)<=piv) {
				this.swap(store, ix);
				++store;
			}
		}
		this.swap(end-1, store);

		return store;
	};

	//function qsort(array, begin, end)
	Array.prototype.qsort = function(begin, end, getValue) {
		if (end-1 > begin) {
			var pivot=begin+Math.floor(Math.random()*(end-begin));

			//pivot=partition(array, begin, end, pivot);
			pivot=this.quicksortPartition(begin, end, pivot, getValue);

			//qsort(array, begin, pivot);
			//qsort(array, pivot+1, end);
			this.qsort(begin, pivot, getValue);
			this.qsort(pivot+1, end, getValue);
		}
	};
	//function quick_sort(array)

	Array.prototype.quickSort = function(getValue) {
		//qsort(array, 0, array.length);
		this.qsort(0, this.length, getValue);
	};
}





if (!Array.prototype.binarySearch) {
	Array.prototype.binarySearch = function(needle, getValue, case_insensitive ) {
	if (typeof(this) === 'undefined' || !this.length) return -1;

	if (typeof getValue === "undefined") {
		getValue = function(idx) {
			return this[idx];
		};
	}
	
	//var high = haystack.length - 1;
	var high = this.length - 1;
	var low = 0;
	case_insensitive = (typeof(case_insensitive) === 'undefined' || case_insensitive) ? true:false;
	needle = (case_insensitive) ? needle.toLowerCase():needle;
	
	while (low <= high) {
		mid = parseInt((low + high) / 2, 10);
		//element = (case_insensitive) ? haystack[mid].toLowerCase() : haystack[mid];
		element = (case_insensitive) ? getValue.call(this, mid).toLowerCase() : getValue.call(this,mid);
		if (element > needle) {
			high = mid - 1;
		} else if (element < needle) {
			low = mid + 1;
		} else {
			return mid;
		}
	}
	return -1;
	};
}

if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function (obj, fromIndex) {
		if (fromIndex == null) {
			fromIndex = 0;
		} else if (fromIndex < 0) {
			fromIndex = Math.max(0, this.length + fromIndex);
		}
		for (var i = fromIndex, j = this.length; i < j; i++) {
			if (this[i] === obj)
				return i;
		}
		return -1;
	};
}

if (!String.prototype.format) {
	String.prototype.format = function() {
		var formatted = this;
		for (var i = 0; i < arguments.length; i++) {
			var regexp = new RegExp('\\{'+i+'\\}', 'gi');
			formatted = formatted.replace(regexp, arguments[i]);
		}
		return formatted;
	};
}

if (!String.prototype.initials) {
	String.prototype.initials = function(dot, spliton){
		dot = (typeof dot  === 'undefined') ? '.' : dot;
		spliton = (typeof spliton  === 'undefined') ? ' ' : spliton;
		words = this.split(spliton);
		for (i=0,len= words.length; i< len; i++) {
			words[i] = words[i].substring(0,1);
		}
		return words.join(dot)+dot;
	};
}
/* http://blog.stevenlevithan.com/archives/faster-trim-javascript [2012 oct] */
if (!String.prototype.trim) {
	String.prototype.trim = function(){
		return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	};
}
/**
 * Function : dump()
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function dump(arr,level,maxlevel) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				if (level+1 < maxlevel) {
					dumped_text += dump(value,level+1);
				}
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}
