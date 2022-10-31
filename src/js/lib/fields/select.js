import $ from 'jquery';

module.exports = {
	type:'select',
	initialize:function() {
		this.parent().initialize.apply(this,arguments);

		this.$input = this.$('.acf-input-wrap select').prop( 'readonly', true );
	},
	setValue:function( value ) {

		this.dntChanged( );

		const is_select = this.$input.is('select')
		const self = this;
		const acfFieldClass = acf.models.SelectField.extend({
			$input: function () {
				return this.$('.acf-input-wrap select');
			},
		})
		this.acfField = new acfFieldClass( this.$input.closest('.acf-field') )
		const select = item => {
			self.$input.append( new Option( item.text, item.id, false, true ) );
		}
		if ( '' !== value ) {
			if( _.isArray( value ) ) { // multiple values
				value.map( select )
			} else if( _.isObject(value) ) { // single values
				select( value )
			}
		}
		console.log(this.acfField)
		// empty value on UI
	},
	unload:function(){
		this.acfField.onRemove()
	}
}
