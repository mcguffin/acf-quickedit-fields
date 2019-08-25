import $ from 'jquery';

module.exports = {
	type:'date_picker',
	initialize:function() {
		const self = this;
		this.$input		= this.$( '[type="text"]' );
		this.$hidden	= this.$( '[type="hidden"]' );
		this.parent().initialize.apply(this,arguments);
		this.datePickerArgs = {
			dateFormat		: this.$('[data-date_format]').data('date_format'),
			altFormat		: 'yymmdd',
			altField		: this.$hidden,
			changeYear		: true,
			yearRange		: "-100:+100",
			changeMonth		: true,
			showButtonPanel	: true,
			firstDay		: this.$('[data-first_day]').data('first_day')
		};
		this.$input.datepicker( this.datePickerArgs ).on('blur',function(){
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
		let date;

		this.dntChanged();

		try {
			date = $.datepicker.parseDate( this.datePickerArgs.altFormat, value );
		} catch(err) {
			return this;
		}
		this.$input.datepicker( 'setDate', date );
		return this;
	}
};
