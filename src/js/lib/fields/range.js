import $ from 'jquery';

const field = {
	type:'range',
	events:{
		'change [type="range"]'		: 'adaptNumber',
		'mousemove [type="range"]'		: 'adaptNumber',
		'change [type="number"]'		: 'adaptRange',
		'mousemove [type="number"]'	: 'adaptRange',
	},
	adaptNumber:function(){
		this.$('[type="number"]').val( this.$('[type="range"]').val() );
	},
	adaptRange:function(){
		this.$('[type="range"]').val( this.$('[type="number"]').val() );
	}
}

field.events['change [type="checkbox"][value="'+acf_qef.options.do_not_change_value+'"]'] = 'dntChanged';

module.exports = field;
