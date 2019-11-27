import $ from 'jquery';

module.exports = {
	type:'post_object',
	initialize:function() {

		this.$input = this.$('select').prop( 'readonly', true );
		//
		this.parent().initialize.apply(this,arguments);

	},
	setValue:function(value) {
		// the value has been loded by an ajax request

		this.dntChanged( );

		const self = this;
		const acfField = new acf.models.PostObjectField( this.$input.closest('.acf-field') )
		const append = item => {
			self.$input.append( new Option( item.text, item.id, true, true ) );
		}

		if( _.isArray( value ) ) {
			value.map( append )
		} else if( _.isObject(value) ) {
			append( value )
		}

		// do we need this ..?
		// self.$input.trigger('change');

		return this;
	}
}
