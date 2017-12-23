(function($){

	acf.add_action('add_field',function( $el ) {

		// remove quickedit options on repeater/flexible_content sub fields

		if ( $el.closest('[data-type="repeater"],[data-type="flexible_content"]').length ) {

			$el.find('tr[data-name="show_column"],tr[data-name="show_column_weight"],tr[data-name="allow_quickedit"],tr[data-name="allow_bulkedit"]').remove();

		}
	});


	/**
	 *	Disable sortable checkbox if column is not visible
	 */
	function set_sortable_disabled( i, show_col_inp ) {

		var checked = $(show_col_inp).prop('checked'),
			$parent = $(show_col_inp).closest('td.acf-input');

		$parent.find('[data-name="show_column_sortable"] [type="checkbox"]').prop('disabled',!checked);
		$parent.find('[data-name="show_column_weight"] [type="number"]').prop('readonly',!checked);

	}

	$(document)
		.on('change','[data-name="show_column"] [type="checkbox"]',function(e){
			set_sortable_disabled( 0, e.target );
		})
		.ready(function(){
			$('[data-name="show_column"] [type="checkbox"]').each(set_sortable_disabled);
		});


})(jQuery);
