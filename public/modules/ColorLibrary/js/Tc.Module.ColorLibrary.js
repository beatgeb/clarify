/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.ColorLibrary = Tc.Module.extend({        
        onBinding: function() {
            $('a', this.$ctx).tooltip();
        }
    });
})(Tc.$);