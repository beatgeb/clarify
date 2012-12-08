/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 

    Tc.Module.Sidebar = Tc.Module.extend({
        
        templates: [],

        on: function(callback) { 
            var that = this;
            var $ctx = this.$ctx;

            this.templates['comment'] = doT.template($('#tmpl-sidebar-comment').text());
            this.sandbox.subscribe('keyboard', this);

            callback();
        },

        after: function() {
            var that = this;
            this.fire('RegisterShortcut', {
                'moduleId': that.id,
                'shortcut': 's',
                'modifier': null,
                'description': 'Toggle sidebar',
                'callback': function() {
                    that.$ctx.toggle();
                }
            });
        },

        onLayerActivated: function(layer) {
            $('.items').empty();
        },

        onCommentRemoved: function(id) {
            $('.items-comments .item-' + id).fadeOut();
        },

        onCommentUpdated: function(data) {
            $('.items-comments .item-' + data.id + ' .content').text(data.content);
        },

        onCommentAdded: function(data) {
            var id = 'comment';
            var $item = $(this.templates[id](data));
            this.$ctx.find('.items-comments').append($item);
            $item.hover(function() {
                $('.def').css('opacity', 0.2);
                $('.def-' + data.id).css('opacity', 1);
            }, function() {
                $('.def').css('opacity', 1);
            });
        }

    });
})(Tc.$);