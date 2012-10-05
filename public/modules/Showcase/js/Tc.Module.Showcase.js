/**
 * Clarify.
 *
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.Showcase = Tc.Module.extend({

        on: function(callback) {
        	callback();
        },

        after: function() {
        	$('.highlight', this.$ctx).fadeIn('fast');
        	$('.screen-1', this.$ctx).delay(100).fadeIn('fast');
        	$('.screen-2', this.$ctx).delay(400).fadeIn('fast');
        	$('.screen-3', this.$ctx).delay(500).fadeIn('fast');
        	$('.screen-4', this.$ctx).delay(800).fadeIn('fast');
        }

    });
})(Tc.$);