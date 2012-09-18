/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.Auth = Tc.Module.extend({        
        on: function(callback) {
            var that = this;
            $('.password', this.$ctx).on('keypress', function(e) {
                if (e.keyCode == 13) {
                    that.authClarify();
                }
            });
            $('.btn-signin', this.$ctx).on('click', function(e) {
                that.authClarify();
            });
            $('.btn-twitter', this.$ctx).on('click', function(e) {
                that.authTwitter();
            });
            callback();
        },

        authClarify: function() {
            $('.btn-signin').addClass('btn-disabled');
        },
        
        authTwitter: function() {
            $('.btn-twitter').addClass('btn-disabled');
            location.href = '/auth/?start=1';
        }
    });
})(Tc.$);