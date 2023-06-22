import $ from 'jquery';
import selectFactory from './select-factory';

/*
Can be radio or checkbox too.
*/
module.exports = {
	type:'taxonomy',
	initialize:function() {
		this.subType = this.$el.attr('data-field-sub-type')
		if ('checkbox' === this.subType ) {
			this.inputSelect = '.acf-input-wrap [type="checkbox"]'
		} else if ('radio' === this.subType ) {
			this.inputSelect = '.acf-input-wrap [type="radio"]'
		} else {
			this.inputSelect = '.acf-input-wrap select'
		}

		this.acfField = null

		this.$input = this.$(this.inputSelect).prop( 'readonly', true );

		this.parent().initialize.apply(this,arguments);
	},
	setValue:function(value) {
		// the value has been loaded by an ajax request

		this.dntChanged( );

		const self = this;
		const acfFieldClass = acf.models.TaxonomyField.extend({
			$input: function () {
				return this.$('.acf-input-wrap select');
			},
		})
		this.acfField = new acfFieldClass( this.$input.closest('.acf-field') )

		if ('checkbox' === this.subType ) {
			this.setCheckboxValue(value)
		} else if ('radio' === this.subType ) {
			this.setCheckboxValue(value)
		} else {
			this.setSelectValue(value)
		}


		return this;
	},
	setCheckboxValue:function(value){

		const setChecked = val => this.$el.find(`${this.inputSelect}[value="${val.id}"]`).prop('checked',true)

		if( _.isArray( value ) ) {
			value.map( setChecked )
		} else if( _.isObject(value) ) {
			setChecked(value)
		}

		return this;
	},
	// setRadioValue:function(value){
	// 	return this;
	// },
	setSelectValue:function(value){
		const append = item => {
			this.$input.append( new Option( item.text, item.id, true, true ) );
		}
		if( _.isArray( value ) ) {
			value.map( append )
		} else if( _.isObject(value) ) {
			append( value )
		}
		return this;
	},
	unload:function(){
		this.acfField && this.acfField.onRemove()
	}
}
