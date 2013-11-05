jQuery(function() {
//
// SELECTION
//

var selection = {
	tabindex: 1,
	selected: [],
	scrollDelay: 0,
	_create: function(){

		var thisElement = jQuery(this.element),
			initialTop =  thisElement.position().top,
			This = this;


		jQuery(window).scroll(function(){			
			var windowTop = jQuery(window).scrollTop();
			var docHeight = jQuery(document).outerHeight(true);
			var selHeight = thisElement.outerHeight(false);

			//thisElement.css('border-color', 'orange');
			//thisElement.css('border-shadow', '3px');

			if ( windowTop > initialTop) {

				dDocElement	= jQuery(window).height() - thisElement.height();
				marginTop	= windowTop-initialTop;
				//thisElement.attr('title', "" +dDocElement +"-"+ jQuery(window).height() +"::"+ jQuery('#subsetWidget').height() );

				sswh = jQuery('#subsetWidget').height();
				if (  sswh <= marginTop && sswh > thisElement.height() ) {
						marginTop=jQuery('#subsetWidget').height();
				}
				
				if (dDocElement <= 0) {
					marginTop -= thisElement.find('div[class~="list-item"] :first').height() ;
					marginTop -= Math.abs(dDocElement);
				} else {
					marginTop +=5;
				}

				//thisElement.css('border-color', 'orange');
				thisElement.css('position', 'relative');
				thisElement.css('margin-top',  marginTop+'px');
			} else {
				thisElement.css('border-color', 'blue');
				thisElement.css('position', 'relative');
				thisElement.css('margin-top', '0px');

			}
		});
		/*
		*/

		/*
		var emptyMessage = jQuery('<p></p>')
				.attr('id', 'emptyMessage')
				.html(	"Ingen varer er valgt.<br>"+
						"Bruk varelisten eller forhåndlagrede filter.")
				.show();
		jQuery(this.element).append(emptyMessage);
		*/

	},
	additem: function(item){
		jQuery('#emptyMessage').hide();
		var id, itemId = parseInt(item.id,10),
			This = this, itemdiv;

		if (this.is_listed(item.id)) {
			jQuery(this.element).find('div').each(function(){
				id = jQuery(this).listitem('getProdId');
				if (itemId == id ) {
				 	jQuery(this).listitem('increment');
				}
			});
		} else {
			itemdiv = jQuery('<div class="ui-widget-content"></div>').  //.addClass('ui-selectee')
					listitem({
						count: 			1,
						prod_id: 		item.id,
						prod_name: 		item.product,
						prod_partnum: 	item.partnum,
						prod_category:	item.category,
						prod_supplier:	item.supplier,
						afterRemove:	function(){ This.removeitem(item.id); },
						tabindex:		this.tabindex
					});
			this.tabindex++;
			jQuery(this.element).append(itemdiv);
			this.selected.push( itemId );
		}
	},
	additemsFromFilter: function(filter) {
		console.log( 'additemsFromFilter():' + count( filter[0].products));
		var item, This=this;
		
		jQuery('#emptyMessage').hide();
		for (var i=0; i< count(filter[0].products); i++ ) {
			item = filter[0].products[i];
			if ( ! this.is_listed( item.id )) {
				var itemdiv = jQuery('<div class="ui-widget-content"></div>');
				itemdiv.listitem({
						count: 			0,
						prod_id: 		item.id,
						prod_name: 		item.name,
						prod_partnum: 	item.partnum,
						prod_category:	item.category.name,
						prod_supplier:	item.category.supplier.name,
						afterRemove:	function( id ){ This.removeitem(id); },
						//afterRemove:	This.removeitem,
						tabindex:		this.tabindex
					});
				this.tabindex++;
				this.selected.push( item.id );
				jQuery(this.element).append(itemdiv);
			//} else {
				//console.log("prod id "+item.id + " is listed");
				//jQuery.varelager.notifyNormal( item.name +' er allerede i listen');
			}
		}
		jQuery.varelager.resizeList();

	},

	removeitem: function(itemid){
		var ix = this.selected.indexOf( itemid );
		console.log( ix + '= indexOf ' + itemid );
		if (ix >=0) {
			this.selected.splice(ix,1);
		}
		if (this.selected.length === 0 ) {
			jQuery('#emptyMessage').show();
			jQuery.varelager.resizeList();
		}
	},
	is_listed: function(itemID){
		for (var i in this.selected) {
			if (this.selected[i] == itemID ) {
				return true;
			}
		}
		return false;
	},
	getSelectionForOrder: function(){
		var selection = new Array();

		jQuery(this.element).find('div[class~="list-item"]').each(function (){

			var item = { 
				'product':	jQuery(this).listitem('option', 'prod_name'),
				'prid':		jQuery(this).listitem('option', 'prod_id'),
				'partnum':	jQuery(this).listitem('option', 'prod_partnum'),
				'category':	jQuery(this).listitem('option', 'prod_category'),
				'supplier':	jQuery(this).listitem('option', 'prod_supplier'),
				'quantity':	[{'department':'', 
								'depid': 0, 
								'qty': jQuery(this).listitem('option', 'count')}]
			};
			selection.push(item);

		});
		return JSON.stringify( selection );

	},
	getSelection: function(){
		var selection = new Array();
		 
		jQuery(this.element).find('div[class~="list-item"]').each(function (){

			var item = { 
				id: jQuery(this).listitem('option', 'prod_id'),
				quantity: jQuery(this).listitem('option', 'count')
			};
			selection.push(item);

		});
		return selection;

	},
	clear: function() {

		this.selected = new Array();
		jQuery(this.element).find('div[class~="list-item"]').each(function (){
			jQuery(this).remove();
		});
		jQuery('#emptyMessage').show();

	},
	options: {
		items: {}
	}
	
};

//
// CATEGORIES 
//

var categories = {
	allcats: [],		// whatever server gives us
	options: {
		maxwidth: 10
	},

	_init: function(){
		this.fetch();
	},
	_create: function() {

		jQuery('<div title="ALLE"'+
			'class="ui-widget-content category-item">ALLE</div>').
			appendTo(this.element);

		var This = this,

		subset = jQuery('div#subsetWidget'),

		fireSelected = function(event, ui) {
			//var chosen= jQuery( ui.selected).attr('title');
			var chosen=  ui.selected.title ;
			if (chosen == "ALLE") { 
				console.log('ALLE!');
				console.log(This.allcats);
				for (var i=0; i<This.allcats.length; i++) {
					subset.trigger('showCategory', This.allcats[i]);
				}
			} else {
				subset.trigger('showCategory', chosen);
			}
			subset.trigger('rePopulate');
		},
		fireUnSelected = function(event, ui) {
			//var chosen= jQuery( ui.selected).attr('title');
			var chosen=  ui.unselected.title ;
			console.log("REMOVED: "+ chosen);
			if (chosen == "ALLE") { 
				for (var i=0; i<This.allcats.length; i++) {
					console.log("hide-trigger:" + This.allcats[i]);
					subset.trigger('hideCategory', This.allcats[i]);
				}
			} else {
				subset.trigger('hideCategory', chosen);
			}
		};

		this.element.selectable({
				selected: fireSelected,
				unselected: fireUnSelected
		}).addClass("beholdning-widget");

	},

	fetch: function() {
		var Cats = this,
			defined_categories = [];
		jQuery.ajax({
			url: jQuery.varelager.links.categories,
			//url: jQuery('#submitURLcreateorder').val(), 
			async: true,
			dataType: 'json',
			success: function(data) { 
				Cats.allcats = data; 
				Cats.populate();
			}
		});
		//return defined_categories;
	},
	populate: function() {
		var cats = this.allcats,
		i, len = count( cats ), name, label, element;
		for (i=0; i<len; i++) {
			name = cats[i];
			label = name.substring(0, this.options.maxwidth );

			if (name.length >= this.options.maxwidth ) {
				label = label+'.';
			}
 			element = ['<div title="'];
			element.push(name);
			element.push('" class="ui-widget-content category-item">');
			element.push(label);
			element.push('</div>');
			jQuery(element.join('')).appendTo(this.element);
		}
		jQuery.varelager.resizeList();
	},

	_refresh: function() { },
	_destroy: function() {},
	_setOptions: function() {
		// in 1.9 would use _superApply
		$.Widget.prototype._setOptions.apply( this, arguments );
		this._refresh();
	},

	_setOption: function( key, value ) {
		// prevent invalid color values
		if ( /red|green|blue/.test(key) && (value < 0 || value > 255) ) {
			return;
		}
		// in 1.9 would use _super
		$.Widget.prototype._setOption.call( this, key, value );
	}
};

//
// PRODUCTS 
//

var products = {
	shown_cats: [],
	allprods: {},		// products from server
	matchingProds: null,	// entries matching search input and category selection
	searchDelay: 0, 		// return value of setTimout
	searchString: 'Søk...', // default value
	searchInput: '',
	options: {
		destination_list_selector: 'div#listWidget'
	},
	_init: function(){
		this.fetch();
	},
	_create: function() {


		var This = this,
			len = 0,
			input = jQuery('input[name=searchProductInput]');


		input.val(this.searchString);
		input.bind('blur', function(event) {
			if (event.target.value === '')  {
				event.target.value = This.searchString;
			}
		});
		input.bind('focus', function(event) {
			if (event.target.value === This.searchString)  {
				event.target.value = '';
			}
		});

		input.bind('keyup', function(event) {
			clearTimeout(This.searchDelay);
			This.searchDelay = setTimeout(function() {
				This.searchInput = event.target.value; 
				This.setFilter(); 
			}, 300);
		});

		this.element.addClass("beholdning-widget").
			bind('rePopulate', function(event, chosen) {
				This.setFilter();
				console.log('rePopulate');
			}).
			bind('hideCategory', function(event, chosen) {
				for (var i=This.shown_cats.length-1; i>-1; i--) {
					if ( This.shown_cats[i] == chosen) {
						This.shown_cats.splice(i,1);
					}
				}
				console.log('hideCategory []='+ This.shown_cats.join(' '));
			}).
			bind('showCategory', function(event, chosen) {
				This.shown_cats.push(chosen);
				console.log('shown_cats []='+ This.shown_cats.join(' '));
			});
	},

	fetch: function() {
		jQuery.varelager.showAjaxLoader();
		var Prodlist = this,
			defined_products = {};
		jQuery.ajax({
			url: jQuery.varelager.links.products,
			async: true,
			dataType: 'json',
			success: function(data) { 
				Prodlist.allprods = data; 
				Prodlist.populate();
				jQuery.varelager.hideAjaxLoader();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				var msg = "Failed to fetch products!\n"+ 
					"reason: " + textStatus +"\n"+
					"errorThrown: " + errorThrown;
				jQuery.varelager.carrotPopup('widgets-beholdning',msg);
				console.log(jqXHR);
			}
		});
	},
	populate: function() {
		//console.log( "populate prod: " + count(prods) );
		jQuery(this.element).children().remove();
		
		var prods =  this.allprods,
			len = count( prods ),
			This = this;

		if (this.matchingProds === null) { 
			this.matchingProds = {};
		} else {
			prods =  this.matchingProds;
			// redo populate and setFilter to avoid this
		}

		len = count( prods );
		for (var i=0; i<len; i++) {
			var product = prods[i],
				item = jQuery('<a href="#" class="block"></a>'),
				prod = jQuery('<div class="ui-widget-content"></div>');

			product.fulltext = "{0} {1} {2} {3} kr {4}".
				format( product.partnum , product.supplier, product.category, product.product, 'kr '+product.price).
				toLowerCase();

			item.data('itemobject', product).attr('id', "product"+i).
				bind('click', function(){ 
					jQuery(This.options.destination_list_selector).
						selectionlist('additem', jQuery(this).data('itemobject') );
					jQuery.varelager.resizeList();
					return false;
				}).
				attr('title','[{0}>{1}:{2}] {3} ({4} kr,-)'.format( 
					product.supplier, product.category, product.partnum, product.product, product.price)).
				html(product.product);
			prod.html(item).appendTo(this.element);
		}
		jQuery.varelager.resizeList();
	},
	setFilter: function() {
		//console.log("setfilter() cats: " + this.shown_cats.join('<>'));
		var /*anchors = jQuery(this.element).find('div > a'),*/
			len = count(this.allprods), 
			terms = this.searchInput.split(' '),
			lent = count(terms),
			fulltext,
			idx, catidx, item;
		this.matchingProds = {}; // reset
		for (var t=0; t<lent; t++){ terms[t] = terms[t].toLowerCase(); }
		t=0;
		for (var i=0; i<len; i++) {
			for (var k=0; k<lent; k++) {
				fulltext 	= this.allprods[i]['fulltext'];
				idx			= fulltext.indexOf(terms[k]);
				catidx		= this.shown_cats.indexOf(this.allprods[i]['category']);
				if ( idx != -1 && ( catidx > -1 || this.shown_cats.length === 0) ) {
					this.matchingProds[t]= this.allprods[i];
					k = lent;
					t++;
				}
			}
		}
		//console.log(this.matchingProds);
		//console.log('calling pop');
		this.populate();
	},
	_destroy: function() {},
	_setOptions: function() {
		// in 1.9 would use _superApply
		$.Widget.prototype._setOptions.apply( this, arguments );
		this._refresh();
	},
	_setOption: function( key, value ) {}
};


//
// INIT WIDGETS
//
jQuery.widget('varelager.selectionlist', selection);
jQuery.widget('varelager.categories', categories);
jQuery.widget('varelager.products', products);
});
