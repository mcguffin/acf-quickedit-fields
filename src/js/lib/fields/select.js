import $ from 'jquery';

module.exports = {
	type:'select',
	initialize:function() {
		this.parent().initialize.apply(this,arguments);

		this.$input = this.$('select').prop( 'readonly', true );
	}
}
