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

			$(this).prop( 'readonly', true );

			if ( ! $(this).is('[data-is-do-not-change="true"]' ) ) {
				if ( $(this).is('[type="radio"],[type="checkbox"]') ) {
					$(this).prop('checked',false);
				} else {
					$(this).val('');
				}
			}
		});

		$.post( ajaxurl, req_data, function( result ) {
			var i, key, value, $tr, $field,
				selected;

			var keys = [];

			// only keep keys that have fields
			$parent.find('[data-acf-field-key]').each(function() {
				var key;
				!$tr && ($tr = $(this).closest('tr.inline-edit-post'));
				key = $(this).data('acf-field-key');
				if ( keys.indexOf( key ) === -1 ) {
					keys.push(key);
				}
			});

			for ( i=0; i<keys.length; i++) {

				key = keys[i], value = result[ key ];

				$field = $('[data-acf-field-key="'+key+'"]');

				// convert bool to int
				if (typeof(value) === 'boolean') {
					value *= 1;
				}

				
				if ( $field.is( '.acf-checkbox-list, .acf-radio-list' ) ) {
					selected = 0;
					if ( $.isArray( value ) ) {
						$.each( value, function( idx, val ) {
							selected += $field.find( '[value="'+val+'"]' ).prop( 'checked',true).length;
						}); 
					} else {
						selected += $field.find( '[value="'+value+'"]' ).prop( 'checked',true).length;
					}
					if ( $field.is('.other') && ! selected ) {
						if ( !! value ) {
							$field.find('[value="other"]').prop( 'checked', true );
						} else {
							$field.find('[type="text"]').val('');
						}
					}
				
				} else if ( ! $field.is( '[type="radio"],[type="checkbox"]' ) ) {
					$field.val( value );
				}
				$field.prop( 'readonly', false );

			}

			// init colorpicker
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

	
	if ( 'undefined' !== typeof inlineEditPost ) {
		// we create a copy of the WP inline edit post function
		var _wp_inline_edit_post = inlineEditPost.edit;
		// and then we overwrite the function with our own code
		inlineEditPost.edit = function( id ) {
			var post_id;
			// "call" the original WP edit function
			// we don't want to leave WordPress hanging
			_wp_inline_edit_post.apply( this, arguments );

			// get the post ID
			post_id = 0;
			if ( typeof( id ) === 'object' ) {
				post_id = parseInt( this.getId( id ) );
			}
			get_acf_post_data( post_id , $('#edit-' + post_id )	 );
		};
	}

	if ( 'undefined' !== typeof inlineEditTax ) {
		// we create a copy of the WP inline edit post function
		var _wp_inline_edit_tax = inlineEditTax.edit;
		// and then we overwrite the function with our own code
		inlineEditTax.edit = function( id ) {
			var object_id,
				tax = $('input[name="taxonomy"]').val();
			// "call" the original WP edit function
			// we don't want to leave WordPress hanging
			_wp_inline_edit_tax.apply( this, arguments );

			// get the post ID
			object_id = 0;
			if ( typeof( id ) === 'object' ) {
				object_id = parseInt( this.getId( id ) );
			}
			console.log(object_id );
			
			get_acf_post_data( tax + '_' + object_id , $('#edit-' + object_id )	 );
		};
	}

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

	})
	.on('change', '.acf-radio-list.other input[type="radio"]', function(e) {

		var $this = $(this), 
			$list = $this.closest('.acf-radio-list'), 
			is_other = $this.val() == 'other',
			$other = $list.find('[type="text"]').prop('disabled', ! is_other );

		!! is_other && $other.focus();

	})
	.on( 'change', '[data-is-do-not-change="true"]', function(){
		var $self = $(this),
			$list = $self.closest('.acf-checkbox-list'),
			$items = $list.find('[type="checkbox"]:not([data-is-do-not-change])');
		$items.prop( 'disabled', $self.prop('checked') );
	});

})(jQuery);