import $ from 'jquery';

module.exports = {
	type:'post_object',
	initialize:function() {
		this.parent().initialize.apply(this,arguments);

		this.$input = this.$('select').prop( 'readonly', true );
	},
	setValue:function(value) {
		// the value has been loded by an ajax request
		this.dntChanged( );
		let acfField = new acf.models.PostObjectField( this.$input.closest('.acf-field') )
		console.log(acfField)
		this.$input.val(value);
		return this;
	}
}
