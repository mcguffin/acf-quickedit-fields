import $ from 'jquery';
import qe from 'base.js';
import '../acf-columns/index.js';


if ( 'undefined' !== typeof inlineEditPost ) {
	// we create a copy of the WP inline edit post function
	const _wp_inline_edit_post = inlineEditPost.edit,
		_wp_inline_edit_save = inlineEditPost.save,
		_wp_inline_edit_revert = inlineEditPost.revert,
		_wp_inline_edit_bulk =  inlineEditPost.setBulk;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {
		let object_id, $tr, ret;

		acf.validation.active = 1;

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		ret = _wp_inline_edit_post.apply( this, arguments );

		// get the post ID
		object_id = 0;
		if ( typeof( id ) === 'object' ) {
			object_id = parseInt( this.getId( id ) );
		}

		$tr = $('#edit-' + object_id );
//			get_acf_post_data( object_id , $('#edit-' + object_id ) );

		this.acf_qed_form = new qe.form.QuickEdit({
			el: $tr.get(0),
			object_id: object_id
		});

		return ret;
	};
	inlineEditPost.revert = function() {
		// unload forms
		!! this.acf_qed_form && this.acf_qed_form.unload();
		return _wp_inline_edit_revert.apply( this, arguments );
	}
	inlineEditPost.save = function() {
		// unload forms
		!! this.acf_qed_form && this.acf_qed_form.unload();
		return _wp_inline_edit_save.apply( this, arguments );
	}
	inlineEditPost.setBulk = function() {
		const ret = _wp_inline_edit_bulk.apply( this, arguments );
		this.acf_qed_form = new qe.form.BulkEdit({
			el: $('#bulk-edit').get(0),
//				object_id: object_id
		});

		return ret;
	}
}
// todo: inlineEditTax
if ( 'undefined' !== typeof inlineEditTax ) {

	const _wp_inline_edit_tax = inlineEditTax.edit,
		_wp_inline_edit_save = inlineEditTax.save,
		_wp_inline_edit_revert = inlineEditTax.revert;

	inlineEditTax.edit = function( id ) {
		const tax = $('input[name="taxonomy"]').val();
		let object_id, $tr, ret;

		ret = _wp_inline_edit_tax.apply( this, arguments );

		// get the post ID
		object_id = 0;
		if ( typeof( id ) === 'object' ) {
			object_id = parseInt( this.getId( id ) );
		}
		$tr = $('#edit-' + object_id );

		this.acf_qed_form = new qe.form.QuickEdit({
			el: $tr.get(0),
			object_id: tax + '_' + object_id
		});
		return ret;
	};
	inlineEditTax.revert = function() {
		// unload forms
		!! this.acf_qed_form && this.acf_qed_form.unload();
		return _wp_inline_edit_revert.apply( this, arguments );
	}
	inlineEditTax.save = function() {
		// unload forms
		!! this.acf_qed_form && this.acf_qed_form.unload();
		return _wp_inline_edit_save.apply( this, arguments );
	}
}
