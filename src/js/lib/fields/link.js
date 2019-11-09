import $ from 'jquery';

const field = {
	type:'link',
	events:{
		'click .select-link' : 'selectLink',
		'click .remove-link' : 'resetLink',
	},
	initialize:function() {
		this.$input = this.$('[data-link-prop],button');
		this.parent().initialize.apply( this, arguments );
		this.$display = this.$('.link-content');
	},
	resetLink:function(e) {
		e.preventDefault();
		this.$input.val('');
		this.render();
	},
	selectLink:function(e) {
		e.preventDefault();
		let $a = this.$('a');
		if ( ! $a.length ) {
			$a = $('<a></a>').appendTo( this.$display );
		}
		$(document).on('wplink-close', this, this.parseCB );
		acf.wpLink.open( $a );
	},
	setValue:function( value ) {
		const self = this;
		this.dntChanged();
		$.each(
			value,
			( prop, val ) => self.$('[data-link-prop="'+prop+'"]').val(val)
		);
		this.render();
	},
	parseCB: function(e) {
		const self = e.data;
		setTimeout( () => { self.parse() }, 1 );
		$(document).off('wplink-close', e.data.parseCB );
	},
	parse:function() {
		const $a = this.$('a');
		this.$('[data-link-prop="target"]').val( $a.attr('target') );
		this.$('[data-link-prop="url"]').val( $a.attr('href') );
		this.$('[data-link-prop="title"]').val( $a.html() );
	},
	render:function() {
		let link = '',
			target = this.$('[data-link-prop="target"]').val(),
			url = this.$('[data-link-prop="url"]').val(),
			title = this.$('[data-link-prop="title"]').val() || url;
		if ( !! url ) {
			target = !! target ? `target="${target}"` : '';
			link = `<a href="${url}"${target}>${title}</a>`;
		}
		this.$display.html( link );
	}
}

field.events['change [type="checkbox"][value="'+acf_qef.options.do_not_change_value+'"]'] = 'dntChanged';

module.exports = field;
