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

			const self = this;
			const acfFieldClass = fieldBaseClass.extend({
				$input: function () {
					return this.$('.acf-input-wrap select');
				},
			})
			this.acfField = new acfFieldClass( this.$input.closest('.acf-field') )
			const append = item => {
				self.$input.append( new Option( item.text, item.id, true, true ) );
			}

			if( _.isArray( value ) ) {
				value.map( append )
			} else if( _.isObject(value) ) {
				append( value )
			} else if ( (  _.isNumber(value) || _.isString(value) ) && this.$input.find(`[value="${value}"]`).length ) {
				this.$input.val(value)
			}

			return this;
		},
		unload:function(){
			this.acfField.onRemove()
		}
	}
}
