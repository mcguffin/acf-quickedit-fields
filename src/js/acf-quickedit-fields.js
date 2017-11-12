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
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
			var $hidden		= this.$( '[type="hidden"]' ),
				$input		= this.$( '[type="text"]' ),
				altFormat	= 'yymmdd'
				args		= {
					dateFormat		: this.$el.data('date_format'),
					altFormat		: altFormat,
					altField		: $hidden,
					changeYear		: true,
					yearRange		: "-100:+100",
					changeMonth		: true,
					showButtonPanel	: true,
					firstDay		: this.$el.data('first_day')
				},
				date		= $.datepicker.parseDate( altFormat, $hidden.val()  );

			$input.datepicker( args ).datepicker( 'setDate', date ).on('blur',function(){
				if ( ! $(this).val() ) {
					$hidden.val('');
				}
			});
		}
 	});

	/**
 	 *	field type date_time_picker
 	 */
 	qe.field.add_type( {
		type:'date_time_picker',
		initialize:function() {
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
			var $hidden			= this.$( '[type="hidden"]' ),
				$input			= this.$( '[type="text"]' ),
				altFormat		= 'yy-mm-dd'
				altTimeFormat	= 'HH:mm:ss',
				args			= {
					altField			: $hidden,
					dateFormat			: this.$el.data('date_format'),
					altFormat			: altFormat,
					timeFormat			: this.$el.data('time_format'),
					altTimeFormat		: altTimeFormat,
					altFieldTimeOnly	: false,
					changeYear			: true,
					yearRange			: "-100:+100",
					changeMonth			: true,
					showButtonPanel		: true,
					firstDay			: this.$el.data('first_day'),
					controlType			: 'select',
					oneLine				: true
				},
				datetime 			= $.datepicker.parseDateTime( altFormat, altTimeFormat, $hidden.val() );

			$input.datetimepicker( args ).datepicker( 'setDate', datetime ).on('blur',function(){
				if ( ! $(this).val() ) {
					$hidden.val('');
				}
			});
		}
	});

	/**
 	 *	field type time_picker
 	 */
 	qe.field.add_type( {
		type:'time_picker',
		initialize:function() {
			qe.field.View.prototype.initialize.apply(this,arguments);
			if( $('body > #ui-datepicker-div').length > 0 ) {
				$('body > #ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
			}
			var self			= this,
				$hidden			= this.$( '[type="hidden"]' ),
 				$input			= this.$( '[type="text"]' ),
 				altTimeFormat	= 'HH:mm:ss',
 				args			= {
 					timeFormat			: this.$el.data('time_format'),
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
 				$input.val( $.datepicker.formatTime( self.$el.data('time_format'), time ) )
 			}

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
			try {
				this.$input.wpColorPicker('close');
			} catch(err){}
		}
	});

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
