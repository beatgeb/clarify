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
            this.$ctx.on('click', '.nav a', function() {
                var tab = $(this).data('tab');
                var $tabs = that.$ctx.find('.tab');
                $tabs.hide();
                that.$ctx.find('.tab-' + tab).show();
                that.$ctx.find('.nav li').removeClass('active');
                $(this).parent().addClass('active');
            });
            this.sandbox.subscribe('keyboard', this);
            callback();
        },

        open: function(id, data, callback, callback_delete) {
            this.templates[id] = !this.templates[id] ? doT.template($('#tmpl-modal-' + id).text()) : this.templates[id];
            var that = this;
            $ctx = this.$ctx;
            var $modal = $(this.templates[id](data));

            // bind callbacks
            $('.btn-primary', $modal).on('click', callback);
            $('.btn-delete', $modal).on('click', callback_delete);

            $ctx.find('.modal').empty().append($modal);
            $ctx.on('keypress', 'input', function(e) {
                if (e.keyCode == 13) {
                    $('.btn-primary').click();
                }
            });

            this.fire('RegisterShortcut', {
                'moduleId': this.id,
                'shortcut': 27,
                'modifier': null,
                'callback': function() {
                    that.cancel();
                }
            });

            this.$backdrop.show();
            $ctx.fadeIn('fast');
            $('input:first', $modal).focus();
        },

        cancel: function() {
            //$(document).off('keyup');
            this.fire('UnregisterShortcut', { 'moduleId': this.id });
            this.$ctx.hide();
            this.$backdrop.fadeOut('fast');
        }

    });
})(Tc.$);