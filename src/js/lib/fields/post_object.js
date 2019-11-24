import $ from 'jquery';

module.exports = {
	type:'post_object',
	initialize:function() {

		this.$input = this.$('select').prop( 'readonly', true );

		this.parent().initialize.apply(this,arguments);

		if( typeof acf !== 'object' ) {
			return;
		}
		var self = this;

		// init select2
		this.$input.select2();		// 2DO: not really needed, but helps in showing placeholder text on init

		// 2DO: workaround 'acf/fields/undefined/query' ajax action, but why?
		acf.add_filter('select2_ajax_data', function( data, args, $input, $field ){
			if( data.field_key !== self.key ) {
				return data;
			}
			
			let type = args.field.data.fieldType;
			if( typeof type !== 'undefined' ) {
				data.action = 'acf/fields/'+type+'/query';
			}
			
			return data;	
		});

	},
	setValue:function(value) {
		// the value has been loded by an ajax request
		this.dntChanged( );
		let acfField = new acf.models.PostObjectField( this.$input.closest('.acf-field') )
		/*
		`this.$input` points to the <select> jQuery object
		`value` contains the selected post IDs.
		We'll have to fetch the corresponding post titles as well and append selected <option>s to this.$input
		*/

		var self = this;
		if( _.isArray(value) ) {
			_.each(value, function(val) {
				self.$input.append( new Option(val.text, val.id, true, true) ).trigger('change');
			});
		} else if( _.isObject(value) ) {
			// set current default value by appending a selected option element
			self.$input.append( new Option(value.text, value.id, true, true) ).trigger('change');
		}

		return this;
	}
}

