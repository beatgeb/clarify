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
            that.sandbox.subscribe('activity.comment', this);

            callback();
        },

        onLayerActivated: function(layer) {
            $('.items').empty();
            this.$ctx.show();
        },

        onCommentAdded: function(data) {
            var id = 'comment';
            var $item = $(this.templates[id](data));
            this.$ctx.find('.items-comments').append($item);
            console.log(data);
        }

    });
})(Tc.$);