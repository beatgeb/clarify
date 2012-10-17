/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.Screen = Tc.Module.extend({
        on: function(callback) {
            var $ctx = this.$ctx;
            
            // set the size of the view
            $ctx.css({
                width: $ctx.data('width'),
                height: $ctx.data('height')
            });
            
            // set the size of the screen & add image
            $('.screen', $ctx).css({
                backgroundImage: 'url(' + $ctx.data('image') + ')',
                width: $ctx.data('width'),
                height: $ctx.data('height')
            });

            // activate the comments layer
            this.sandbox.getModuleById($('.modLayerComment').data('id')).activate();
            callback();
        }
    });
})(Tc.$);