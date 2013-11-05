/* DB TABLE
 * displays a view from the db with options to manipulate the data.
 * sheads: FIX
 * headers: what to name the various columns. If there is a columns called 
 *          name, phone, room  and our headers are supposed to be in 
 *          Norwegian, we may want to put Navn, Tlf., Rom here.
 * fields: fields to include from the json-data. Field names must match. 
 * 				 fields will still be present in options.row, but will not appear 
 * 				 on the web-interface.
 * type: must have the same number of elements as columns in the json-data
 * skip: tells which fields to skip when building the data. 
 *       must be as long as type (above)
 * dataUrl: where to  fetch json-data
 * mboxes: FIX
 */
var jtable = {
	options: {
		pickedAction: "Users/Permissions/Products/...",
		sheads: [],
		headers: [],	/* display titles */
		fields: [], 	/* properties to include */
		skip: [], 		/* fields to ignore*/
		type: [], 		/* i = input, s = none, t = textarea */
		rows: [],
		emptyRow: {},
		dataUrl: '/db/get-json/admin*', /* nonfunctional default */
		webroot: jQuery.varelager.links.buttons,
		mboxes: {},
		save: null, // function() 
		del: null, // function()
		elementsPerPage: 15,
		elementsPerPageDefault: 15,
		activePage: 1,
		orderBy: {},
		// DEBUG
		oneColTime: 0,
		ButtonsTime: 0,
		moo: 0
	},
	previousDataUrl: '',
	_init: function(){
		//console.log("jtable._init(): " + this.options.pickedAction);
		var cookieEps = parseInt(getCookie(
			this.options.pickedAction +":eps"), 10);
		if (isNumber( cookieEps )) {
			//console.log("setting eps to: " + cookieEps);
			this.options.elementsPerPage = cookieEps;
		} else {
			this.options.elementsPerPage = 
			this.options.elementsPerPageDefault;
		}
	},
	_create: function() {
		//console.log('jtable._create(): ' + this.options.pickedAction);
		/*
		if (this.options.rows.length === 0) {
			this.options.rows =  this.fetchRows();
		}
		this.previousDataUrl = this.options.dataUrl;
		*/

	},
	paginationDiv: {},
	createPaginationOptions: function() {
		var eps = this.options.elementsPerPage,
			p	= this.options.activePage,
			len = this.options.rows.length,
			Table = this,
			from = 0,
			to	= 0,
			nop = 0,
			epli= jQuery('<li class="input"> linjer per side: </li>'),
			epin= jQuery('<input value="20" name="pagination-eps" type="text" />'),
			ul	= jQuery('<ul></ul>'),
			div = jQuery('<div class="db-table-pagination"></div>');
		epin.val( eps);
		epin.focus( function(){ this.select(); });
		epin.blur(function(){
			if (isNumber(parseInt(this.value,10)) ) { 
				Table.options.elementsPerPage = this.value;
				setCookie( Table.options.pickedAction +":eps", this.value);
				Table.refresh();
			}
		});
		epin.keypress( function(event) { 

			if (event.which == 13 && isNumber(this.value) ) { 
				Table.options.elementsPerPage = this.value;
				setCookie( Table.options.pickedAction +":eps", this.value);
				//setTimeout( function() {Table.refresh();} ,100);
				Table.refresh();

			}
		});

		nop = (parseInt(len/eps, 10) + ( (len%eps===0)? 0 : 1) );
		if ( p >= nop ) { p = nop; Table.options.activePage = nop; }
		if ( p < 1 ) {p=1; Table.options.activePage = 1;}
		
		from = p - 5 ;
		to = p + 5 + 1 ;

		if ( from < 1) { to += 1+Math.abs(from); from=1; }
		if ( to > nop) { from -= to-nop; to = nop+1; }
		if ( from < 1) { from = 1; }

		
		for (var i=from; i<to; i++) {
			var a = jQuery('<a href="#"></a>').html(i).
				data('page', i).
				attr('title','gå til side '+i).
				bind('click', function(){
					Table.options.activePage = jQuery(this).data('page');
					Table.refresh();
					return false;
			});
			if ( p === i ) { a.addClass('pagination-current'); }
			ul.append(jQuery('<li></li>').html(a));
		}

		jQuery('<li></li>').html(
		jQuery('<a class="arrows" href="#" title="hopp 10 sider tilbake">&lsaquo;</a>').
			bind('click',function(){Table.options.activePage -=10;Table.refresh();})).
			prependTo(ul);

		jQuery('<li></li>').html(
		jQuery('<a class="arrows" href="#" title="hopp 10 sider fram">&rsaquo;</a>').
			bind('click',function(){Table.options.activePage +=10;Table.refresh();}).
			appendTo('<li></li>')).
			appendTo(ul);

		jQuery('<li></li>').html(
		jQuery('<a class="arrows" href="#" title="gå til side 1">&laquo;</a>').
			bind('click',function(){Table.options.activePage -=10;Table.refresh();}).
			appendTo('<li></li>')).
			prependTo(ul);

		jQuery('<li></li>').html(
		jQuery('<a class="arrows" href="#" title="gå til side '+nop+'">&raquo;</a>').
			bind('click',function(){Table.options.activePage +=10;Table.refresh();}).
			appendTo('<li></li>')).
			appendTo(ul);

		ul.appendTo(div);
		epin.appendTo(epli);
		epli.appendTo(ul);
		//div.append('<div class="cleardiv"></div>');

		return div;
	},
	refresh: function() {
		jQuery.varelager.showAjaxLoader();

		if (this.options.rows !== null && this.options.rows.length === 0 ) {
			//console.log(" if empty - fetch in refresh() ");
			//this.options.rows =  this.fetchRows(
			this.fetchRows( this.options.dataUrl +"/"+ JSON.stringify( this.options.orderBy));
			return; 

			//url: from+"/"+JSON.stringify( { 'supplier_id': 'DESC', 'category_id': "ASC" }),
		}

		var rows, tr, td, sfidx, subfield, fld, headers, sheads, skip, 
		type, tbl, fragment, Table = this;
		subfield= false;
		sfidx	= 0;
		cont	= this.element;
		fields	= this.options.fields;
		headers	= this.options.headers;
		sheads	= this.options.sheads;
		skip	= this.options.skip;
		type	= this.options.type;
		rows	= this.options.rows;
		fragment= document.createDocumentFragment();
		// TODO should update columns/rows and not the whole element
		// need to adapt some kind of model-view framework
		cont.children().remove();

		jQuery(this.paginationDiv).remove();
		this.paginationDiv = this.createPaginationOptions();
		jQuery('#actionPickerClearDiv').before( this.paginationDiv);
		tbl 	= jQuery('<table class="db-table"></table>');
		tr 		= jQuery('<tr><th class="save-delete"></th></tr>').appendTo(tbl);

		// table headers
		for (var i=0; i<headers.length; i++) {
			fld =  headers[i];
			td = jQuery('<th class="crud-header"></th>');
			if (typeof fld === 'object') {
				for (var m=0; m<fld['f'].length; m++) {
					var fname	= fld['n'];
					var sfld	= fld['f'][m];
					td = jQuery('<th></th>');
					td.html(sfld);
					td.addClass("crud-header");
					td.addClass("crud-header-"+fname);
					if (!this.options.skip[i+m]) { 
						var noni = (this.options.orderBy[fields[i]['f'][m]] == 'DESC' )? 1  : -1; // cleanme order
						var a = jQuery('<a href="#">' + td.html()+'</a>');	// cleanme order
						a.data('nonincreasing', noni);						// cleanme order
						a.data('columnName', fields[i]['f'][m]);						// cleanme order
						td.html( a );										// cleanme order
						td .appendTo(tr); 

						// cleanme order
						a.bind('click', function() {
							var ci = i;
							var noni = jQuery(this).data('nonincreasing'),
								col = jQuery(this).data('columnName');
							noni = noni * -1;
							Table.options.orderBy[col] =  (noni==1)? 'DESC': 'ASC'; // rep
							if ( countProperties(  Table.options.orderBy ) > 1 ) {
								Table.options.orderBy = {};
								Table.options.orderBy[col] =  (noni==1)? 'DESC': 'ASC'; // rep
							}
							Table.options.rows = []; // force re-fetch
							Table.refresh();
							return false;
						});
					}
				}
			} else {
				td .html(fld);
			}
			if ( type[i] === 'dscp'	) { td.addClass('product-category'); }
			if ( type[i] === 'c' 	) { td.addClass('checkbox'); }
			if ( type[i] === 'ti'	) { td.addClass('category-tag'); }
			if ( fields[i] === 'zipcode') { td.addClass('zipcode');}

			if (!this.options.skip[i]) { 
				var noni = (this.options.orderBy[fields[i]] == 'DESC' )? 1  : -1; // cleanme order
				var a = jQuery('<a href="#">' + td.html()+'</a>');	// cleanme order
				a.data('nonincreasing', noni);						// cleanme order
				a.data('columnName', fields[i]);					// cleanme order
				td.html( a );										// cleanme order
				td .appendTo(tr); 

				// cleanme order
				a.bind('click', function() {
					var ci = i;
					var noni = jQuery(this).data('nonincreasing'),
						col = jQuery(this).data('columnName');
					noni = noni * -1;
					Table.options.orderBy[col] =  (noni==1)? 'DESC': 'ASC'; // rep
					if ( count(  Table.options.orderBy ) > 1 ) {
						Table.options.orderBy = {};
						Table.options.orderBy[col] =  (noni==1)? 'DESC': 'ASC'; // rep
					}
					Table.options.rows = []; // force re-fetch
					Table.refresh();
					return false;
				});
			}
		}

		// table body
		for (var j= this.options.elementsPerPage * 
					this.options.activePage - 
					this.options.elementsPerPage;
				(j<	this.options.elementsPerPage * 
					this.options.activePage &&
				j< rows.length); 
				j++) {
			if ( count( rows[j]) !==  0 ) {
				this.onerowShow(rows[j], j).appendTo(tbl);
			}
		}

		if (this.options.emptyRow ) {
			this.onerowShow(this.options.emptyRow, -1).appendTo(tbl);
		}
		tbl.appendTo(cont);
		//jQuery(fragment).appendTo(cont);
		//console.log("one col: "+ this.options.oneColTime);
		//console.log("buttons : "+ this.options.ButtonsTime);

		jQuery.varelager.hideAjaxLoader();
	},

	/**
	 * Process individual row in the table and figure out how to display them.
	 * The method traverses predefined types. If any of the fields are ojects, 
	 * they are assumed to have key-value pairs wth keys corresponding to the 
	 * predefined fields in options.fields. The values may be either plain 
	 *
	 * defi
	 * aRow: an object with key-values representing a single row. 
	 *       fields containing more than a single value are objects
	 * rowNr: position in data-array (index) 
	 */
	onerowShow: function(aRow, rowNr, skip) {
		var tr		= jQuery( document.createElement('tr')).attr('id', 'row'+rowNr);
		var fields	= this.options.fields;
		var skip	= this.options.skip;
		var type	= this.options.type;
		var subfield= true;



		tr.data('rowNr', rowNr);
		tr.addClass( (rowNr%2==1) ? 'altrow' : '');
		if (typeof aRow === 'undefined' || aRow === null || count(aRow) === 0 ) {
			// cant build table rows, when we know nothing about them
			//console.log("onerowShow(): aRow == null");
			console.log("should skip");
			return null;
		} 
		this.buttonsColumn(tr, rowNr);
		//console.log( "onerowShow() "+ rowNr+ " "+ aRow['name']);

		for (var i=0, len=fields.length; i<len; i++) {
			var fld = fields[i];

			// the field is actually a connection to another table.
			// such as name, phone, {address}
			if (typeof fld === 'object') {

				// the basic idea here is to flatten out nested tables
				// and to present their values as felds from the nesting
				// table. e.g.
				// name, phone, street, city, ...
				for (var m=0; m<fld['f'].length; m++) {
					var fname	= fld['n'];
					var sfld	= fld['f'][m];
					var sfval;

					/*
					if (!aRow[fname]) {
						// misconfigured feields. expected values do not 
						// match whats in the db-view-json
						console.log(fname +' is not in aRow' );
					}
					*/
					// collapse values if a subfield is an array skipping any sub-values
					// that are marked to be skipped
					if (aRow[fname].length) {
						for (var k=0;k< aRow[fname].length; k++){
							if (! skip[i+m]) {
								if (!sfval) {
									sfval = [];
									sfval.push( aRow[fname][k][sfld] );
								} else {
									sfval.push( aRow[fname][k][sfld] );
								}
							}
						}
					} else {
					/* name: moo, srname: foo, address { street: sesame, city:  ny } 
					 * arow[address][street] 
					 * this is the common scenario
					 */
						sfval = aRow[fname][sfld];
					}
					this.oneColumn(fname, sfld, sfval, tr, skip[i+m], type[i+m], rowNr);

				}
			} else { 
				this.oneColumn(!subfield, fld, aRow[fields[i]], tr, skip[i], type[i], rowNr); 
			}

		}
		return tr;
	},
	/*
	inputTooltipBind: function(event) {
		var input = jQuery( event.target );
		//var input = jQuery( event.data.input );
		var intmp = jQuery('<span>'+input.val()+'</span>');
		console.log( "HOVER "+input.val() );
		jQuery('body').append(intmp);
		if ( intmp.width() > input.width() ){
			console.log(input.val() + "is too long");
			input.tooltip({
					track: true,
					delay: 100,
					opacity: 1,
					top: -40,
					left: 5
			});
		}
		return true;
	},
	*/

	/**
	 * Translates a single column-row value from the database into a editing-useful html markup.
	 * subfield: the caller inspects the parent row for this column and decides 
	 *           wether it's a special field. The value is the name of the table
	 *           that was join on this spot.
	 * fName: name of the column as received from the db
	 * val: the actual data storen in the field
	 * row: parent html element
	 * skip: wether or not to show this field at all (skip keys and such..)
	 * type: decides how to markup a given field.
	 *  1  skip (not used)
	 * 	i  <input type=text>
	 * 	id <input type=text disabled=disabled>
	 * 	t  <textarea>
	 * 	c  <input type=checkbox>
	 * 	d  <div> plain text
	 * 	m  menuBox <ul><li>
	 * 	s  drop-down <select><options>
	 * 	sr  drop-down roles
	 * 	dscp double select category primary
	 * 	dscs double select category secondary
	 * rowNr: position in the data-array (index)
	 */
	oneColumn: function(subfield, fName, val, row, skip, type, rowNr) {
		var s = (new Date).getTime(); // DEBUG
			/*
			 */
		var inp, td = jQuery('<td></td>');
		if ( fName=== 'zipcode') { td.addClass('zipcode');}

		if (!skip) {
			if (type === 'i') {
				inp = jQuery('<input name="'+fName+'" type="text" value="'+val+'"></input>');
				inp.attr('title',val);
				inp.focus( function(){ this.select(); });
				// .tooltip() adds alt="" to input which isnt valid
				//inp.tooltip({ track: true, delay: 100, opacity: 1, top: -30, left: 5 });
			} else 
			if (type === 'id') {
				inp = jQuery('<input name="'+fName+'" type="text" value="'+val+'" disabled="disabled"></input>');
			} else
			if (type === 't') {
				inp = jQuery('<textarea></textarea>') .attr('name', fName) .html(val);
				inp.focus( function(){ this.select(); });
				inp.attr('title',val);
				inp.tooltip({ track: true, delay: 100, opacity: 1, top: 0, left: 5 });
			
			} else 
			if (type === 'c') {
				inp = jQuery('<input name="'+fName+'" value="'+(val?1:0)+'" type="checkbox" '+
						(val==1?'checked="checked"':'')+'></input>');
				td.addClass('checkbox');

			} else 
			/*
			if (type === 'm') {
				var text = '<ul class="onglets-vert catsupp"><li><a href="#" style="text-transform: none;">kategorier</a></li></ul>';
				inp = jQuery('<div></div>');
				inp.data('rowNr', rowNr);
				widgetName = this.options.mbtypes[fName];
				widgetOpts = this.options.mboxes[val] || {};
				widgetOpts['rowNr'] = rowNr;
				widgetOpts['parentTable'] = this;

				inp[widgetName].call(inp, {mboxOptions: widgetOpts});
			} else 
			*/
			if (type === 'dscp') {

				var suppid, catid;
				inp = jQuery('<div name="doubleselect'+ rowNr +'"></div>');

				if (jQuery.varelager.productcategories === null || 
						typeof jQuery.varelager.productcategories === 'undefined') {
					jQuery.varelager.productcategories = 
					this.fetchRows(jQuery.varelager.links.formSuppcat, 'sync');
				}

				if ( rowNr > -1 ) {
					suppid= this.options.rows[rowNr]['suppid'];
					catid= this.options.rows[rowNr]['catid'];
				} else {
					suppid = catid = 1;
				}
				inp.doubleselect({
					// INITDS
					categories:	jQuery.varelager.productcategories,
					dataUrl:	jQuery.varelager.links.formSuppcat,
					primaryId:	suppid,
					secondaryId:catid
				});
				inp.addClass('product-category');
			} else 
			if (type === 'dscs') {
				//inp = jQuery('<div name="doubleselect'+ rowNr+'sec"></div>').html(val);
				//ignored...
				true;
			} else 
			if (type === 'sr') {
				if (this.options.roles === null || typeof this.options.roles === 'undefined') {
					this.options.roles = this.fetchRows(jQuery.varelager.links.roles, 'sync');
				}
				roles = this.options.roles;

				//var div = jQuery('<div style="height:20px;width:120px"></div>');
				inp = jQuery('<select style="border:none" size="'+ count( roles) +'" name="'+fName+'" multiple="multiple"></select>');
				for (var role in roles){
					enabled = "";
					val = (typeof val === 'undefined') ? [] : val; // todo fix adminPermissions json-input
					for (i=0; i<val.length; i++) {
						if (val[i] == role) {
							enabled = 'selected="selected"';
							i=val.length;
						}
					} 
					jQuery('<option class="admin-input-option-role" ' + enabled +
						' value="'+role+'">'+roles[role]+'</option>').appendTo(inp);
				}
				//div.html(inp);
				//div.resizable({ maxHeight:140, minHeight: 20 , minWidth: 120, maxWidth: 120  } );
				//inp = div;
			} else 
			if (type == 'ti') {
				td.addClass('categoriy-tag');
				val = (val===null)? "": val;
				var inputvalues = jQuery('<input name="'+fName+'" type="hidden"></input>'),
					assignedCategories = val.split(',');

				td.append(inputvalues);
		
				if (jQuery.varelager.definedCategories === null || 
						typeof jQuery.varelager.definedCategories === 'undefined') {
					jQuery.varelager.definedCategories = 
					this.fetchRows(jQuery.varelager.links.categories, 'sync');
				}
				inp = jQuery('<ul></ul>').tagit({
					availableTags: jQuery.varelager.definedCategories,
					singleField: true,
					singleFieldNode: inputvalues,
					caseSensitive: false,
					singleField: true
				});
			
				inp.addClass('category-tag');
				inp.resizable({ ghost: true, grid:[80,66], minHeight:"2em", minWidth: 60, maxWidth: 300  } );
				
				for (i=0; i< assignedCategories.length; i++) {
					inp.tagit('createTag', assignedCategories[i].trim() );
				}
				
				// createTag(tagName, additionalClass)
			} else {
				inp = jQuery('<div></div>').html('unknown column type');
				//inp = val;
			}

			if (inp.addClass) {
				inp.addClass('admin-input');
			} else {
				console.log('cannot add class to ' + fName);
			}

			td.append('\n');
			//td.html(inp);
			td.append(inp);
			if (subfield) {
				td.addClass('admin-input-sub');
				td.addClass(subfield);
			} else {
				td.addClass('admin-input');
			}
			td.appendTo(row);
		} 
		var diff = ((new Date).getTime() - s); 
		this.options.oneColTime  =this.options.oneColTime+  ((new Date).getTime() - s); //DEBUG
		//else { console.log('skipped ' +subfield+':'+fName+' = '+val); }
	},
	buttonsColumn: function(row, rowNr) {
		var s = (new Date).getTime(); // DEBUG
		This = this;
		var webroot	= this.options.webroot;
		
		var td 	= jQuery('<td class="save-delete"></td>'),
			div = jQuery('<div class="save-delete"></div>').appendTo(td);

		if (rowNr < this.options.rows.length) {
			submitImg = rowNr < 0 ? 'add20.png' : 'floppy20.png';

			jQuery('<a title="lagre" onclick="return false;" href="#"><img ' +
					'src="'+webroot+'/'+submitImg+'" alt="lagre"></img></a>').
				bind('click', function(event) { 
					if (This.options.save) {
						//This.options.save(e, rowNr); 
						console.log( "calling from This: ");
						console.log( This );
						This.options.save.call( This, event, rowNr);
					} else {
						This.saveRow.call(This, event, rowNr );
					}
			}).appendTo(div);

			if (rowNr >= 0)  {
				jQuery('<a title="slett" onclick="return false;" href="#"><img src="'+webroot+
						'delete20.png" alt="slett"></img></a>').
					bind('click', function(e) { 
						if (This.options.del !== null) {
							This.options.del(e, rowNr);
						} else {
							This.deleteRow.call(This, e, rowNr );
						}
				}).appendTo(div);
			}
		}
		td.appendTo(row);

		/*		
		} else {
			jQuery('<a></a>')
				.attr('title', 'ny')
				.attr('href', '#')
				.attr('onclick', 'return false;')
				.html( jQuery('<img></img>') 
						.attr('src', webroot+'add20.png') 
						.attr('alt', 'new'))
				.bind('click', function(e) {
					This.options.addnew(e, rowNr);
				}) .appendTo(td);
		*/

		this.options.ButtonsTime += ((new Date).getTime() - s); //DEBUG

	},
	getInputs: function(rowIndex) { 
		var kv = {};
		// <a>.parent()=<td>.siblings()=<td,...>
		//siblings = jQuery(clickedSibling).parent().siblings().find('input,textarea,select');
		rowInputs = jQuery('tr#row'+rowIndex).children().find('input,textarea,select');
		//console.log( "getInputs "+ rowIndex);
		//console.log( rowInputs );
		rowInputs.each(function(){
			t = jQuery(this);
			if  (t.attr('type')==='checkbox') {
				val = t.attr('checked') ? 1 : 0; 
			} else if (t.is('select')) {
				val = [];
				t.find('option :selected').each(function(){ 
					console.log( 'pushing option: ' + jQuery(this).val() ); 
					val.push( jQuery(this).val() ); 
				});
				if (val.length === 1) { val = val[0]; }
				if (val.length === 0) { val = t.val(); } 
			} else {
				val =  t.attr('value');
			}
			//console.log( "pushing: " +t.attr('name') + "\tvalues:<"+val+">" );
			if (t.attr('name') !== 'undefined' && t.attr('name') !== '') {
				kv[t.attr('name')] = val;
			}
		});
		return kv;
	},
	deleteRow: function(event, rowNr) { // persist change to db
		var Table = this; 
		var dbID = 0; // invalid 0 == add new
		var id_field=this.options.fields[0];
		console.log("deleting on row: "+ rowNr);
		console.log("id-field: "+ id_field);
		console.log("id-value: "+ this.options.rows[rowNr][id_field]);

		if (typeof this.options.rows[rowNr][id_field] !== 'undefined') {
			dbID = this.options.rows[rowNr][id_field];
		}
		jQuery.varelager.doAction('delete' + this.options.pickedAction, dbID, 
			function() {
				Table.options.rows.splice( rowNr, 1, {});
				jQuery('#row'+rowNr).remove();
		});
	},
	getDBIDofRow: function(rowNr) {
		if (rowNr > -1 ) {
			if (this.options.rows !== null) {
				if (typeof this.options.rows[rowNr][this.options.fields[0]] !== 'undefined') {
					return this.options.rows[rowNr][this.options.fields[0]];
				}
			}
		}
		return 0;
	},
	saveRow: function(event, rowNr) { // persist change to db
		var Table = this,
			ID = Table.options.fields[0],
			kv = this.getInputs( rowNr );

		
		/*
		dbID = 0; // invalid 0 == add new
		if (rowNr > 0 ) {
			if (typeof this.options.rows[rowNr][this.options.fields[0]] !== 'undefined') {
				dbID = this.options.rows[rowNr][this.options.fields[0]];
			}
		}
		*/

		// ID

		kv[ ID] = this.getDBIDofRow( rowNr );

		console.log( "saveRow # " +rowNr+ " IDFIELD:"+ ID +" dbid:"+ kv[ ID ]);
		console.log( "saveRow values: "  );
		console.log( kv );

		var successCB = (kv[ID] > 0)? function(){}
			:function(textStatus, data,  jqXHR) {  
				var str = data.responseText;
				lastId =  parseInt( str.substring(str.lastIndexOf('id:')+3), 10);
				if (isNumber(lastId)) {
					kv[ID]= lastId;
				} else {
					kv[ID]= -2;
				}
				console.log("append row");
				console.log(kv);
				Table.appendRow( kv ); 
			};

		jQuery.varelager.doAction('save' + this.options.pickedAction, kv, successCB);
	},
	appendRow: function(rowData) {
		// FIX must set correct db id or updates will go to wrong 
		// row
		// after successfully inserting a row in the db
		this.options.rows.push(rowData);
		var tr = this.onerowShow( rowData, this.options.rows.length -1);
		var lastRow = jQuery('#row-1');
		lastRow.before( tr );
		if (this.options.emptyRow ) {
			lastRow.replaceWith(this.onerowShow(this.options.emptyRow, -1));
			lastRow.find('select').doubleselect('change');
		}

	},
	updateRow: function(aRow) { // as in refreesh view+model after user input
		rows 	= this.options.rows;

		// update the 'model'
		for (var key in aRow) {
			if (key !== 'rowNr') {
				rows[aRow.rowNr][key] = aRow[key];
			}
		}

		// update the view
		var ix = 0;
		var allRows = jQuery('tr');
		while (ix < allRows.length){
			var r = allRows[ix];
			ix++;
			if (jQuery(r).data('rowNr') == aRow['rowNr']) { 
				ix = allRows.length;
				for (var ki in aRow) {
					if (ki != 'rowNr'){
						// FIX will not update elements without a name attribute
						jQuery(r).find('input[name="'+ki+'"]').val(aRow[ki]);
					}
				}
			}
		}
		//this.refresh();
	},

	fetchRows: function(from, sync) {
		if (jQuery.varelager.ajaxHasFailed) {
			return null;
		}
		from = (typeof from !== 'undefined') ? from : this.options.dataUrl;
		var tuples = [],
			Table = this;
		jQuery.ajax({
			url: from,
			async: (sync==='sync')? false : true,
			dataType: 'json',
			error: function(a,b,c) {
				Table.options.rows=null;
				jQuery.varelager.ajaxError.call(this, a,b,c);
			},
			success: function(data) { 
				//console.log("fetchRows:ajax:success() -> Table.opt.rows = data ");
				if (this.async) {
					Table.options.rows = data;
				}
				tuples = data; 
			},
			complete: function(xmlHtmlReq, textStatus){
				//console.log("fetchRows:ajax:complete() -> Table.refresh() ");
				//console.log("fetchRows:ajax:complete() -> opt len =  "+ Table.options.rows.length );
				//console.log( xmlHtmlReq );
				if (this.async) { // assuming the main "rows" url was fetched
					//Table.options.rows = JSON.parse( xmlHtmlReq.responseText );
					//console.log( Table.options.rows );
					Table.refresh();
				}

			}
		});
		return tuples;
	},
	destroy: function() {
		$.Widget.prototype.destroy.call( this );
	}
};

//
// MENU BOX CATEGORY PICKER WIDGET
//
/*
*/

//var menuboxPickerCategory = {
//	
//	options: {
//		mboxOptions: {}
//	},
//	_create: function(){
//		var element = jQuery(this.element);
//		var suppliers 	= {};
//		var cats = jQuery.varelager.dataCatSupp;
//
//		if (cats.length === 0 ) {
//			jQuery.varelager.dataCatSupp = this.fetchRows();
//			cats = jQuery.varelager.dataCatSupp;
//			//cats = this.fetchRows();
//		}
//		// 1 get data from db like u need
//		// 2 dont create 1 widget for every row - reuse (on hover)
//
//		for (var i=0; i<cats.length; i++) {
//			var
//			supplier 	= cats[i]['supplier'],
//			category 	= cats[i]['category'],
//			suppid		= cats[i]['suppid'],
//			catid		= cats[i]['catid'];
//			if (suppliers[supplier]) {
//
//				if (suppliers[supplier]['cats'][category]) {
//					suppliers[supplier]['cats'][category].names.push(category);
//				} else {
//					suppliers[supplier]['cats'][category] = {name:category, id:catid};
//
//				}
//			} else {
//				newcat = {name:category, id:catid};
//				newsupp = { name: supplier, id:suppid, cats:{} };
//				suppliers[supplier] = newsupp;
//				suppliers[supplier]['cats'][category] = newcat;
//
//			}
//
//
//		}
//
//		var l0 = jQuery('<ul class="onglets-vert catsupp"></ul>').appendTo(element);
//		var l0t= jQuery('<li><a style="text-transform:none" href="#">kategorier</a></li>').appendTo(l0);
//		var l1 = jQuery('<ul></ul>').appendTo(l0t);
//		for (var s in suppliers){
//			var l1t = jQuery('  <li><a onclick="return false;" href="#">'+suppliers[s].name+'</a></li>').appendTo(l1),
//				l2  = jQuery('      <ul></ul>').appendTo(l1t);
//			for (var c in suppliers[s]['cats']) {
//				var	l2t = jQuery('<li ></li>').appendTo(l2);
//				var leaf= jQuery(
//				'<a href="#" class="liLeaf">'+suppliers[s]['cats'][c].name+ '</a>'+
//				'<input type="hidden" name="category" value="'+suppliers[s]['cats'][c].name+'"></input>'+
//				'<input type="hidden" name="catid"    value="'+suppliers[s]['cats'][c].id+'"></input>'+
//				'<input type="hidden" name="supplier" value="'+suppliers[s].name+'"></input>'+
//				'<input type="hidden" name="suppid"   value="'+suppliers[s].id+'"></input>'+
//				'<input type="hidden" name="rowNr"    value="'+this.options.mboxOptions.rowNr+'"></input>').
//				appendTo(l2t);
//			}
//		}
//		l0.menuBox(this.options.mboxOptions);
//		//this.options.ulElement = l0;
//		//this.setSelected();
//
//	},
//	setSelected: function() {
//
//		rn 		= this.options.mboxOptions.rowNr;
//		//jtablew = jQuery('#dbTableWidget').data('jtable');
//		rows 	= this.options.mboxOptions.parentTable.options.rows;
//
//
//		if (rn < rows.length && rn != -1 )  {
//
//		jQuery(this.element).find('.liLeaf').each(function() {
//			var supcat = {};
//			jQuery(this).siblings('input').each(function(){
//				var input = $(this);
//				supcat[input.attr('name')] = input.attr('value');
//			});
//
//			if (supcat['suppid'] == rows[rn]['suppid'] && 
//				supcat['catid'] == rows[rn]['catid']) {
//					underline = {'text-decoration': 'underline'};
//					jQuery(this).css(underline);
//					jQuery(this).parent().parent().siblings('a').css(underline);
//			}
//		});
//
//		}
//	},
//	fetchRows: function() {
//		var tuples = [];
//		jQuery.ajax({
//			url: this.options.mboxOptions.dataUrl,
//			async: false,
//			dataType: 'json',
//			success: function(data) { tuples = data; }
//		});
//		return tuples;
//	}
//
//};
//
// DOUBLE SELECT CATEGORY WIDGET
//


doubleselect = {
	options: {
		categories: [],
		dataUrl: {},
		primaryId: 0,		// supplier id
		secondaryId: 0,		// category id
		secondaryName: "catid",	// name attribute of select box with categories (catid)
		primarySelect: {},
		secondarySelect: {}
	},
	undobutton: function(){
	},
	_create: function(){


		var cats = this.options.categories, 
			cont = jQuery( this.element),
			form = jQuery('<form action=""></form>'),
			This = this,
			undoButton,
			suppliers = {},
			primary	= this.options.primarySelect, 
			secondary	= this.options.secondarySelect,
			primarySelected	= this.options.primarySelected, 
			secondarySelected	= this.options.secondarySelected,
			fragment = document.createDocumentFragment();

		undoButton = jQuery('<a title="gjennoprett" onclick="return false;" href="#"><img src="'+
					jQuery.varelager.links.buttons+'undo16.png" align="top" alt="angre"></img></a>').
				bind('click', function(e) { 
					This.setSelected( primary, This.options.primaryId);
					This.setSelected( secondary, This.options.secondaryId);
		});

		if (cats.length === 0) {
			//console.log("SHOULD NOT HAPPEN");
			this.options.categories = this.fetchRows( this.options.dataUrl);
		}

		primary = jQuery('<select class="double-select-primary flat" name="suppid"></select>');
		secondary = jQuery('<select class="double-select-secondary flat" name="catid"></select>');

		for (var i=0; i<cats.length; i++) {
			var
			supplier 	= cats[i]['supplier'],
			category 	= cats[i]['category'],
			suppid		= cats[i]['suppid'],
			catid		= cats[i]['catid'];
			if (suppliers[supplier]) {

				if (suppliers[supplier]['cats'][category]) {
					suppliers[supplier]['cats'][category].names.push(category);
				} else {
					suppliers[supplier]['cats'][category] = {name:category, id:catid};

				}
			} else {
				newcat = {name:category, id:catid};
				newsupp = { name: supplier, id:suppid, cats:{} };
				suppliers[supplier] = newsupp;
				suppliers[supplier]['cats'][category] = newcat;
			}
		}
		for (var s in suppliers ){
			jQuery('<option name="suppid" value="'+suppliers[s].id+'">'+suppliers[s].name+'</option>').
			appendTo(primary);
			for (var c in suppliers[s]['cats']) {
				var opt = jQuery('<option name="catid" ></option>').
					data('suppid',	suppliers[s].id).
					data('wrapped',	false).
					val(			suppliers[s]['cats'][c].id).
					html(			suppliers[s]['cats'][c].name).
					appendTo(secondary);
				//opt.wrap('<span>').hide();
			}
		}
		this.options['primarySelect'] = primary;
		this.options['secondarySelect'] = secondary;

		primary.appendTo(form);
		secondary.appendTo(form);
		form.appendTo(cont);
		undoButton.appendTo(form);

		primary.change( { secondaryname: this.options.secondaryName}, this.__onchange);
		this.setSelected( primary, this.options.primaryId);
		this.setSelected( secondary, this.options.secondaryId);
		//primary.change();
	},
	setSelected: function(selectElement, id) {
		var anOption, selectedOption, opts;
		selectElement.children('option').removeAttr('selected');
		//selectElement.children("option").each(function(){
		opts = selectElement.children('option');
		for (var i=0; i< opts.length; i++) {
			//anOption = jQuery(this);
			if (id == opts[i].value /*option.val()*/) {
				selectedOption = opts[i];
				i = opts.length;
			}
		}
		selectedOption.selected= 'selected';
		//selectElement.val( jQuery(':selected', selectElement).val() );
		selectElement.val( selectedOption.value );

	},
	__onchange: function(event){
		//console.log('event.currentTarget.name: ' +event.currentTarget.name + ' val: ' + event.currentTarget.value );
		//console.log('event.data.secondaryname: '+ event.data.secondaryname);
		var opt, 
			prim 	= jQuery(event.currentTarget), 
			primId	= prim.find(':selected').val(), 
			secOpts = jQuery('select[name="{0}"] option'.format(event.data.secondaryname), prim.parent());
			//console.log( dump(secOpts, 1, 1) )
			//hidden = []; shown = [];
			console.log( typeof secOpts );
			console.log( secOpts );
			console.log( 'size:'+ secOpts.size() );
			console.log( 'count:'+ count( secOpts) );
			console.log( 'length:'+ secOpts.length );
			for (var i=0; i< secOpts.size(); i++){
				opt = jQuery(secOpts[i]);

				if ( ! opt.data('wraped')) {
					//hidden.push( '{0}:{1}, '.format(opt.html(), opt.val()));
					opt.wrap('<span>').hide();
					opt.data('wraped', true);
				}

				if (opt.data('suppid') == primId) {
					//shown.push( '{0}:{1}, '.format(opt.html(), opt.val()));
					opt.unwrap('<span>').show();
					opt.data('wraped', false);
				//} else {
					//shown.push( primId +'!=='+ opt.data('suppid') );
				}
			}
			//console.log('HIDDEN ' + hidden.join(' '));
			//console.log('SHOWN  ' + shown.join(' '));

		//jQuery( 'option', prim ).unwrap('<span>').show();
		//

		/*
		sec.find("option").each(function(){
			opt = jQuery(this);
			if (opt.data('suppid') === primId) { 
				//opt.show(); 
				opt.unwrap('<span>').show();
				//console.log('option: '+opt.val() + ' show');
			} else { 
				//opt.hide(); 
				opt.wrap('<span>').hide();
				opt.attr('selected','');
			}
		});
		*/
	},
	fetchRows: function(dataURL) {
		var tuples = [];
		jQuery.ajax({
			url: dataURL || this.options.dataUrl,
			async: false,
			dataType: 'json',
			success: function(data) { tuples = data; }
		});
		return tuples;
	}
				
};

//
// INIT WIDGETS
//
//

jQuery.widget('varelager.doubleselect', doubleselect);
jQuery.widget('varelager.jtable', jtable);
//jQuery.widget('varelager.mbpCategory', menuboxPickerCategory);
