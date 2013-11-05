jQuery(function() {


//
// PLACE AND DATE PICKER
//
//

var placedate = {
	options: {
		fetchURL: jQuery.varelager.links.placedates
	},
	places: [],

	fetch: function(){
		var placesdates = [];
		jQuery.ajax({
			url: this.options.fetchURL,
			async: false,
			dataType: 'json',
			success: function(data) { placesdates = data; }
		});
		return placesdates;
	},

	_create: function() {

		this.places = this.fetch();

		var datesBox = jQuery('#datesBox');
		jQuery(this.element).selectable({
			selected: function(event, ui) {
				datesBox.html('');
				var depDates = jQuery(ui.selected).data('departmentDates');

				for (d=0; d< depDates.dates.length; d++){
					depAndDate = { 
						'department': depDates.department, 
						'depid': depDates.depid, 
						'date': depDates.dates[d] 
					};
					date= jQuery('<div></div>')
						.data('depAndDate', depAndDate)
						.addClass('ui-widget-content')
						.html(depDates.dates[d])
						.appendTo(datesBox);
				}
			}
		});

		datesBox.selectable({
			selected: function(event, ui) {
				jQuery(ui.selected).addClass('loading-date');

				jQuery('#spreadSheetWidget')
					.spreadsheet( 'addColumn', 
					jQuery(ui.selected).data('depAndDate'));
			},  
			unselected: function(event, ui) {
				jQuery('#spreadSheetWidget')
					.spreadsheet( 'removeColumn', 
					jQuery(ui.unselected).data('depAndDate'));
			}
		});


		for (i=0; i< this.places.length; i++) {
			place = jQuery('<div></div>')
				.addClass('ui-widget-content')
				.data('departmentDates', this.places[i])
				.html(this.places[i]['department'])
				.appendTo(this.element);
		}

	}
};


//
// INIT WIDGETS
//

jQuery.widget('varelager.placedate', placedate);


});
