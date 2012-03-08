/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.ProjectBrowser = Tc.Module.extend({
        onBinding: function() {
            $('.btn-add-project', this.$ctx).bind('click', function() {
                var name = prompt('name',''); 
                if (name != 'null' && name != '') {
                    $.ajax({
                        url: "?view=api&action=project.add&name=" + name,
                        dataType: 'json',
                        success: function(data){
                            location.reload();
                        }
                    });
                }
            });
        }
    });
})(Tc.$);