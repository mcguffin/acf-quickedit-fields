(function($) {
	
	function get_acf_post_data( post_id , $parent ) {
		var req_data = {
			'action' : 'get_acf_post_meta',
			'post_id' : post_id,
			'acf_field_keys' : []
		};
		$parent.find('[data-acf-field-key]').each(function(){
			req_data.acf_field_keys.push( $(this).data('acf-field-key') );
			$(this).prop('readonly',true);
		});
		
		$.post(ajaxurl,req_data,function(result){
			var i;
			$parent.find('[data-acf-field-key]').each(function(i,elem){
				var key = $(this).data('acf-field-key');
				try {
					$(this).prop('readonly',false);
					switch ( $(this).attr('type') ) {
						case 'radio':
							$(this).prop( 'checked', result[ key ] == $(this).val() );
							break;
						default:
							$(this).val( result[ key ] );
							break;
					}
				} catch(err){
					console.log(err);
				}
			})
		});
	}
	
	// we create a copy of the WP inline edit post function
	var _wp_inline_edit = inlineEditPost.edit;
	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {
		var post_id, req_data;
		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		_wp_inline_edit.apply( this, arguments );

		// get the post ID
		post_id = 0;
		if ( typeof( id ) == 'object' ) {
			post_id = parseInt( this.getId( id ) );
		}
		get_acf_post_data( post_id , $('.inline-edit-row')	 );
	};
	
	$(document).on( 'click' , '.bulkactions .button.action' , function( e ) {
		var post_ids = [];
		var req_data = {
			'action' : 'get_acf_post_meta',
			'post_id' : false,
			'post_ids' : [],
			'acf_field_keys' : []
		}
		$( '#bulk-edit #bulk-titles' ).children().each( function() {
			post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});
		get_acf_post_data( post_ids , $( '#bulk-edit' ) );
	});
	

})(jQuery);