(function($){
$.fn.extend({
flatSelectStyle: function(options) {
	if (!$.browser.msie || ($.browser.msie && $.browser.version > 6)) {
		return this.each(function() {
			var doublespan = 
				$('<span></span>').addClass('flatSelectBox').html(
					$('<span></span>').addClass('flatSelectBoxInner').html(
						$(this).find(':selected').text()	));

			$(this).after( doublespan).css({
				position:	'absolute', 
				opacity:	0, 
				fontSize:	$(this).next().css('font-size')
			});


			var selectBoxSpan = $(this).next();
			var selectBoxSpanInner = selectBoxSpan.find(':first-child');

			var selectBoxWidth = parseInt(
				$(this).width()) 
				- parseInt(selectBoxSpan.css('padding-left')) 
				- parseInt(selectBoxSpan.css('padding-right'));            

			var selectBoxHeight = parseInt(
				selectBoxSpan.height()) 
				+ parseInt(selectBoxSpan.css('padding-top')) 
				+ parseInt(selectBoxSpan.css('padding-bottom'));

			selectBoxSpan
				.css('display','inline-block');
			selectBoxSpanInner
				.css('width',selectBoxWidth)
				.css('display','inline-block');

				//$(this).find(':selected').text();
			$(this).height(selectBoxHeight);
			$(this).bind('change', function() {
				selectBoxSpanInner.text( $(this).find(':selected').text() ) 
					.parent().addClass('changed');
					
			});
	 });
	}
}
});
})(jQuery);
