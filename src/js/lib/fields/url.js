import $ from 'jquery';

module.exports = {
	type:'url',
	events:{
		'change [type="checkbox"][data-is-do-not-change="true"]' : 'dntChanged',
		'change .bulk-operations select' : 'setBulkOperation',
	},
	setBulkOperation: function(e) {
		if ( '' === $(e.target).val() ) {
			this.$input.attr('type','url')
		} else {
			this.$input.attr('type','text')
		}
	}
}
