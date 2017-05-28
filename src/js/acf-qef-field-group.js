(function($){

	acf.add_action('add_field',function( $el ) {

		// remove quickedit options on repeater/flexible_contetn sub fields

		if ( $el.closest('[data-type="repeater"],[data-type="flexible_content"]').length ) {

			$el.find('tr[data-name="show_column"],tr[data-name="show_column_weight"],tr[data-name="allow_quickedit"],tr[data-name="allow_bulkedit"]').remove();

		}
	});


})(jQuery);