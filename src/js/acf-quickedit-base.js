(function( $, exports ){
	exports.acf_quickedit = qe = {
		form:{},
		field: {
			_types: {},// field types
			add_type:function(a) {
				qe.field._types[a.type] = qe.field.View.extend(a);
				return qe.field._types[a.type];
			},
			factory:function(el,controller){
				var type = $(el).attr('data-field-type'),
					types = qe.field._types;

				field_class = type in types ? types[type] : qe.field.View;
				return new field_class({
					el:			el,
					controller:	controller,
				});
			}
		},
	};
	qe.form.View = Backbone.View.extend({
		initialize:function(){

			var self = this;

			this.options = arguments[0];

			Backbone.View.prototype.initialize.apply( this, arguments );

			this.fields = {};

			this.$('.inline-edit-col-qed [data-key]').each(function(i,el){
				var field = qe.field.factory( el, this );
				self.fields[ field.key ] = field;
			});
			// load values
			this.loadValues();

		},
		loadValues: function() {
			var self = this;
			$.post({
				url:ajaxurl,
				data:{
					'action' : 'get_acf_post_meta',
					'object_id' : this.options.object_id,
					'acf_field_keys' : Object.keys(this.fields)
				},
				success:function(response){
					_.each(response,function( val, key ){
						self.fields[key].setValue( val );
					});
				}
			});
			// todo: bind validation
			return this;
		},
		unload:function(e){
			_.each(this.fields,function(field){
				field.unload();
			});
		}
	});
	qe.form.QuickEdit = qe.form.View.extend({

	});

	qe.form.BulkEdit = qe.form.View.extend({
		// todo: do not change
	});



	qe.field.View = wp.media.View.extend({
		initialize:function(){
			Backbone.View.prototype.initialize.apply( this, arguments );
			this.key = this.$el.attr('data-key');
			this.$('input').prop( 'readonly', true );

		},
		setValue:function(value){
			this.$('input').prop( 'readonly', false );
			this.$('input').val(value);
			return this;
		},
		unload:function(){}
	});


})(jQuery,window);
