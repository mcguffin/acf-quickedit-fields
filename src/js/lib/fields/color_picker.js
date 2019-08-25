import $ from 'jquery';

module.exports = {
	type:'color_picker',
	initialize:function() {
		const args = acf.apply_filters('color_picker_args', {
			defaultColor: false,
			palettes: true,
			hide: true,
		}, this.$el );
		this.$input = this.$('[type="text"]').first().wpColorPicker( args );
		this.parent().initialize.apply(this,arguments);
	},
	setValue:function( value ) {
		this.dntChanged();
		this.$input.wpColorPicker( 'color', value );
	},
	unload:function() {
		$( 'body' ).off( 'click.wpcolorpicker' );
	}
}
