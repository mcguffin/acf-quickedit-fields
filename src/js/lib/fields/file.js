import $ from 'jquery';

module.exports = {
	type:'file',
	mediaFrameType:'',
	events:{
		'click .select-media' : 'selectFile',
		'click .remove-media' : 'removeFile',
		'change [type="checkbox"][value="___do_not_change"]' : 'dntChanged',
	},
	initialize:function() {
		this.$input = this.$('button');
		this.$hidden = this.$('[type="hidden"]');
		this.parent().initialize.apply(this,arguments);

		const self = this,
			post_id = acf.get('post_id');

		this.mediaFrameOpts = {
			field		: this.key,
			multiple	: false,
			post_id		: post_id,
			library		: this.$hidden.attr('data-library'),
			mode		:'select',
			type		: this.mediaFrameType,
			select		: function ( attachment, i ) {
				if ( ! attachment ) {
					return;
				}
				self.setValue( attachment.get('id') );
			}
		};
		if ( this.$hidden.data('mime_types') ) {
			this.mediaFrameOpts.mime_types = this.$hidden.data('mime_types');
		}

	},
	selectFile:function(e){
		e.preventDefault();
		// Create a new media frame
		const media_frame = acf.media.popup( this.mediaFrameOpts ),
			media_id = this.$hidden.val();

		if ( !! media_id ) {
			media_frame.on('open',function(){
				const selection = media_frame.state().get('selection');
				const attachment = wp.media.attachment( media_id );
				attachment.fetch();
				selection.add( attachment ? [ attachment ] : [] );
			});
		}

		// set post id, so new uploads are attached to edited post
		if ( acf.isset(window,'wp','media','view','settings','post') && $.isNumeric( this.mediaFrameOpts.post_id ) ) {

			wp.media.view.settings.post.id = this.mediaFrameOpts.post_id;

		}

	},
	removeFile:function(e){
		e.preventDefault();
		this.setValue('');
	},
	setValue:function(value) {
		this.dntChanged();
		this.$hidden.val( value );
		return this;
	}
};
