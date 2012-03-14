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
                if (name) {
                    $.ajax({
                        url: "/api/project/add/",
                        dataType: 'json',
                        type: 'POST',
                        data: 'name=' + encodeURIComponent(name),
                        success: function(data){
                            location.reload();
                        }
                    });
                }
            });
        }
    });
})(Tc.$);