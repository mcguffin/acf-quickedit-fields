(function( $, qe ){
	var file_type = {
		type:'file',
		mediaFrameType:'',
		events:{
			'click .select-media' : 'selectFile',
			'click .remove-media' : 'removeFile',
			'change [type="checkbox"][value="___do_not_change"]' : 'dntChanged',
		},
		initialize:function() {
			this.$input = this.$('button');
			this.$hidden = this.$('[type="hidden"]');
			qe.field.View.prototype.initialize.apply(this,arguments);

			var self = this,
				post_id = acf.get('post_id');

			this.mediaFrameOpts = {
				field		: this.key,
				multiple	: false,
				post_id		: post_id,
				library		: this.$hidden.attr('data-library'),
				mode		:'select',
				type		: this.mediaFrameType,
				select		: function ( attachment, i ) {
					if ( ! attachment ) {
						return;
					}
					self.setValue( attachment.get('id') );
				}
			};
			if ( this.$hidden.data('mime_types') ) {
				this.mediaFrameOpts.mime_types = this.$hidden.data('mime_types');
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
			var media_frame = acf.media.popup( this.mediaFrameOpts ),
				media_id = this.$hidden.val();

			if ( !! media_id ) {
				media_frame.on('open',function(){
					var selection, attachment;
					selection = media_frame.state().get('selection');
					attachment = wp.media.attachment( media_id );
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				});
			}

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
			this.dntChanged();
			this.$hidden.val( value );
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
			 'change [type="checkbox"][value="___do_not_change"]' : 'dntChanged',
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
			this.$input		= this.$( '[type="text"]' );
			this.$hidden	= this.$( '[type="hidden"]' );
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
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
			var date;

			this.dntChanged();

			try {
				date = $.datepicker.parseDate( this.datePickerArgs.altFormat, value );
			} catch(err) {
				return this;
			}
//			console.log(value,this.datePickerArgs.altFormat,this.datePickerArgs.altTimeFormat,typeof value);
			this.$input.datepicker( 'setDate', date );
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
			this.$input		= this.$( '[type="text"]' );
			this.$hidden	= this.$( '[type="hidden"]' );
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
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
			var date;

			this.dntChanged();

			try {
				date = $.datepicker.parseDateTime(
					this.datePickerArgs.altFormat,
					this.datePickerArgs.altTimeFormat,
					value
				);
			} catch(err) {
				return this;
			}

			if ( ! date ) {
				return;
			}
			//*
			this.$hidden.val(date);
			this.$input.val( $.datepicker.formatDate(this.datePickerArgs.dateFormat, date) + ' ' + $.datepicker.formatTime(
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
			this.$input		= this.$( '[type="text"]' );
			this.$hidden	= this.$( '[type="hidden"]' );
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
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
			var time;

			this.dntChanged();
			try {
				time = $.datepicker.parseTime( this.datePickerArgs.altTimeFormat, value );
			} catch(err){
				return this;
			}
			if ( ! time ) {
				return;
			}
			this.$hidden.val( value );
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
			this.$input = this.$('[type="text"]').first().wpColorPicker();
			qe.field.View.prototype.initialize.apply(this,arguments);
		},
		setValue:function( value ) {
			this.dntChanged();
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
			this.$input = this.$('textarea').prop( 'readonly', true );

			qe.field.View.prototype.initialize.apply(this,arguments);


			this.$input.on('keydown keyup', function(e) {
				if ( e.which == 13 || e.which == 27 ) {
					e.stopPropagation();
//					e.preventDefault();
				}
			});
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
			'change [type="checkbox"].custom' : 'removeChoice',
			'change [type="checkbox"][value="___do_not_change"]' : 'dntChanged',
		},
		initialize:function() {
			this.$input = this.$('[type="checkbox"]:not([value="___do_not_change"])');
			this.$button = this.$('button.add-choice').prop('disabled',true);
			qe.field.View.prototype.initialize.apply(this,arguments);

		},
		setEditable:function(editable){
			this.$input.prop( 'readonly', !editable );
			this.$button.prop( 'disabled', !editable );
		},
		setValue:function( value ) {
			var self = this;
			this.dntChanged();
			if ( $.isArray(value) ) {
				$.each( value, function( idx, val ) {
					self.$( '[type="checkbox"][value="'+val+'"]' )
						.prop( 'checked', true );
				});
			} else {
				this.$( '[type="checkbox"][value="'+value+'"]' )
					.prop( 'checked', true );
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

			this.$input = this.$('[type="radio"]');

			qe.field.View.prototype.initialize.apply(this,arguments);

			this.$('[type="radio"]').prop( 'readonly', true );

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
			this.dntChanged();
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
			this.dntChanged();
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
