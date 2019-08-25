import $ from 'jquery';

module.exports = {
	type:'button_group',
	initialize:function() {
		const $ul = this.$('ul'),
			$lis = this.$('li');
		this.$input = this.$('[type="radio"]');


		this.parent().initialize.apply(this,arguments);

		this.$('[type="radio"]').prop( 'readonly', true );

		if ( this.$el.is( '[data-allow-null="1"]' ) ) {
			this.$el.on( 'click', '[type="radio"]', function(e){
				const $li = $(this).closest('li'),
					selected = $li.hasClass('selected');

				$lis.removeClass('selected');

				if ( selected ) {
					$(this).prop( 'checked', false );
					return;
				}

				$li.addClass('selected');
			});
		}
		//

	},
	setValue:function( value ) {
		this.dntChanged();
		this.$('[type="radio"][value="'+value+'"]' )
			.prop( 'checked', true )
			.closest('li').addClass('selected');
	}
}
