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
            this.templates['color'] = doT.template($('#tmpl-sidebar-color').text());
            
            $('.tab-comment', $ctx).on('click', function() { that.onLayerActivated('comment', true); });
            $('.tab-color', $ctx).on('click', function() { that.onLayerActivated('color', true); });

            $ctx.on('mouseover', '.items-colors .item', function() {
                var id = $(this).data('id');
                $('.modLayerColor .color').css('opacity', 0.2);
                console.log(id);
                $('.modLayerColor .color[data-project-color="' + id + '"]').css('opacity', 1);
            });
            $ctx.on('mouseleave', '.items-colors', function() {
                $('.modLayerColor .color').css('opacity', 1);
            });

            $('.btn-sidebar-toggle').on('click', function() {
                if (that.$ctx.css('right') == '0px') {
                    that.close();
                } else {
                    that.open();
                }
            });

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
                    if (that.$ctx.css('right') == '0px') {
                        that.close();
                    } else {
                        that.open();
                    }
                }
            });
        },

        onLayerActivated: function(layer, tab) {
            $('.items').hide();
            $('.nav li', this.$ctx).removeClass('active');
            $('.tab-' + layer, this.$ctx).addClass('active');
            switch (layer) {
                case 'comment':
                    if (!tab) {
                        $('.items-comments').empty();
                    }
                    $('.items-comments').fadeIn();
                    this.open();
                    break;
                case 'color':
                    $('.items-colors').fadeIn();
                    this.open();
                    break;
            }
        },

        onLayerDeactivated: function(layer) {
            this.close();
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
        },

        onColorAdded: function(data) {
            var id = 'color';
            var $item = $(this.templates[id](data));
            this.$ctx.find('.items-colors').append($item);
        },

        onColorRemoved: function(id) {
            $('.items-colors .item-' + id).fadeOut();
        },

        onColorUpdated: function(data) {
            
        },

        close: function() {
            this.$ctx.stop().animate({ 'right': -260 }, 100);
            $('.btn-sidebar-toggle .icon-remove').hide();
            $('.btn-sidebar-toggle .icon-reorder').show();
        },

        open: function() {
            this.$ctx.stop().animate({ 'right': 0 }, 100);
            $('.btn-sidebar-toggle .icon-reorder').hide();
            $('.btn-sidebar-toggle .icon-remove').show();
        }

    });
})(Tc.$);