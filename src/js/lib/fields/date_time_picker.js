import $ from 'jquery';

module.exports = {
	type:'date_time_picker',
	initialize:function() {
		const self = this;
		this.$input		= this.$( '[type="text"]' );
		this.$hidden	= this.$( '[type="hidden"]' );
		this.parent().initialize.apply(this,arguments);
		this.datePickerArgs = {
			altField			: this.$hidden,
			dateFormat			: this.$('[data-date_format]').data('date_format'),
			altFormat			: 'yy-mm-dd',
			timeFormat			: this.$('[data-time_format]').data('time_format'),
			altTimeFormat		: 'HH:mm:ss',
			altFieldTimeOnly	: false,
			changeYear			: true,
			yearRange			: "-100:+100",
			changeMonth			: true,
			showButtonPanel		: true,
			firstDay			: this.$('[data-first_day]').data('first_day'),
			controlType			: 'select',
			oneLine				: true
		};

		this.$input.datetimepicker( this.datePickerArgs ).on('blur',function(){
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
		let date, formattedDate, timeObject;

		this.dntChanged();

		try {
			date = $.datepicker.parseDateTime(
				this.datePickerArgs.altFormat,
				this.datePickerArgs.altTimeFormat,
				value
			);
		} catch(err) {
			return this;
		}

		if ( ! date ) {
			return;
		}
		//*
		timeObject = {
			hour: date.getHours(),
			minute: date.getMinutes(),
			second: date.getSeconds(),
			millisec: date.getMilliseconds(),
			microsec: 0,
			timezone: date.getTimezoneOffset(),
		};
		formattedDate = $.datepicker.formatDate(this.datePickerArgs.dateFormat, date) + ' ' + $.datepicker.formatTime(
			this.datePickerArgs.timeFormat,
			timeObject
		);
		this.$hidden.val(value);
		this.$input.val(formattedDate);
		/*/
		// will set form dirty if done like this
		this.$input.datepicker( 'setDate', date );
		//*/
		return this;
	}
};
