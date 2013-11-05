//
// SPREADSHEET
//

var spreadsheet = {
	options: {
		products: null,
		enable_listitem_amount: 0
	},

	_create: function(){
		jQuery(this.element).html('');
		if (this.options.products !== null ){
			this.productRows = this.options.products;
			for (var i=0,len=this.productRows.length ;i< len; i++){
				p = this.productRows[i];
				for (var j=0; j< p.quantity.length; j++) {
					var dd = {
						department: p.quantity[j].department,
						depid: 		p.quantity[j].depid
					}
					this.addDepartment(dd);
				}
			}
			this.refresh();
		}
	},
	department_names: new Array(),
	departments: new Array(),
	productRows: new Array(), 	//[{ prodname => str, 
								//   prodid	  => int,
								//   quantity => [ {depid=>int, qty=>int}, ... ]
								// },  ...  ]

	getValue: {
		//prid: function(i){ return this[i].prid; },
		prid: function(i){ return this[i].prid; },
		product: function(i){ return this[i].product; },
		depid: function(i){ return this[i].depid; }
	},
	haveProd: function(prid){
		this.productRows.quickSort(this.getValue.prid);
		return -1 !== this.productRows.binarySearch(prid, this.getValue.prid);
	},
	haveQuantity: function(prid, depid){
		this.productRows.quickSort(this.getValue.prid);
		ix = this.productRows.binarySearch(prid, this.getValue.prid );
		if (ix !== -1) {
			this.productRows[ix].quantity.quickSort(this.getValue.depid);
			return -1 !== this.productRows[ix].quantity
								.binarySearch( depid, this.getValue.depid );
		}
		return false;
	},
	setQuantity: function(prodid, depid, depname, qty){
		pr = this.productRows;

		if (this.haveQuantity(prodid, depid)) {
			ixp = pr.binarySearch(prodid, this.getValue.prid);
			ixq = pr[ixp].quantity.binarySearch(depid, this.getValue.depid);
			pr[ixp].quantity[ixq].qty = qty;
		} else {
			//pr.quickSort(this.getValue.prid);
			ixp = pr.binarySearch(prodid, this.getValue.prid);
			pr[ixp].quantity.push({'depid': depid, 'department': depname, 'qty':qty});
			pr[ixp].quantity.quickSort(this.getValue.depid);
		}
	},
	//mergeRow: function(prodname, prodid, depid, depname, quantity){
	mergeRow: function(r){

		//This.mergeRow(r.product, r.prid, r.depid, r.department, r.qty);
		pr = this.productRows;
		if (! this.haveProd(r.prid)){
			//pr.push({ 'prid': prodid, 'product': prodname, 'quantity': [] });
			pr.push({ 
					'prid':		r.prid, 
					'product':	r.product, 
					'partnum':	r.partnum, 
					'category':	r.category,
					'supplier':	r.supplier, 
					'quantity':	[] 
			});
		}
		this.setQuantity(r.prid, r.depid, r.department, r.qty);
	},
	addDepartment: function(depAndDate) {
		if (this.department_names.binarySearch(depAndDate.department) == -1) {
			this.departments.push(depAndDate.depid);
			this.department_names.push(depAndDate.department);

			this.departments.quickSort();
			this.department_names.quickSort();
		}

	},
	addColumn: function(depAndDate) {
		This = this;
		jQuery.ajax({
			url:		jQuery.varelager.links.stockrecord,
			data:		{ 
				'depid':	depAndDate.depid, 
				'date':		depAndDate.date 
			},
			success: 	function(returnedData, textStatus, jqXHR){
				jQuery('div[class~=loading-date]').removeClass('loading-date');

				This.addDepartment(depAndDate);

				for (i=0, len=returnedData.length; i<len; i++) {
					This.mergeRow(returnedData[i]);
					//r = returnedData[i];
					//This.mergeRow(r.product, r.prid, r.depid, r.department, r.qty);
				}
				This.refresh();
			},
			error:		jQuery.varelager.ajaxError,
			dataType:	'json',
			type:		'POST'
		});
	},
	// used when date changes for a department
	removeColumn: function(depAndDate) {
		for (var i=0; i< this.productRows.length; i++) {
			pr = this.productRows[i];
			for (var q=0; q< pr.quantity.length; q++) {
				if (pr.quantity[q].depid === depAndDate.depid) {
					a = pr.quantity.splice(q,1);
				}
				if (pr.quantity.length === 0) {
					a = this.productRows.splice(i,1);
				}
			}
		}
	},
	sumQuantities: function() {
	/*

		jQuery(this.element).find('.spreadsheet-header-product').after(
			'<td></td><td style="font-size:0.8em;align:center;">Sum</td>');
	*/

		var tdNameElements = jQuery(this.element).find('td[class~="spreadsheet-product-name"]');
		tdNameElements.each(function() {
			var qty_sum = 0;
			jQuery(this).siblings('td[class~="spreadsheet-table-quantity"]').each(function(){

				qty_sum+= parseInt( jQuery(this).html(), 10);
			});


			//jQuery('div[class~="list-item"]', this).listitem('setTarget', qty_sum - mal_amount);
			jQuery('div[class~="list-item"]', this).listitem('setCount', qty_sum);



			/*
			jQuery(this).after(function() {
				amount	= jQuery('<td></td>');
				input 	= jQuery('<input></input>').css({'width':'20px','height':'0.87em'});
				sum		= jQuery('<td></td>').html(qty_sum);
				amount.html(input);
				return jQuery.merge( jQuery.merge([],amount), sum );
			});
			*/
		});
	},

	refresh: function(){
		jQuery(this.element).html('');
		this.productRows.quickSort(this.getValue.product);

		var table	= jQuery('<table></table>') .addClass('spreadsheet-table');
		var headers = jQuery('<tr></tr>');
		var varer 	= jQuery('<th>Varenavn</th>') .addClass('spreadsheet-header-product') .appendTo(headers);

		for (n=0; n< this.department_names.length; n++) {
			name = this.department_names[n];
			jQuery('<th></th>')
				.addClass('spreadsheet-header-department')
				.html(name.initials(''))
				.appendTo(headers);
		}

		headers.appendTo(table);

		for (i=0; i<this.productRows.length; i++) {
			row = this.productRows[i];
			var ssLine = jQuery('<tr></tr>');
			var ssProd = jQuery('<td></td>').addClass('spreadsheet-product-name');
			var item = jQuery('<div></div>').listitem({
				'prod_id':			row.prid,
				'prod_name':		row.product,
				'prod_category':	row.category,
				'prod_supplier':	row.supplier,
				'prod_partnum':		row.partnum,
				'show_amount':		this.options.enable_listitem_amount,
				'name_max_len':		55
			});
				ssProd.html(item);
				ssProd.appendTo(ssLine);

			if (i % 2) { ssProd.addClass('spreadsheet-table-item'); } 
			else { 		 ssProd.addClass('spreadsheet-table-item-alt'); }



			for (k=0; k<this.department_names.length; k++){
				ssQTY = jQuery('<td></td>').addClass('spreadsheet-table-quantity');

				if ((i+k) % 2) { /*ssQTY.addClass('spreadsheet-table-quantity');*/ true ; } 
				else { 		 ssQTY.addClass('spreadsheet-table-quantity-alt'); }
				depfound = -1;
				for (j=0; j< row.quantity.length; j++) {
					if (row.quantity[j].department === this.department_names[k]){
						depfound = j;
						j=row.quantity.length;
					}
				}
				if (depfound > -1) {
					ssQTY.html( row.quantity[depfound].qty );
				} else {
					ssQTY.html( 0 );
				}

				ssQTY.appendTo(ssLine);
			}
			ssLine.appendTo(table);
		}
		table.appendTo(this.element);
	},

	clear: function() {
		this.productRows = new Array();
		this.refresh();
	},


	getProducts: function(){
		return JSON.stringify( this.productRows );
	},
	toString: function(){
		var out = "";
		pr = this.productRows;
		for (i=0; i<pr.length; i++ ){
			r = pr[i];
			out += "prod({0}): {1}\n".format( r.prid, r.product);
			out += "\t";
			for (j=0; j< r.quantity.length; j++) {
				q = r.quantity[j];
				out += "{0}:{1}, ".format(q.depid, q.qty);
			}
			out += "\n";
		}
		return out;
	}
};

//
// SINGLE LIST ITEM
//

var list_item = {
	options: {
		count: 0,
		prod_id:  0,
		prod_name:  'product',
		prod_category: 'category',
		prod_supplier: 'supplier',
		prod_partnum:  0,
		show_amount: 1,
		afterRemove: null, // callback
		name_max_len: 45,
		tabindex: -1

	},

	_create: function() {
		var This = this,
			partnum = this.options.prod_partnum,
			pname	= this.options.prod_name,
			nameArr	= [],
			words	= pname.split(' '),
			line	= '',
			initlen = words.length,
			l = 0,
			w = 0,
			// elements
		prodpnSpan 	= jQuery('<span></span>'),
		nameSpan	= jQuery('<span></span>'),
		amount	= jQuery('<div></div>'),
		input 	= jQuery('<input></input>'),
		imgPlus = jQuery('<img>').attr('alt', '+') .attr('src', jQuery.varelager.links.buttons+'plus32.png'),
		imgMinus= jQuery('<img>').attr('alt', '\u2212') .attr('src', jQuery.varelager.links.buttons+'minus32.png'),
		btnMore	= jQuery('<a class="increment" href="#"></a>') .html(imgPlus).
							click(function(){ This.increment(); return false;}),
		btnLess	= jQuery('<a class="decrement" href="#"></a>') .html(imgMinus).
			click(function(){ This.decrement(); return false;}),

		endElements;


		//if ( partnum == "-1") { partnum = "0000000"; } else { partnum = this.options.prod_partnum; }
		partnum = partnum > -1? partnum : "0000000";

		while (initlen > -1) { 
			if ((line + words[0]).length < this.options.name_max_len) { 
				line += words.splice(0,1) + ' ';
				} else { 
					nameArr[l] = line;
					l++;
					line='';
				} 
			initlen--;
		} nameArr[l] = line; line='';

		//pname = nameArr.join('<br />');
			pname = nameArr[0];
		if (nameArr.length > 1 ) {
			pname = nameArr[0] + '...';
		} 


		/////

		prodpnSpan.addClass('list-item-prod-partnum') .html(partnum) .
				attr('title', this.options.prod_supplier +': '+ this.options.prod_category);
		nameSpan.addClass('list-item-prod-name') .html(pname); 

		this.element .addClass('list-item') .data('prod_id', this.options.prod_id) .
				append(prodpnSpan) .append(nameSpan);

		if (this.options.show_amount) {
			amount.addClass('list-item-amount');
			input.bind('keyup', function(event) { 
					This.setCount(event.target.value); }).
				addClass('amountinput').
				addClass('amount-input').
				attr('type', 'text').
				attr('tabindex', this.options.tabindex).
				val(this.options.count);
			amount.append(input).append(btnMore).append(btnLess);
			this.element.append(amount);
		}
	},
	increment: function(){ 
		this.options.count++;
		this._refresh();
	},
	decrement: function(){ 
		if (this.options.count > 0 ) {
			this.options.count--; 
		} else {
			jQuery( this.element ).remove();
			jQuery.varelager.resizeList();
			if (this.options.afterRemove){
				//console.log('list_item.decrement -> removeAfter :' + this.options.prod_id );
				this.options.afterRemove.call( this, this.options.prod_id );
			}
		}
		this._refresh();
	},
	getProdId: function(){
		return parseInt( this.options.prod_id ,10);
	},

	setCount: function(count){
		//console.log(this.options.prod_name + ' setting count: ' + count);
		number = parseInt( count, 10) || 0 ;
		this.options.count = number;
		this._refresh();
	},

	_refresh: function(){
		jQuery(this.element).find('input').val(this.options.count);
	}

};

//
// INIT WIDGETS
//

jQuery.widget('varelager.spreadsheet', spreadsheet);
jQuery.widget('varelager.listitem', list_item);
