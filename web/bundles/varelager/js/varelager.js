//
// varelager.js: common methods and variables for bundle PersilerietVarelager
//

// POST 
function submitPostForm(url, data) {
	var form 				= document.createElement("form");
		form.action			= url;
		form.method			= 'POST';
		form.style.display	= 'none';
	for (var attr in data) {
		var param 		= document.createElement("input");
			param.name 	= attr;
			param.value	= data[attr];
			param.type 	= 'hidden';
		form.appendChild(param);
	}

	document.body.appendChild(form);
	form.submit();
}

// IE doesnt have console.
if (!window.console) {
	window.console = { log: function() {}};
}

// just to keep things in a namespace
jQuery.varelager = function() {};
jQuery.varelager.links = function(){};

// Display confirmation messages
jQuery.pnotify.defaults.styling = "jqueryui";
jQuery.pnotify.defaults.delay = 3000;
var stack_bar_bottom = {'dir1': 'up', 'dir2': 'right', 'spacing1': 0, 'spacing2': 0};
jQuery.varelager.notifyOk = function(message) {
	var opts = {
		icon: 'ui-icon ui-icon-check',
		type: 'info',
		onblock_opacity: 0.2,
		nonblock: true,
		opacity: 0.9,
 		animation: 'none',
		/*hide: false,*/
		width: '400px',
		//stack: { 'dir1': 'up', 'dir2': 'right', 'spacing1': 2, 'spacing2': 2 },
		stack: stack_bar_bottom,
		addclass: "stack-bar-bottom",
		title: false,
		text: message
	};
//var stack_bar_bottom = {"dir1": "up", "dir2": "right", "spacing1": 0, "spacing2": 0};

	jQuery.pnotify(opts);
};

jQuery.varelager.notifyFailure = function(message) {
	var opts = {
		icon: 'ui-icon ui-icon-alert',
		type: 'info',
		onblock_opacity: 0.9,
		nonblock: false,
		opacity: 0.9,
 		animation: 'none',
		/*hide: false,*/
		width: '400px',
		//stack: { 'dir1': 'up', 'dir2': 'right', 'spacing1': 2, 'spacing2': 2 },
		stack: stack_bar_bottom,
		addclass: "stack-bar-bottom",
		title: false,
		text: message
	};
	jQuery.pnotify(opts);
};

jQuery.varelager.notifyNormal = function(message) {
	var opts = {
		//icon: 'ui-icon ui-icon-alert',
		icon: false,
		type: 'info',
		onblock_opacity: 0.9,
		nonblock: false,
		opacity: 0.9,
 		animation: 'none',
		width: '400px',
		stack: stack_bar_bottom,
		addclass: "stack-bar-bottom",
		title: false,
		text: message
	};
	jQuery.pnotify(opts);
};

jQuery.varelager.carrotPopup = function( title, message) {
	jQuery('<div>'+message+'</div>').
		dialog({ 
			title: title,
			closeOnEscape: true,
			dialogClass: "alert",
			hide: { effect: 'drop', direction: 'down' },
			show: { effect: 'drop', direction: "up" },
			maxWidth: 600,
			minWidth: 450,
			modal: true, // disables everyting else
			buttons: { "Ok": function() { jQuery(this).dialog("close"); }},
			closeText: 'lukk'
	});
};

// Display errors on bad requests or bad responses
jQuery.varelager.errorToMessage = function(jqXHR, textStatus, errorThrown ){
	var msg="";
	if (jqXHR.status == 412) {
		msg += "Forespørselen ga tilbake HTTP status: 412 (Precondition failed)<br />";
		msg += "Feil eller mangelfulle data ble sendt inn!<br />";
		msg += "feilmelding:<br />" +jqXHR.responseText;
	} else if ( jqXHR.status == 403) {
		msg += "Forespørselen ga tilbake: 403 (forbudt).<br />";
		msg += jqXHR.responseText;
	} else {
		msg += "Forespørselen ga tilbake HTTP status:<br />{0} ({1})<br />feilmelding:<br />{2}" .
		format(jqXHR.status, errorThrown, jqXHR.responseText);
	}
	return msg;
};
jQuery.varelager.ajaxErrorPnotify = function(jqXHR, textStatus, errorThrown ){
	console.log('ajaxErrorPnotify');
	var msg = jQuery.varelager.errorToMessage(jqXHR, textStatus, errorThrown);
	console.log(msg);
	jQuery.varelager.notifyFailure(msg);
};
jQuery.varelager.ajaxError = function(jqXHR, textStatus, errorThrown ){
	var msg = jQuery.varelager.errorToMessage(jqXHR, textStatus, errorThrown);
	//alert.call(this.window, msg);
	jQuery.varelager.carrotPopup('Noe gikk galt!', msg);
	console.log(jqXHR);
};

// adresses where we send stuff and where we fetch stuff
var webroot  ='/symfony/web';
jQuery.varelager.links.webroot	= webroot;

//var imgbase = '/symfony/web/bundles/persilleriet/images/';
jQuery.varelager.links.buttons	= webroot +'/bundles/persilleriet/images/buttons/';
jQuery.varelager.links.roles	= webroot +'/db/get-json/roles-unique';
jQuery.varelager.links.categories	= webroot +'/db/get-json/prodcat-unique';
jQuery.varelager.links.placedates	= webroot +'/db/get-json/placedates';
jQuery.varelager.links.products		= webroot +'/db/get-json/products';
jQuery.varelager.links.stockrecord	= webroot +'/db/get-json/stockrecord';
jQuery.varelager.links.adminUsers	= 		webroot +'/db/get-json/adminusers';
jQuery.varelager.links.adminPermissions	= 	webroot +'/db/get-json/adminpermissions';
jQuery.varelager.links.adminProducts= 		webroot +'/db/get-json/adminproducts';
jQuery.varelager.links.adminDepartments= 	webroot +'/db/get-json/adminDepartments';
jQuery.varelager.links.adminSuppliers=		webroot +'/db/get-json/adminsuppliers';
jQuery.varelager.links.adminOrders  =		webroot +'/db/get-json/adminOrders';
jQuery.varelager.links.formSuppcat  =		webroot +'/db/get-json/formSupplierCategories';
jQuery.varelager.dataCatSupp = [];

jQuery.varelager.showAjaxLoader = function(){ jQuery('img#pacman').removeClass('hidden'); };
jQuery.varelager.hideAjaxLoader = function(){ jQuery('img#pacman').addClass('hidden'); };


/*
jQuery.varelager.placeButtons = function() {

	var listWidth = (1 +jQuery("#listWidget").outerWidth()) + "px";
	var listHeight = (-1 - jQuery("#listWidget").outerHeight()) + "px";

	jQuery('button#addItem')
		.css('position','relative')
		.css('left', listWidth )
		.css('top', listHeight );
	jQuery('button#saveList')
		.css('position', 'relative')
		.css('left', -2 -jQuery('button#addItem').outerWidth() + "px");
};
*/

// move to widget-bestilling
jQuery.varelager.resizeList = function() {

		var itemsHeight = 0;

		jQuery('div#listWidget')
			.find('p#emptyMessage:visible')
			.each( function(i, element) {
				var e = jQuery(element);
				itemsHeight = itemsHeight + e.outerHeight(true);
			});
		jQuery('div#listWidget')
			.find('div[class~="list-item"]')
			.each( function(i, element) {
				var e = jQuery(element);
				itemsHeight = itemsHeight + e.outerHeight();
			});
		jQuery('div#listWidget').css('height', itemsHeight + "px");
		/*
		*/

		itemsHeight = 0;
		jQuery('div#categoryWidget')
			.find('div')
			.each( function(i, element) {
				var e = jQuery(element);
				itemsHeight = itemsHeight + e.outerHeight();
			});
		jQuery('div#categoryWidget').css('height', itemsHeight + "px");

		itemsHeight = 0;
		jQuery('div#subsetWidget')
			.find('div:visible')
			.each(function(i,element){
				var e = jQuery(element);
				itemsHeight = itemsHeight + e.outerHeight();
				
			});
		jQuery('div#subsetWidget').css('height', itemsHeight + "px");

};

jQuery.varelager.doAction = function(action, params, successCallBack) {

 	action = (typeof action !== 'undefined') ? 
		action : jQuery('#varelagerAction option:selected').val();
	var postData	= {};
	var products 	= [];


	if (action == 'saveStock') {

		postData = {
			action:		action,
			products:	jQuery('#listWidget').selectionlist('getSelection'),
			employee:	1, // FIX
			department:	jQuery('#varelagerDepartment option:selected').val()
		};

		jQuery.ajax({
			url:		jQuery('#submitURLsavestock').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				jQuery('#listWidget').selectionlist('clear');
				//window.alert("Listen ble lagret");
				jQuery.varelager.notifyOk(responseText);

			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});

	} else 
	if (action == 'saveFilter') {

		postData = {
			action:		action,
			employee:	-1 // FIX
		};
		var dialogDiv = jQuery('<div></div>').html(
			'<form id="saveFilter">'+
			'<label for="name">Tittel </label><br><input value="" type="text" name="name" /><br>'+
			'<label for="comment">Kommentar </label><br><textarea value="" name="comment" /></textarea>'+
			'</form>'+
		'');

		dialogDiv.dialog({ 
			//'minWidth': 650,
			//'maxWidth': 800,
			//'minHeight': 150,
			//'maxHeight': 350,
			//'resizable': false,
			'title': 'Lagre mal for varetelling',
			'buttons': {
				'Lagre': function(){
					var This = jQuery(this);
					postData['name']= jQuery("form#saveFilter > input[name='name']").val();
					postData['comment']= jQuery("form#saveFilter > textarea[name='comment']").val();
					postData['products']=	jQuery('#listWidget').selectionlist('getSelection');
					postData['department']=	jQuery('#varelagerDepartment option:selected').val();
					console.log('name ' + postData['name']);
					console.log('comment ' + postData['comment']);

					if ( postData['name'] === '') {
						jQuery.varelager.notifyFailure('Skriv på et navn!');
					} else {
						jQuery.ajax({
							url:		jQuery('#submitURLsavefilter').val(),
							data:		postData,
							success:	function(responseText, textStatus, jqXHR ){
								jQuery('#listWidget').selectionlist('clear');
								//window.alert("Filteret ble lagret");
								jQuery.varelager.notifyOk(responseText);
								dialogDiv.remove();
								This.dialog('close');
							},
							error:		jQuery.varelager.ajaxErrorPnotify,
							dataType:	'text',
							type:		'POST'
						});
					}
				},
				'Avbryt': function(){
					jQuery(this).dialog('close');
				}
			}
		});


	} else 
	if (action == 'fetchFilter') {
		var drawDialog = function() { };

		jQuery.ajax({
			url:		jQuery('#submitURLfetchfilter').val(),
			data:		{ 'department': jQuery('#varelagerDepartment option :selected').val() },
			success:	function(responseText, textStatus, jqXHR ){
				var table =  jQuery(responseText),
					dialogDiv = jQuery('<div></div>'),
					filterJson;

				table.addClass('filters-list');
				dialogDiv.addClass('filters-list');

				table.find('tr').each(function(){
					jQuery(this).bind('mouseenter', function() { jQuery(this).addClass('ui-selecting'); });
					jQuery(this).bind('mouseleave', function() { jQuery(this).removeClass('ui-selecting'); });

					jQuery(this).bind('click', function() { 
						filterJson = jQuery('#submitURLfetchfilter').val().replace('filters/table','filter') +'/'+
							jQuery(this).attr('id').replace('filter','');
						jQuery.ajax({ 
							url: filterJson, 
							success:	function(responseText, textStatus, jqXHR){
								filterJson = JSON.parse( responseText );
								jQuery('div#listWidget').selectionlist('additemsFromFilter', filterJson);
							},
							dataType:	'text',
							type:		'GET'

						});
						dialogDiv.dialog('close');
					});
				});

				dialogDiv.html(table).dialog({ 
					'minWidth': 650,
					'maxWidth': 800,
					'minHeight': 150,
					'maxHeight': 350,
					'resizable': false,
					'title': 'Tilgjengelige filter'
					}
				);
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});
	} else 
	if (action == 'createOrder') {

		products = jQuery('#spreadSheetWidget').spreadsheet('getProducts'); // from historikk

		if (products.length === 0) {
			products = jQuery('#listWidget').selectionlist('getSelectionForOrder'); // from beholdning
		}


		postData = {
			'action':	action,
			'products':	products
		};

		
		if (products.length > 0) {

			submitPostForm(jQuery('#submitURLcreateorder').val(), postData);
		
		} else {
			window.alert("Ingen varer valgt");
		}

	} else 
	if (action == 'saveOrder') {

		products = jQuery('#spreadSheetWidget').spreadsheet('getProducts'); // from bestilling

		postData = {
			'action':	action,
			'products':	products
		};

		jQuery.ajax({
			url:		jQuery('#submitURLsaveorder').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				jQuery('#spreadSheetWidget').spreadsheet('clear');
				jQuery.varelager.notifyOk(responseText);
				//window.alert("Bestillingen ble lagret");

			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});
	} else 
	if (action == "saveUsers") {

		postData = {
			'action':	action,
			'params':	params
		};

		jQuery.ajax({
			url:		jQuery('#submitURLsaveuser').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				successCallBack.call( responseText, textStatus, jqXHR);
				jQuery.varelager.notifyOk(responseText);
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});
	} else 
	if (action == "deleteUsers") {

		postData = {
			'action':	action,
			'params':	params
		};

		jQuery.ajax({
			url:		jQuery('#submitURLdeleteuser').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				successCallBack.call( responseText, textStatus, jqXHR);
				jQuery.varelager.notifyOk(responseText);
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});
	} else 
	if (action == "savePermissions") {

		postData = {
			'action':	action,
			'params':	params
		};

		jQuery.ajax({
			url:		jQuery('#submitURLsavepermissions').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				jQuery.varelager.notifyOk(responseText);
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});
	} else 
	if (action == "deletePermissions") {
		jQuery.varelager.carrotPopup('Kan ikke slette brukerroller',
		'velg `Brukere\' i gulrot-menyen &oslash;verst for å slette eller legge til nye ansatte');
		
	} else 
	if (action == "saveProducts") {

		postData = {
			'action':	action,
			'params':	params
		};

		jQuery.ajax({
			url:		jQuery('#submitURLsaveproduct').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				successCallBack.call( responseText, textStatus, jqXHR);
				jQuery.varelager.notifyOk(responseText);
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});

	} else 
	if (action == "deleteProducts") {

		postData = {
			'action':	action,
			'params':	params
		};

		jQuery.ajax({
			async:		false,
			url:		jQuery('#submitURLdeleteproduct').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				successCallBack.call( responseText, textStatus, jqXHR);
				jQuery.varelager.notifyOk(responseText);
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});
	} else 
	if (action == "saveSuppliers") {

		postData = {
			'action':	action,
			'params':	params
		};

		jQuery.ajax({
			async:		false,
			url:		jQuery('#submitURLsavesupplier').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				successCallBack.call( responseText, textStatus, jqXHR);
				jQuery.varelager.notifyOk( responseText );
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});

	} else 
	if (action == "deleteSuppliers") {

		postData = {
			'action':	action,
			'params':	params
		};

		jQuery.ajax({
			async:		false,
			url:		jQuery('#submitURLdeletesupplier').val(),
			data:		postData,
			success:	function(responseText, textStatus, jqXHR ){
				successCallBack.call( responseText, textStatus, jqXHR);
				jQuery.varelager.notifyOk(responseText);
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'text',
			type:		'POST'
		});


	} else {
		//window.alert("The carrot does not know what to do with:\n" + action);
		jQuery.varelager.carrotPopup('<b>Ugyldig kommando</b>', 
		'<img alt="gulrot" src="'+jQuery.varelager.links.webroot+ 
		'/bundles/persilleriet/images/carrot.png"/>'+
		'skjønner ikke hva den skal gj&oslash;re med'+
		'<br /><code>'+ action+'</code>');
	}

};


jQuery( function(){

	jQuery('select.flat').flatSelectStyle();

	jQuery.varelager.resizeList();
	//jQuery.varelager.placeButtons();
	//jQuery('div#listWidget').selectable();
	

});


