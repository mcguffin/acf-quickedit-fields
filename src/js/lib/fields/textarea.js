import $ from 'jquery';

module.exports = {
	type:'textarea',
	initialize:function() {
		this.$input = this.$('textarea').prop( 'readonly', true );

		this.parent().initialize.apply(this,arguments);


		this.$input.on('keydown keyup', function(e) {
			if ( e.which == 13 || e.which == 27 ) {
				e.stopPropagation();
			}
		});
	}
}
