import $ from 'jquery';

module.exports = {
	type:'time_picker',
	initialize:function() {
		const self = this;
		this.$input		= this.$( '[type="text"]' );
		this.$hidden	= this.$( '[type="hidden"]' );
		this.parent().initialize.apply(this,arguments);
		this.datePickerArgs = {
					timeFormat			: this.$('[data-time_format]').data('time_format'),
					altTimeFormat		: 'HH:mm:ss',
					altField			: this.$hidden,
					altFieldTimeOnly	: false,
					showButtonPanel		: true,
					controlType			: 'select',
					oneLine				: true
				};

			this.$input.timepicker( this.datePickerArgs ).on('blur',function(){
				if ( ! $(this).val() ) {
					self.$hidden.val('');
				}
			});
		if( $('body > #ui-datepicker-div').length > 0 ) {
			$('#ui-datepicker-div').wrap('<div class="acf-ui-datepicker" />');
		}
		return this;
	},
	setEditable:function(editable){
		this.parent().setEditable.apply(this,arguments);
		this.$hidden.prop( 'disabled', ! editable );
	},
	setValue:function(value) {
		let time;

		this.dntChanged();
		try {
			time = $.datepicker.parseTime( this.datePickerArgs.altTimeFormat, value );
		} catch(err){
			return this;
		}
		if ( ! time ) {
			return;
		}
		this.$hidden.val( value );
		this.$input.val( $.datepicker.formatTime( this.datePickerArgs.timeFormat, time ) )
		return this;
	}
};
