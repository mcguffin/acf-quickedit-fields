import $ from 'jquery';

$('.acf-qef-gallery-col').on('mousemove',function(e){
	const $this	= $(this),
		$img	= $(this).find('img'),
		x		= e.offsetX,
		num		= $img.length,
		step	= $(this).width() / num;

	$img.each( function(i,el) {
		if (x >= step*i) {
			$(el).show();
		} else {
			$(el).hide();
		}
	} );
});
