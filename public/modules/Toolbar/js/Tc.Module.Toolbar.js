/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.Toolbar = Tc.Module.extend({
        on: function(callback) {
            this.sandbox.subscribe('keyboard', this);
            /*
            $('.btn-clear').bind('click', 
                function(e) {
                    return;
                    // TODO
                    var screen = $('.modScreen').data('screen');
                    $.ajax({
                        url: "?view=api&action=comment.clear&screen=" + screen,
                        dataType: 'json',
                        success: function(data){
                            $('.modLayerComment > ol').empty();
                        }
                    });
                }
            );
            */
            callback();
        },

        after: function() {
            
        }
    });
})(Tc.$);