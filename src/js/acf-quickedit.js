var acfQuickedit = {};

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
		$.post( ajaxurl, req_data, function( result ) {
			var i, key, value, $tr;

			var keys = [];

			$parent.find('[data-acf-field-key]').each(function() {
				!$tr && ($tr = $(this).closest('tr.inline-edit-post'));
				var key = $(this).data('acf-field-key');
				if ( keys.indexOf( key ) === -1 ) {
					keys.push(key);
				}
			});

			for ( i=0; i<keys.length; i++) {

				key = keys[i], value = result[ key ];

				var $selected;

				// convert bool to int
				if (typeof(value) === 'boolean') {
					value *= 1;
				}

				// remove readonly prop
				$('input[data-acf-field-key="'+key+'"],textarea[data-acf-field-key="'+key+'"]')
					.prop('readonly',false);


				// set text field values
				$('input[type!="radio"][type!="checkbox"][data-acf-field-key="'+key+'"],textarea[data-acf-field-key="'+key+'"]')
					.val(value);

				// set val for radio buttons
				$selected = $('.acf-radio-list[data-acf-field-key="'+key+'"],.acf-checkbox-list[data-acf-field-key="'+key+'"]')
					.find('[value="'+value+'"]')
					.prop( 'checked',true);

				if ( ! $selected.length ) {
					if ( !! value )
						$('.acf-radio-list.other[data-acf-field-key="'+key+'"]').find('[value="other"]').prop( 'checked', true );
					else 
						$('.acf-radio-list.other[data-acf-field-key="'+key+'"]').find('[type="text"]').val('');
				}
				
			}

			$parent.find('input.acf-quick-edit-color_picker').each( function( i, el ) {
				$(el).wpColorPicker();
			})
			
			// init datepicker
			$parent.find('.acf-quick-edit-date_picker').each( function( i, el ) {

				acfQuickedit.datepicker.init( $(el) );

			});

			// init timepicker
			$parent.find('.acf-quick-edit-time_picker').each( function( i, el ) {

				acfQuickedit.timepicker.init( $(el) );

			});

			// init datetimpicker
			$parent.find('.acf-quick-edit-date_time_picker').each( function( i, el ) {

				acfQuickedit.datetimepicker.init( $(el) );

			});


			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
		});
	}
	
	acfQuickedit.datepicker = {
		init: function( $wrap ) {
			var $hidden		= $wrap.find( '[type="hidden"]' ),
				$input		= $wrap.find( '[type="text"]' ),
				altFormat	= 'yymmdd'
				args		= {
					dateFormat		: $wrap.data('date_format'),
					altFormat		: altFormat,
					altField		: $hidden,
					changeYear		: true,
					yearRange		: "-100:+100",
					changeMonth		: true,
					showButtonPanel	: true,
					firstDay		: $wrap.data('first_day')
				},
				date		= $.datepicker.parseDate( altFormat, $hidden.val()  );

			$input.datepicker( args ).datepicker( 'setDate', date ).on('blur',function(){
				if ( ! $(this).val() ) {
					$hidden.val('');
				}
			});
		},
	
	};

	acfQuickedit.timepicker = {
		init: function( $wrap ) {
			var $hidden			= $wrap.find( '[type="hidden"]' ),
				$input			= $wrap.find( '[type="text"]' ),
				altTimeFormat	= 'HH:mm:ss',
				args			= {
					timeFormat			: $wrap.data('time_format'),
					altTimeFormat		: altTimeFormat,
					altField			: $hidden,
					altFieldTimeOnly	: false,
					showButtonPanel		: true,
					controlType			: 'select',
					oneLine				: true
				},
				time 			= $.datepicker.parseTime( altTimeFormat, $hidden.val() );

			$input.timepicker( args ).on('blur',function(){
				if ( ! $(this).val() ) {
					$hidden.val('');
				}
			});
			if ( $hidden.val() ) {
				$input.val( $.datepicker.formatTime( $wrap.data('time_format'), time ) )
			}
		},
	
	};

	
	acfQuickedit.datetimepicker = {
		init: function( $wrap ) {
			var $hidden			= $wrap.find( '[type="hidden"]' ),
				$input			= $wrap.find( '[type="text"]' ),
				altFormat		= 'yy-mm-dd'
				altTimeFormat	= 'HH:mm:ss',
				args			= {
					altField			: $hidden,
					dateFormat			: $wrap.data('date_format'),
					altFormat			: altFormat,
					timeFormat			: $wrap.data('time_format'),
					altTimeFormat		: altTimeFormat,
					altFieldTimeOnly	: false,
					changeYear			: true,
					yearRange			: "-100:+100",
					changeMonth			: true,
					showButtonPanel		: true,
					firstDay			: $wrap.data('first_day'),
					controlType			: 'select',
					oneLine				: true
				},
				datetime 			= $.datepicker.parseDateTime( altFormat, altTimeFormat, $hidden.val() );

			$input.datetimepicker( args ).datepicker( 'setDate', datetime ).on('blur',function(){
				if ( ! $(this).val() ) {
					$hidden.val('');
				}
			});
		},
	
	};

	
	// we create a copy of the WP inline edit post function
	var _wp_inline_edit = inlineEditPost.edit;
	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {
		var post_id;
		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		_wp_inline_edit.apply( this, arguments );

		// get the post ID
		post_id = 0;
		if ( typeof( id ) === 'object' ) {
			post_id = parseInt( this.getId( id ) );
		}
		get_acf_post_data( post_id , $('#edit-' + post_id )	 );
	};
	
	$(document).on( 'click' , '.bulkactions .button.action' , function( e ) {
		var post_ids = [];
// 		var req_data = {
// 			'action' : 'get_acf_post_meta',
// 			'post_id' : false,
// 			'post_ids' : [],
// 			'acf_field_keys' : []
// 		}
		$( '#bulk-edit #bulk-titles' ).children().each( function() {
			post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});
		get_acf_post_data( post_ids , $( '#bulk-edit' ) );
	}).on('change', '.acf-radio-list.other input[type="radio"]', function(e) {
		var $this = $(this), $list = $this.closest('.acf-radio-list'), 
			$other = $list.find('[type="text"]').prop('disabled',$this.val() != 'other');
	});
	

})(jQuery);