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
		/*
		`this.$input` points to the <select> jQuery object
		`value` contains the selected post IDs.
		We'll have to fetch the corresponding post titles as well and append selected <option>s to this.$input
		*/

		this.$input.val(value);
		return this;
	}
}
