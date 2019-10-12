import $ from 'jquery';
import { factory } from 'fields.js';

const View = Backbone.View.extend({
	events:{
		'heartbeat-send.wp-refresh-nonces': 'heartbeatListener'
	},
	initialize:function(){

		const self = this;
		this.active = true;
		this.options = arguments[0];

		Backbone.View.prototype.initialize.apply( this, arguments );

		this.fields = {};

		this.$('.inline-edit-col-qed [data-key]').each(function(i,el){
			var field = factory( el, this );
			self.fields[ field.key ] = field;
		});

		// load values
		if ( !! Object.keys( this.fields ).length ) {
			this.loadValues();
		}

	},
	getFieldsToLoad:function(){
		var fields = [];
		_.each( this.fields,function( field, key ) {
			/*
			if ( field.parent_key ) {
				fields.push( field.parent_key );
			} else {
				fields.push( field.key );
			}
			/*/
			fields.push( field.key );
			//*/
		});
		return fields;
	},
	loadedValues:function(values) {
		if ( this.active ) {
			this._setValues( values );
			this.initValidation();
		}
	},
	_setValues:function(values) {
		const self = this;
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
		this.active = false;
		acf.unload.reset();
	},
	validationComplete:function( json, $form ) {
		const self = this;

		if ( ! json.valid ) {
			_.each(json.errors,function(err){
				// err.input is in format `acf[<FIELD_KEY>]`
				var match = err.input.match(/\[([0-9a-z_]+)\]$/g), // match last field key
					key = !! match ? match[0].substring( 1, match[0].length -1 ) : false; // rm braces

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

		if ( ! $button.length ) {
			return;
		}

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

const QuickEdit = View.extend({
	loadValues: function() {
		const self = this;
		const data = _.extend( {}, acf_qef.options.request, {
			'object_id' : this.options.object_id,
			'acf_field_keys' : this.getFieldsToLoad(),
			'_wp_http_referrer': $('[name="_wp_http_referer"]:first').val()
		} );

		$.post({
			url:ajaxurl,
			data: data,
			success:function( response ){
				// check for response.success && response.message!
				self.loadedValues( response.data );
			}
		});

		return this;
	},
	getSaveButton:function(){
		return this.$el.closest('form').find('button.save')
	}
});

const BulkEdit = View.extend({
	// todo: do not change
	initialize:function(){

		const self = this;

		View.prototype.initialize.apply( this, arguments );

		acf.add_filter( 'prepare_for_ajax', this.prepareForAjax, null, this );

	},
	prepareForAjax:function(data){
		// remove unchanged values in bulk
		if ( !! data.acf ) {
			$.each(data.acf,function(i,val){
   				if ( val == acf_qef.options.do_not_change_value ) {
					delete( data.acf[i] );
   				}
   			});
		}
		return data;
	},
	loadValues: function() {
		var post_ids = [];
		$('[type="checkbox"][name="post[]"]:checked').each(function(){
			post_ids.push($(this).val())
		});

		const self = this;

		const data = _.extend( {}, acf_qef.options.request, {
			'object_id' : post_ids,
			'acf_field_keys' : this.getFieldsToLoad(),
			'_wp_http_referrer': $('[name="_wp_http_referer"]:first').val()
		} );

		$.post({
			url: ajaxurl,
			data: data,
			success: function( response ){
				// check for response.success && response.message!
				self.loadedValues( response.data );
			}
		});

		return this;
	},
	getSaveButton:function(){
		return this.$('[type="submit"]#bulk_edit');
	}
});


module.exports = {
	form : { BulkEdit, QuickEdit }
}
