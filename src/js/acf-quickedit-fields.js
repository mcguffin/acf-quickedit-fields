(function( $, qe ){
	var file_type = {
		type:'file',
		mediaFrameType:'',
		events:{
			'click .select-media' : 'selectFile',
			'click .remove-media' : 'removeFile',
		},
		initialize:function() {

			qe.field.View.prototype.initialize.apply(this,arguments);

			var self = this,
				$hidden = this.$('[type="hidden"]'),
				post_id = acf.get('post_id');

			this.mediaFrameOpts = {
				field		: this.key,
				multiple	: false,
				post_id		: post_id,
				library		: $hidden.attr('data-library'),
				mode		:'select',
				type		: this.mediaFrameType,
				select		: function ( attachment, i ) {
					if ( ! attachment ) {
						return;
					}
					self.setValue( attachment.get('id') );
				}
			};
			if ( $hidden.data('mime_types') ) {
				this.mediaFrameOpts.mime_types = $hidden.data('mime_types');
			}

			// this.$('.select-media').on('click',function(e){
			// 	e.preventDefault();
			// 	self.selectFile();
			// 	console.log(this);
			// });
//			console.log(this.$('.select-media'))
		},
		selectFile:function(e){
			e.preventDefault();
			// Create a new media frame
			var media_frame = acf.media.popup( this.mediaFrameOpts );

			// set post id, so new uploads are attached to edited post
			if ( acf.isset(window,'wp','media','view','settings','post') && $.isNumeric( this.mediaFrameOpts.post_id ) ) {

				wp.media.view.settings.post.id = this.mediaFrameOpts.post_id;

			}

		},
		removeFile:function(e){
			e.preventDefault();
			this.setValue('');
		},
		setValue:function(value) {
			this.$('[type="hidden"]').val(value);
			return this;
		}
	};

	/**
	 *	field type file
	 */
	qe.field.add_type( file_type );

	/**
	 *	field type image
	 */
	qe.field.add_type( _.extend(file_type,{type:'image',mediaFrameType:'image'}) );

	qe.field.add_type( file_type );

	/**
	 *	field type range
	 */
	qe.field.add_type( {
		 type:'range',
		 events:{
			 'change [type="range"]'		: 'adaptNumber',
			 'mousemove [type="range"]'		: 'adaptNumber',
			 'change [type="number"]'		: 'adaptRange',
			 'mousemove [type="number"]'	: 'adaptRange',
		 },
		 adaptNumber:function(){
			 this.$('[type="number"]').val( this.$('[type="range"]').val() );
		 },
		 adaptRange:function(){
			 this.$('[type="range"]').val( this.$('[type="number"]').val() );
		 }
	 } );


	/**
  	 *	field type date_picker
  	 */
  	qe.field.add_type( {
 		type:'date_picker',
		initialize:function() {
			var self = this;
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
			this.$input		= this.$( '[type="text"]' );
			this.$hidden	= this.$( '[type="hidden"]' );
			this.datePickerArgs = {
					dateFormat		: this.$('[data-date_format]').data('date_format'),
					altFormat		: 'yymmdd',
					altField		: this.$hidden,
					changeYear		: true,
					yearRange		: "-100:+100",
					changeMonth		: true,
					showButtonPanel	: true,
					firstDay		: this.$('[data-first_day]').data('first_day')
				};
			this.$input.datepicker( this.datePickerArgs ).on('blur',function(){
				if ( ! $(this).val() ) {
					self.$hidden.val('');
				}
			});
			return this;
		},
		setValue:function(value) {
			this.$input.prop('readonly',false);
			this.$input.datepicker( 'setDate', $.datepicker.parseDate( this.datePickerArgs.altFormat, value ) );
			return this;
		}
 	});

	/**
 	 *	field type date_time_picker
 	 */
 	qe.field.add_type( {
		type:'date_time_picker',
		initialize:function() {
			var self = this;
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
			this.$input		= this.$( '[type="text"]' );
			this.$hidden	= this.$( '[type="hidden"]' );
			this.datePickerArgs = {
					altField			: this.$hidden,
					dateFormat			: this.$('[data-date_format]').data('date_format'),
					altFormat			: 'yy-mm-dd',
					timeFormat			: this.$('[data-time_format]').data('time_format'),
					altTimeFormat		: 'HH:mm:ss',
					altFieldTimeOnly	: false,
					changeYear			: true,
					yearRange			: "-100:+100",
					changeMonth			: true,
					showButtonPanel		: true,
					firstDay			: this.$('[data-first_day]').data('first_day'),
					controlType			: 'select',
					oneLine				: true
				};

			this.$input.datetimepicker( this.datePickerArgs ).on('blur',function(){
				if ( ! $(this).val() ) {
					self.$hidden.val('');
				}
			});
			return this;
		},
		setValue:function(value) {
			console.log(value,this.datePickerArgs.altFormat,this.datePickerArgs.altTimeFormat,typeof value);
			var date = $.datepicker.parseDateTime(
				this.datePickerArgs.altFormat,
				this.datePickerArgs.altTimeFormat,
				value
			);
			this.$input.prop('readonly',false);
			//*
			this.$hidden.val(date);
			this.$input.val( $.datepicker.formatDate(this.datePickerArgs.dateFormat, date) +' ' + $.datepicker.formatTime(
				this.datePickerArgs.timeFormat,
				date
			));
			/*/
			// will set form dirty if done like this
			this.$input.datepicker( 'setDate', date );
			//*/
			return this;
		}
	});

	/**
 	 *	field type time_picker
 	 */
 	qe.field.add_type( {
		type:'time_picker',
		initialize:function() {
			var self = this;
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
			this.$input		= this.$( '[type="text"]' );
			this.$hidden	= this.$( '[type="hidden"]' );
			this.datePickerArgs = {
 					timeFormat			: this.$('[data-time_format]').data('time_format'),
 					altTimeFormat		: 'HH:mm:ss',
 					altField			: this.$hidden,
 					altFieldTimeOnly	: false,
 					showButtonPanel		: true,
 					controlType			: 'select',
 					oneLine				: true
 				};

 			this.$input.timepicker( this.datePickerArgs ).on('blur',function(){
 				if ( ! $(this).val() ) {
 					self.$hidden.val('');
 				}
 			});
			return this;
		},
 		setValue:function(value) {
			var time = $.datepicker.parseTime( this.datePickerArgs.altTimeFormat, value );
			this.$input.prop('readonly',false);
			this.$hidden.val(value);
			this.$input.val( $.datepicker.formatTime( this.datePickerArgs.timeFormat, time ) )
			return this;
 		}
	});

	/**
 	 *	field type time_picker
 	 */
 	qe.field.add_type( {
		type:'color_picker',
		initialize:function() {
			qe.field.View.prototype.initialize.apply(this,arguments);
			this.$input = this.$('[type="text"]').first().wpColorPicker();
		},
		setValue:function( value ) {
			this.$input.prop('readonly',false);
			this.$input.wpColorPicker( 'color', value );
		},
		unload:function() {
			$( 'body' ).off( 'click.wpcolorpicker' );
		}
	});



	/**
 	 *	field type time_picker
 	 */
 	qe.field.add_type( {
		type:'textarea',
		initialize:function() {
			qe.field.View.prototype.initialize.apply(this,arguments);

			this.$input = this.$('textarea').prop( 'readonly', true );

			this.$input.on('keydown keyup', function(e) {
				if ( e.which == 13 || e.which == 27 ) {
					e.stopPropagation();
//					e.preventDefault();
				}
			});
		},
		setValue:function( value ) {
			this.$input.prop( 'readonly', false ).val(value);
		}
	});

	/**
 	 *	field type choice
 	 */
	// Todo

	/**
 	 *	field type checkbox
 	 */
	// Todo
	qe.field.add_type( {
		type:'checkbox',
		events:{
			'click .add-choice': 'addChoice',
			'change [type="checkbox"].custom' : 'removeChoice'
		},
		initialize:function() {
			qe.field.View.prototype.initialize.apply(this,arguments);

			this.$input = this.$('[type="checkbox"]').prop( 'readonly', true );
		},
		setValue:function( value ) {
			var self = this;
			this.$input.prop( 'readonly', false ).val(value);
			if ( $.isArray(value) ) {
				$.each( value, function( idx, val ) {
					self.$( '[type="checkbox"][value="'+val+'"]' )
						.prop( 'checked',true);
				});
			} else {
				this.$( '[type="checkbox"][value="'+value+'"]' )
					.prop( 'checked',true);
			}
 		},
		addChoice:function(e){
			e.preventDefault();
			var tpl = wp.template('acf-qef-custom-choice-' + this.$el.attr('data-key'));
			this.$('ul').append(tpl());
		},
		removeChoice:function(e) {
			$(e.target).closest('li').remove();
		}

	});

	/**
 	 *	field type radio
 	 */
	qe.field.add_type( {
		type:'radio',
		initialize:function() {
			var $other, is_other;
			qe.field.View.prototype.initialize.apply(this,arguments);

			this.$('[type="radio"]').prop( 'readonly', true );
			console.log(this.$('ul.acf-radio-list.other').length);
			if ( this.$('ul.acf-radio-list.other').length ) {
				$other = this.$('[type="text"]');
				this.$('[type="radio"]').on('change',function(e){
					//console.log(this,$(this).is(':checked'))
					is_other = $(this).is('[value="other"]:checked');
					$other
						.prop('disabled', ! is_other )
						.prop('readonly', ! is_other );

				})
			}
		},
		setValue:function( value ) {
			this.$('[type="radio"]').prop( 'readonly', false );
			this.$('[type="radio"][value="'+value+'"]' )
				.prop( 'checked', true );
 		}
	});

	/**
 	 *	field type select
 	 */
	qe.field.add_type( {
		type:'select',
		initialize:function() {
			qe.field.View.prototype.initialize.apply(this,arguments);

			this.$input = this.$('select').prop( 'readonly', true );
		},
		setValue:function( value ) {
 			this.$input.prop( 'readonly', false ).val(value);
 		}
	});

	/**
 	 *	field type true_false
 	 */
	 qe.field.add_type( {
 		type:'true_false',
 		initialize:function() {
 			qe.field.View.prototype.initialize.apply(this,arguments);

 			this.$('[type="radio"]').prop( 'readonly', true );
 		},
 		setValue:function( value ) {
			this.$('[type="radio"]').prop( 'readonly', false );
			this.$('[type="radio"][value="'+value+'"]' )
				.prop( 'checked', true );
  		}
 	});
	// Todo

})( jQuery, acf_quickedit );


/*
range
select
checkbox
radio
true_false
image
file
date_picker
date_time_picker
time_picker
color_picker

*/
