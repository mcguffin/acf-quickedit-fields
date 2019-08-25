import $ from 'jquery';

module.exports = {
	type:'checkbox',
	events:{
		'click .add-choice': 'addChoice',
		'change [type="checkbox"].custom' : 'removeChoice',
		'change [type="checkbox"][value="___do_not_change"]' : 'dntChanged',
	},
	initialize:function() {
		this.$input = this.$('[type="checkbox"]:not([value="___do_not_change"])');
		this.$button = this.$('button.add-choice').prop('disabled',true);
		this.parent().initialize.apply(this,arguments);

	},
	setEditable:function(editable){
		this.$input.prop( 'disabled', !editable );
		this.$button.prop( 'disabled', !editable );
	},
	setValue:function( value ) {
		const self = this;
		this.dntChanged();
		if ( $.isArray(value) ) {
			$.each( value, function( idx, val ) {
				self.$( '[type="checkbox"][value="'+val+'"]' )
					.prop( 'checked', true );
			});
		} else {
			this.$( '[type="checkbox"][value="'+value+'"]' )
				.prop( 'checked', true );
		}
	},
	addChoice:function(e){
		e.preventDefault();
		const tpl = wp.template('acf-qef-custom-choice-' + this.$el.attr('data-key'));
		this.$('ul').append(tpl());
	},
	removeChoice:function(e) {
		$(e.target).closest('li').remove();
	}

}
