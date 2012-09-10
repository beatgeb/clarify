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
            $('.code', this.$ctx).on('keypress', function(e) {
                if (e.keyPress == 13) {
                    that.auth();
                }
            });
            $('.twitter', this.$ctx).on('click', function(e) {
                that.auth();
            });
            callback();
        },
        
        auth: function() {
            location.href = '/auth/?start=1';
        }
    });
})(Tc.$);