import $ from 'jquery';

module.exports = {
	type:'taxonomy',
	initialize:function() {
		this.parent().initialize.apply(this,arguments);

		this.$input = this.$('select,input[value!="'+acf_qef.options.do_not_change_value+'"]').prop( 'readonly', true );
	},
	setValue:function( value ) {
		const self = this;
		this.dntChanged();
		if ( 'number' === typeof value ) {
			value = [ value ];
		}
		$.each( value, function( i, val ) {
			self.$('[value="'+val+'"]' ).each(function(i,el){
				if ( $(this).is('[type="radio"],[type="checkbox"]') ) {
					$(this).prop( 'checked', true );
				} else if ( $(this).is('option') ) {
					$(this).prop( 'selected', true );
				}
			});
		});
	}
}
