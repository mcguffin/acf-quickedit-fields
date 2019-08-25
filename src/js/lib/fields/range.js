import $ from 'jquery';

module.exports = {
	type:'range',
	events:{
		'change [type="range"]'		: 'adaptNumber',
		'mousemove [type="range"]'		: 'adaptNumber',
		'change [type="number"]'		: 'adaptRange',
		'mousemove [type="number"]'	: 'adaptRange',
		'change [type="checkbox"][value="___do_not_change"]' : 'dntChanged',
	},
	adaptNumber:function(){
		this.$('[type="number"]').val( this.$('[type="range"]').val() );
	},
	adaptRange:function(){
		this.$('[type="range"]').val( this.$('[type="number"]').val() );
	}
}
