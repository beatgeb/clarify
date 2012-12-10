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
            var that = this;
            
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

            var $overlay = $('.ajax .overlay');
            $('.ajax').ajaxStart(function() {
                $(this).show();
            });
            $('.ajax').ajaxStop(function() {
                $(this).hide();
            });
            $('.ajax').ajaxError(function() {
                $(this).hide();
            });
            $overlay.on('click', function(e) {
                return false;
            });
            

            // subscribe to the keyboard-channel
            this.sandbox.subscribe('keyboard', this);

            callback();
        },

        after: function() {

            // activate the comments layer
            this.sandbox.getModuleById($('.modLayerComment').data('id')).activate();
            
        }
    });
})(Tc.$);