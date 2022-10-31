import $ from 'jquery';

module.exports = {
	type:'taxonomy',
	initialize:function() {
		this.acfField = null

		this.parent().initialize.apply(this,arguments);

		this.$input = this.$('.acf-input-wrap select,.acf-input-wrap input').prop( 'readonly', true );
	},
	setValue:function( value ) {

		this.dntChanged( );

		const is_select = this.$input.is('select')
		const self = this;
		const acfFieldClass = acf.models.TaxonomyField.extend({
			$input: function () {
				return this.$('.acf-input-wrap select');
			},
		})
		this.acfField = new acfFieldClass( this.$input.closest('.acf-field') )
		const select = item => {
			if ( is_select ) {
				self.$input.append( new Option( item.text, item.id, true, true ) );
			} else {
				self.$input.filter( `[value="${item.id}"]` ).prop('checked',true)
			}
		}

		if( _.isArray( value ) ) { // multiple values
			value.map( select )
		} else if( _.isObject(value) ) { // single values
			select( value )
		}

	},
	unload:function(){
		this.acfField.onRemove()
	}
}
