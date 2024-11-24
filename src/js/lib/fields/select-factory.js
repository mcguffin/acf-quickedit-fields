import $ from 'jquery';

module.exports = ( type, fieldBaseClass ) => {
	return {
		type,
		initialize:function() {
			this.acfField = null
			this.$input = this.$('.acf-input-wrap select').prop( 'readonly', true );
			//
			this.parent().initialize.apply(this,arguments);
		},
		setValue:function(value) {
			// the value has been loaded by an ajax request

			this.dntChanged( );

			const self        = this,
				ui            = !! this.$input.data('ui'),
				multiple      = !! this.$input.data('multiple'),
				acfFieldClass = fieldBaseClass.extend({
					$input: function () {
						return this.$('.acf-input-wrap select');
					},
				})

			this.acfField = new acfFieldClass( this.$input.closest('.acf-field') )

			const getSelect2 = () => {
				const select2Id = this.$input.attr('data-select2-id')
				return $(`#${select2Id}`);
			}

			const appendOrSelect = item => {

				let $input = ui
					? getSelect2()
					: self.$input

				const $option = $input.find(`[value="${item.id}"]`)

				if ( $option.length ) {
					$option.prop( 'selected', true )
				} else {
					$input.append( new Option( item.text, item.id, true, true ) );
				}
				$input.trigger('change')
			}


			if( _.isArray( value ) ) {
				value.map( appendOrSelect )
			} else if( _.isObject(value) ) {
				appendOrSelect( value )
			}

			return this;
		},
		unload:function(){
			this.acfField && this.acfField.onRemove()
		}
	}
}
