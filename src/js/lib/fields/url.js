import $ from 'jquery';

module.exports = {
	type:'url',
	events:{
		'change [type="checkbox"][data-is-do-not-change="true"]' : 'dntChanged',
	},
	setBulkOperation: function(e) {
		if ( '' === $(e.target).val() ) {
			this.$input.attr('type','url')
		} else {
			this.$input.attr('type','text')
		}
	}
}
