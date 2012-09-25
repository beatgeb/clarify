/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.Modal = Tc.Module.extend({

    	templates: [],
        $backdrop: null,

        on: function(callback) {
        	var that = this;
            this.$backdrop = $('.modModalBackdrop');
            this.$ctx.on('click', '.btn-cancel, .btn-close', function() {
            	that.cancel();
            	return false;
            });
            callback();
        },

        open: function(id, data, callback) {
        	this.templates[id] = !this.templates[id] ? doT.template($('#tmpl-modal-' + id).text()) : this.templates[id];
            var that = this;
        	var $ctx = this.$ctx;
        	var $modal = $(this.templates[id](data));
            $('.btn-primary', $modal).on('click', callback);
        	$ctx.find('.modal').empty().append($modal);
            $ctx.on('keypress', 'input', function(e) {
                if (e.keyCode == 13) {
                    $('.btn-primary').click();
                }
            });
            $(document).on('keyup', function(e){
                if (e.keyCode === 27) {
                    that.cancel();
                }
            });
        	this.$backdrop.show();
        	$ctx.fadeIn('fast');
            $('input:first', $modal).focus();
    	},

    	cancel: function() {
            $(document).off('keyup');
    		this.$ctx.hide();
    		this.$backdrop.fadeOut('fast');
    	}

    });
})(Tc.$);