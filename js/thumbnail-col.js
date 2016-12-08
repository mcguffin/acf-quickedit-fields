(function($) {
	$('.acf-qef-gallery-col').on('mousemove',function(e){
		var $this	= $(this),
			$img	= $(this).find('img')
			x		= e.offsetX,
			num		= $img.length,
			step	= $(this).width() / num;

		$img.each( function(i,el) {
			if (i <= Math.round(x/step)) {
				$(el).show();
			} else {
				$(el).hide();
			}
		} );
	});
})(jQuery);