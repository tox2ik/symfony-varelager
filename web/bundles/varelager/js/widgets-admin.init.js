jQuery(function() {


	//
	// ACTION PICKER LISTENER
	//
	
	jQuery('#varelagerAction').bind('change', function(e){

		var table = jQuery('#dbTableWidget');
		var selected = jQuery(':selected', e.currentTarget).attr('value');
		var vType=null;
		var vSkip=null;
		var vSheads=null;
		var vHeaders=null;
		var vFields=null;
		var vMboxes=null;
		var vMbtype=null;
		var vfSave=null;
		var vfAddnew=null;
		var vfDel=null;
		//console.log('varelagerAction: CHANGE > ' + selected);


		//
		// COLUMN DEFINITIONS
		//

		if (selected === 'Users') {
			vType	=['1','i','i','i','c','1','1','i','i','i','i','i','i','t'];
			vSkip	=[ 1 , 0 , 0 , 0 , 0 , 1 , 1 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ];
			vSheads	=[];
			// FIX department
			vHeaders=['id', 'Navn', 'Etternavn', 'Brukernavn', 'Akt.', '',  '',   
					'Gate', 'Postnr.', 'By', 'Land','Tlf.','E-mail','Kommentar' ];
			vFields	=['id', 'nameFirst', 'nameLast', 'username', 'active', 'address_id', 
					'addr_name', 'street', 'zipcode', 'city', 'country', 'phone', 'email', 'comment'];
			vfEmptyRow = { "id": 1, "username": "", "nameFirst": "", "nameLast": "", 
				"salt": "", "password": "", "active": true,  
				"address_id": 0, "addr_name": "", "street": "", "zipcode": 0, "city": "Trondheim",
				"country": "Norge", "phone": "", "email": "", "comment": ""
			};
			vfSave	= function(e, rowNr) {
				//kv = table.jtable('getInputs', e.currentTarget);
				var Table = this;
				kv = Table.getInputs( rowNr);
				if (rowNr >= 0) {
					// update user
					//kv['id'] = this.options.rows[rowNr]['id'];
					//kv['address_id'] = this.options.rows[rowNr]['address_id'];
					kv['id'] = Table.getDBIDofRow(rowNr);
				} else {
					// add new user
					kv['id'] = -1; 
					kv['address_id'] = -1;
				}

				console.log("sending user: ");
				console.log(kv);

				var successCB = (kv['id'] > 0)? function(){}
					:function(textStatus, data,  jqXHR) {  
						var str = data.responseText;
						lastId =  parseInt( str.substring(str.lastIndexOf('id:')+3), 10);
						if (isNumber(lastId)) {
							kv['id']= lastId;
						} else {
							kv['id']= -2;
						}
						Table.appendRow( kv ); 
					};
				jQuery.varelager.doAction('saveUsers', kv, successCB);
				//jQuery.varelager.doAction('saveUser', JSON.stringify(kv));
			};
			vfDel	= null;
			/*
			vfDel	=function(e, rowNr) {
				//kv = table.jtable('getInputs', e.currentTarget);
				kv = [];
				kv['id'] = this.rows[rowNr].id;
				kv['address_id'] = this.rows[rowNr].address.id;
				//kv.push({'employee_id': this.rows[rowNr].id });
				//kv.push({'address_id':  this.rows[rowNr].address.id });
				jQuery.varelager.doAction('deleteUsers', kv);
			};
			*/
			vDataUrl=jQuery.varelager.links.adminUsers;
		} else 
		if (selected === 'Permissions') {

			vType	=['1','id','id','id','c','1','sr','1'];
			vSkip	=[ 1 , 0  , 0  , 0  , 0 , 1 , 0  , 1 ];
			//vType	=['1','d','d','d','c','1','i'];
			//vSkip	=[ 1 , 0 , 0 , 0 , 0 , 1 , 0 ];
			vSheads	=['Rettigheter'];

			vHeaders=['id','Navn',      'Etternavn','Brukernavn', 'Akt.',   {'n':'roles', 'f': [ 'rid', 'Rolle', 'virtual_role']} ];
			vFields	=['id','nameFirst', 'nameLast', 'username',   'active', {'n':'roles', 'f': [ 'id',  'title', 'virtual_role']} ];

			//vHeaders=['id','Navn',      'Etternavn','Brukernavn', 'Akt.',  'rid', 'Rolle', 'virtual_role' ];
			//vFields	=['id','nameFirst', 'nameLast', 'username',   'active', 'role_id', 'title', 'virtual_role'];
			//vfEmptyRow = { "id": "-1", "username": "", "nameFirst": "", "nameLast": "", "salt": "", "password": "", "active": true,
    		//				"roles": [ { "id": 4, "title": "ROLE_USER", "virtual_role": true } ] };
			vfEmptyRow = null;
			/*
			vfSave	=function(e, rowNr) {
				Table = this;

				kv = table.jtable('getInputs', e.currentTarget);
				kv['id']= Table.options.rows[rowNr].id;
				//kvush({'address_id':  this.rows[rowNr].address.id });
				jQuery.varelager.doAction('savePermissions', kv);
			};
			vfDel	=function(e, rowNr) {
				kv = table.jtable('getInputs', e.currentTarget);
				kv.push({'employee_id': this.rows[rowNr].id });
				kv.push({'role_id':  this.rows[rowNr].address.id });
			};
			*/
			vDataUrl=jQuery.varelager.links.adminPermissions;

		} else 
		if (selected === 'Products') {
			vType=['1','i','i','i','1','dscp','1','1','c'];
			vSkip=[ 1 , 0 , 0 , 0 , 1 , 0    , 1 , 1  , 0 ];
			vSheads=null;
			vHeaders=['prid', 'Vare',    'Varenr.', 'Pris',   'catid', 'Kategori', 'suppid', 'Leverand\x00\xf8r',   'Ut'];
					//'Utl\x00\xf8pt'
			vFields= ['prid', 'product', 'partnum', 'price',  'catid', 'category', 'suppid', 'supplier',            'expired'];
			vfEmptyRow = { "prid": "", "product": "", "partnum": "", "price": "", "expired": "", "catid": "",
 							"category": "", "suppid": "", "supplier": "" };
			/*
			vMbtype={menubox:'mbpCategory'};
			vMboxes={
				catsupp: {
					dataUrl: jQuery.varelager.links.formSuppcat,
					menuWi:150,
					speedIn:333,
					speedOut:133,
					leafClick: function(table){
						var values = {};
						jQuery(this).siblings('input').each(function(){
							var input = $(this);
							values[input.attr('name')] = input.attr('value');
						});
						jQuery('#dbTableWidget').jtable('updateRow', values);
						return false;
					}
				}
			};
			*/
			/*
			vfSave	= function(e, rowNr) {
				kv = table.jtable('getInputs', e.currentTarget);
				kv['id'] = this.rows[rowNr].prid;
				console.log("save product ");
				console.log( kv);
				jQuery.varelager.doAction('saveProduct', kv);
			};
			*/
			/*
			vfDel	= function(event, rowNr) {
				//kv = table.jtable('getInputs', e.currentTarget);
				//kv['id'] = this.rows[rowNr].prid;
				//jQuery.varelager.doAction('deleteProduct', kv);
				jQuery.varelager.doAction('deleteProduct', this.rows[rowNr].prid );
			};
			*/
			vfAddnew=null;
			vDataUrl=jQuery.varelager.links.adminProducts;
		} else 
		if (selected === 'Departments') {
				vType=null; vSkip=null; vSheads=null; vHeaders=null; vFields=null; vfSave=null; vfAddnew=null; vfDel=null;
			vfEmptyRow = {};
				vDataUrl=jQuery.varelager.links.adminDepartments;
		} else 
		if (selected === 'Suppliers') {
				vType=['1','i','1','i','i','1','i','i','i','i','i','i','t','1','ti'];
				vSkip=[ 1 , 0 , 1 , 0 , 0 , 1 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 1 , 0  ];

				vSheads=null;
				vHeaders=['id', 'Leverand\x00\xf8r', 'account_id', 'Kontonr.', 'Kid', 'address_id', 'Gate', 
							'By', 'Postnr.', 'Land', 'Tlf.', 'E-mail', 'Kommentar', 'catids', 'Kategorier' ];
				vFields=['id', 'name', 'account_id', 'account_number', 'default_cid', 'address_id', 'street',
    						'city', 'zipcode', 'country', 'phone', 'email', 'addr_comment', 'catids', 'catnames'];
				vfEmptyRow={'id':'', 'name':'<navn>', 'account_id':'', 
							'account_number':'[konto nr.]', 'default_cid':'[kid]', 'address_id':'', 'street':'[gatenavn]',
    						'city':'[by]', 'zipcode':'[post nr.]', 'country':'[land]', 'phone':'[tlf.]', 'email':'[e-mail]', 
							'addr_comment':'[kommentar]', 'catids':'', 'catnames':''};
				vfSave= function(event, rowNr) {
					var pc = jQuery.varelager.productcategories;
					var Table = this;
					console.log("checking pc ");

					if (jQuery.varelager.productcategories === null || 
							typeof jQuery.varelager.productcategories === 'undefined' ||
							count( jQuery.varelager.productcategories) === 0) {
						jQuery.varelager.productcategories = 
						this.fetchRows(jQuery.varelager.links.formSuppcat, 'sync');
						pc = jQuery.varelager.productcategories;
					}
					var kv = this.getInputs( rowNr);
					kv['id'] = this.getDBIDofRow( rowNr);
					kv['catnames_new'] = null;

					var action2do = 'save'+this.options.pickedAction;
					var newSupplierCategory = true;
					var newCats = kv['catnames'].split(',');
					var catnames = newCats.concat();
					var len = count(pc);

					var successCB = (kv['id'] > 0)? function(){}
						:function(textStatus, data,  jqXHR) {  
							var str = data.responseText;
							lastId =  parseInt( str.substring(str.lastIndexOf('id:')+3), 10);
							if (isNumber(lastId)) {
								kv['id']= lastId;
							} else {
								kv['id']= -2;
							}
							jQuery.varelager.productcategories = [];
							Table.appendRow( kv ); 
						};

					for (var i=0; i < len; i++) {
						if (pc[i]['suppid'] == kv['id'] ) {
							for (var j=0; j< catnames.length ; j++) {
								var cat = catnames[j].trim().toUpperCase();
								if ( pc[i]['category'].trim() == cat ) {
									newCats.splice( newCats.indexOf(cat),1);
								}
							}
						}
					}
					if (newCats.length > 0 && ! (newCats.length === 1 && newCats[0] === '') )  {
						console.log("newCats: " + newCats.length +" elements:>"+ newCats +"<");
						kv['catnames_new'] = newCats;
						jQuery('<div></div>').
							html(
"Vennligst bekreft oprettelse av nye kategorier for "+ kv['name']+ ": <br/>"+
	newCats.toString().replace(',',', ','g') ).
							dialog({ 
								buttons: [
									{ text: 'Greit', click: function() {
										kv['create_new_categories']=true;
										$(this).dialog("close");
										jQuery.varelager.doAction( action2do, kv, successCB);
									}},{ text: 'Nei', click: function(){ 
										kv['create_new_categories']=false;
										$(this).dialog("close");
										jQuery.varelager.doAction( action2do, kv, successCB);
									}}
								],
								modal: true,
								title: "<b>Nye Kategorier</b>",
								closeOnEscape: true,
								dialogClass: "alert",
								hide: { effect: 'drop', direction: 'down' },
								show: { effect: 'drop', direction: "up" },
								modal: true, // disables everyting else
								closeText: 'lukk'
							});
					} else {
						jQuery.varelager.doAction( action2do, kv, successCB);

					}
					console.log( kv)

				};
				vfDel=null;
				vDataUrl=jQuery.varelager.links.adminSuppliers;
		} else 
		if (selected === 'Orders') {
				vType=null; vSkip=null; vSheads=null; vHeaders=null; vFields=null; vfSave=null; vfAddnew=null; vfDel=null;
			vfEmptyRow = {};
				vDataUrl=jQuery.varelager.links.adminOrders;
		}

		table.jtable({
			pickedAction: selected,
			type:		vType,
			skip: 		vSkip,
			sheads: 	vSheads,
			headers: 	vHeaders, 
			fields: 	vFields,
			save: 		vfSave,
			addnew:		vfAddnew,
			del: 		vfDel,
			mboxes:		vMboxes,
			mbtypes:	vMbtype,
			dataUrl:	vDataUrl,
			rows:		[],
			emptyRow:	vfEmptyRow
		});
		table.jtable('refresh');
	}); // change() definition

	jQuery('#varelagerAction').change(); // trigger a refresh

});
