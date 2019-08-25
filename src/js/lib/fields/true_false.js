import $ from 'jquery';

module.exports = {
	type:'true_false',
	initialize:function() {
		this.parent().initialize.apply(this,arguments);

		this.$('[type="radio"]').prop( 'readonly', true );
	},
	setValue:function( value ) {
	this.dntChanged();
	this.$('[type="radio"][value="'+value+'"]' )
		.prop( 'checked', true );
	}
}
