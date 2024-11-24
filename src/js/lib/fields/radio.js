import $ from 'jquery';

module.exports = {
	type: 'radio',
	initialize: function() {
		let $other, is_other;

		this.$input = this.$('[type="radio"]');

		this.parent().initialize.apply(this,arguments);

		this.$('[type="radio"]').prop( 'readonly', true );

		if ( this.$('ul.acf-radio-list.other').length ) {
			$other = this.$('[type="text"]');
			this.$('[type="radio"]').on('change',function(e){

				is_other = $(this).is('[value="other"]:checked');
				$other
					.prop('disabled', ! is_other )
					.prop('readonly', ! is_other );

			})
		}
	},
	setValue: function( value ) {
		this.dntChanged();
		var $cb = this.$('[type="radio"][value="'+value+'"]' )
		if ( ! $cb.length ) {
			$cb = this.$('[type="radio"][value="other"]' )
			$cb.next('[type="text"]').prop( 'disabled', false ).val( value )
		}
		$cb.prop( 'checked', true );
	}
}
