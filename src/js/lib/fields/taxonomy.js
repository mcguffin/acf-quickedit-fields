import $ from 'jquery';

module.exports = {
	type:'taxonomy',
	initialize:function() {
		this.parent().initialize.apply(this,arguments);

		this.$input = this.$('select,input[value!="'+acf_qef.options.do_not_change_value+'"]').prop( 'readonly', true );
	},
	setValue:function( value ) {
		/*
		const self = this;
		this.dntChanged();
		if ( 'number' === typeof value || value.match(/^[0-9]+$/g) ) {
			value = [ value ];
		}
		console.log(value,typeof value)
		$.each( value, function( i, val ) {
			self.$('[value="'+val+'"]' ).each(function(i,el){
				if ( $(this).is('[type="radio"],[type="checkbox"]') ) {
					$(this).prop( 'checked', true );
				} else if ( $(this).is('option') ) {
					$(this).prop( 'selected', true );
				}
			});
		});
		/*/

		this.dntChanged( );

		const self = this;
		const acfField = new acf.models.TaxonomyField( this.$input.closest('.acf-field') )
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
		//*/
	}
}
