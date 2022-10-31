import $ from 'jquery';

// fields
import button_group from 'fields/button_group.js'
import checkbox from 'fields/checkbox.js'
import color_picker from 'fields/color_picker.js'
import date_picker from 'fields/date_picker.js'
import date_time_picker from 'fields/date_time_picker.js'
import file from 'fields/file.js'
import image from 'fields/image.js'
import link from 'fields/link.js'
import post_object from 'fields/post_object.js'
import radio from 'fields/radio.js'
import range from 'fields/range.js'
import select from 'fields/select.js'
import taxonomy from 'fields/taxonomy.js'
import textarea from 'fields/textarea.js'
import time_picker from 'fields/time_picker.js'
import true_false from 'fields/true_false.js'
import url from 'fields/url.js'

const user = Object.assign({},select,{type:'user'} );

const View = wp.media.View.extend({
	events:{
		'change [type="checkbox"][data-is-do-not-change="true"]' : 'dntChanged',
	},
	initialize:function(){
		const self = this;
		Backbone.View.prototype.initialize.apply( this, arguments );
		this.key = this.$el.attr('data-key');
		this.$bulkOperations = this.$('.bulk-operations select,.bulk-operations input')
		if ( ! this.$input ) {
			this.$input = this.$('.acf-input-wrap input')
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
		this.$input.each( (i,el) => $(el).prop( 'readonly', ! editable ).prop( 'disabled', ! editable ) );
		this.$bulkOperations.prop( 'readonly', ! editable ).prop( 'disabled', ! editable )
	},
	setError:function(message) {
		this.$el.attr('data-error-message',message);
		return this;
	},
	resetError:function() {
		this.$el.removeAttr( 'data-error-message' );
		return this;
	},
	unload:function(){},
	parent: () => View.prototype
});


const types = {};

const field = {

	add_type:function(t) {
		types[t.type] = View.extend( t );
		return types[t.type];
	},
	factory:function(el,controller){
		const type = $(el).attr('data-field-type'),
			field_class = type in types ? types[type] : View;

		return new field_class({
			el:			el,
			controller:	controller,
		});
	},
	View
}


/**
 *	field type file
 */
field.add_type( file );

/**
 *	field type image
 */
field.add_type( image );

/**
 *	field type range
 */
field.add_type( range );

/**
 *	field type date_picker
 */
field.add_type( date_picker );

/**
 *	field type date_time_picker
 */
field.add_type( date_time_picker );

/**
 *	field type time_picker
 */
field.add_type( time_picker );

/**
 *	field type color_picker
 */
field.add_type( color_picker );

/**
 *	field type time_picker
 */
field.add_type( textarea );

/**
 *	field type checkbox
 */
field.add_type( checkbox );


/**
 *	field type radio
 */
field.add_type( link )

/**
 *	field type radio
 */
field.add_type( radio );

/**
 *	field type button group
 */
field.add_type( button_group );

/**
 *	field type select
 */
field.add_type( select );

/**
 *	field type post_object
 */
field.add_type( post_object );

/**
 *	field type taxonomy
 */
field.add_type( taxonomy );

/**
 *	field type true_false
 */
field.add_type( true_false );

/**
 *	field type user
 */
field.add_type( user );

/**
 *	field type url
 */
field.add_type( url );

module.exports = field;
