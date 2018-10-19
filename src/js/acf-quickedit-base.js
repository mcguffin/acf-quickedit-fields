(function( $, exports ){

	exports.acf_quickedit = qe = {
		form:{},
		field: {
			_types: {},// field types
			add_type:function(a) {
				qe.field._types[a.type] = qe.field.View.extend(a);
				return qe.field._types[a.type];
			},
			factory:function(el,controller){
				var type = $(el).attr('data-field-type'),
					types = qe.field._types;

				field_class = type in types ? types[type] : qe.field.View;
				return new field_class({
					el:			el,
					controller:	controller,
				});
			}
		},
	};


	qe.form.View = Backbone.View.extend({
		initialize:function(){

			var self = this;

			this.options = arguments[0];

			Backbone.View.prototype.initialize.apply( this, arguments );

			this.fields = {};

			this.$('.inline-edit-col-qed [data-key]').each(function(i,el){
				var field = qe.field.factory( el, this );
				self.fields[ field.key ] = field;
			});
			// load values
			this.loadValues();

		},
		getFieldsToLoad:function(){
			var fields = [];
			_.each( this.fields,function( field, key ) {
				if ( field.parent_key ) {
					fields.push( field.parent_key );
				} else {
					fields.push( field.key );
				}
			});
			return fields;
		},
		loadedValues:function(values) {
			this._setValues( values );
			this.initValidation();
		},
		_setValues:function(values) {
			var self = this;
			_.each( values, function( val, key ){
				if ( key in self.fields ) {
					self.fields[key].setValue( val );
				} else if( _.isObject( val ) ) {
					self._setValues(val);
				}
			});
		},
		unload:function(e){
			this.deinitValidation();
			_.each(this.fields,function(field){
				field.unload();
			});
			acf.unload.reset();
		},
		validationComplete:function( json, $form ) {
			var self = this;

			if ( ! json.valid ) {
				_.each(json.errors,function(err){
					// err.input is in format `acf[<FIELD_KEY>]`
					var key = err.input.replace(/^acf\[([0-9a-z_]+)\]$/g,'$1');
					if ( key in self.fields ) {
						self.fields[key].setError( err.message );
					}
				});
			} else {
				acf.unload.off();
			}
			return json;
		},
		deinitValidation:function(){
			var $button = this.getSaveButton();
			$button.off( 'click', this._saveBtnClickHandler );
		},
		initValidation:function() {
			var $form = this.$el.closest('form'),
				$button = this.getSaveButton();

			acf.update('post_id', this.options.object_id );

			acf.addFilter( 'validation_complete', this.validationComplete, 10, this );
//			acf.add_action('validation_failure', this.validationFailure );

			$button.on( 'click', this._saveBtnClickHandler );
			$form.data('acf',null)
			// move our events handler to front

			$._data($button[0],'events').click.reverse();

		},
		_saveBtnClickHandler:function(e) {
			// scope: quick/bulk edit save button
			var $button = $(this),
				$form = $(this).closest('form'),
				valid;

			valid = acf.validateForm({
				form: $form,
				event: false,
				reset: false,
				success: function( $form ) {
					$button.trigger('click');
				}
			});

			if ( ! valid ) {
				// stop WP JS validation
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
				return false;
			}
			return true;
		}
	});
	qe.form.QuickEdit = qe.form.View.extend({
		loadValues: function() {
			var self = this;
			$.post({
				url:ajaxurl,
				data:{
					'action' : 'get_acf_post_meta',
					'object_id' : this.options.object_id,
					'acf_field_keys' : this.getFieldsToLoad(),
				},
				success:function(response){
					self.loadedValues( response );
				}
			});

			return this;
		},
		getSaveButton:function(){
			return this.$el.closest('form').find('button.save')
		}
	});

	qe.form.BulkEdit = qe.form.View.extend({
		// todo: do not change
		initialize:function(){

			var self = this;

			qe.form.View.prototype.initialize.apply( this, arguments );

			acf.add_filter( 'prepare_for_ajax', this.prepareForAjax, null, this );

		},
		prepareForAjax:function(data){
			var ret = {};
			$.each(data,function(i,val){
				if (val !== '___do_not_change') {
					ret[i] = val;
				}
			});
			return ret;
		},
		loadValues: function() {
			var post_ids = [];
			$('[type="checkbox"][name="post[]"]:checked').each(function(){
				post_ids.push($(this).val())
			});

			var self = this;
			$.post({
				url:ajaxurl,
				data:{
					'action' : 'get_acf_post_meta',
					'object_id' : post_ids,
					'acf_field_keys' : this.getFieldsToLoad(),
				},
				success:function(response){
					self.loadedValues( response );
				}
			});

			return this;
		},
		getSaveButton:function(){
			return this.$('[type="submit"]#bulk_edit');
		}
	});



	qe.field.View = wp.media.View.extend({
		events:{
			'change [type="checkbox"][data-is-do-not-change="true"]' : 'dntChanged',
		},
		initialize:function(){
			var self = this;
			Backbone.View.prototype.initialize.apply( this, arguments );
			this.key = this.$el.attr('data-key');
			this.parent_key = this.$el.attr('data-parent-key');

			if( 'false' === this.parent_key ) {
				this.parent_key = false;
			}

			if ( ! this.$input ) {
				this.$input = this.$('input:not([data-is-do-not-change="true"])')
			}
			this.setEditable( false );
			this.$('*').on('change',function(){self.resetError()})
		},
		setValue:function(value){
			this.dntChanged( );
			this.$input.val(value);
			return this;
		},
		dntChanged:function(){
			this.setEditable( ! this.$('[type="checkbox"][data-is-do-not-change="true"]').is(':checked') );
		},
		setEditable:function(editable){
			this.$input.prop( 'readonly', ! editable ).prop( 'disabled', ! editable );
		},
		setError:function(message) {
			this.$el.attr('data-error-message',message);
			return this;
		},
		resetError:function() {
			this.$el.removeAttr( 'data-error-message' );
			return this;
		},
		unload:function(){}
	});


})( jQuery, window );
