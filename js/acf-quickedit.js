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
			var i,key,value,$tr;
			var keys = [];
			$parent.find('[data-acf-field-key]').each(function() {
				!$tr && ($tr = $(this).closest('tr.inline-edit-post'));
				var key = $(this).data('acf-field-key');
				keys.push(key);
			});
			for (i=0;i<keys.length;i++) {
				key=keys[i],value=result[key];
				var $selected;
				if (typeof(value) === 'boolean') {
					value*=1;
				}

				// remove readonly prop
				$('input[data-acf-field-key="'+key+'"],textarea[data-acf-field-key="'+key+'"]')
					.prop('readonly',false);


				// set text field values
				$('input[type!="radio"][data-acf-field-key="'+key+'"],textarea[data-acf-field-key="'+key+'"]')
					.val(value);
				
				// set val for radio buttons
				$selected = $('.acf-radio-list[data-acf-field-key="'+key+'"]')
					.find('[value="'+value+'"]')
					.prop( 'checked',true);
				if ( ! $selected.length ) {
					if ( !! value )
						$('.acf-radio-list.other[data-acf-field-key="'+key+'"]').find('[value="other"]').prop( 'checked', true );
					else 
						$('.acf-radio-list.other[data-acf-field-key="'+key+'"]').find('[type="text"]').val('');
				}
				
				
			}
			$tr.find('input.acf-quick-edit-color_picker').wpColorPicker();
			
			// init datepicker
			$tr.find('input.acf-quick-edit-date_picker').each(function(i,el){
				var args = {
					dateFormat		:	'yymmdd',
					altFormat		:	'yymmdd',
					changeYear		:	true,
					yearRange		:	"-100:+100",
					changeMonth		:	true,
					showButtonPanel	:	true,
					firstDay		:	$(this).data('first_day')
				};
				console.log($(this).next('input'));
				$(this).datepicker(args);
			});
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
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
	}).on('change', '.acf-radio-list.other input[type="radio"]', function(e) {
		var $this = $(this), $list = $this.closest('.acf-radio-list'), 
			$other = $list.find('[type="text"]').prop('disabled',$this.val() != 'other');
	});
	

})(jQuery);