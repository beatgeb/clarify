/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.ModuleLibrary = Tc.Module.extend({
        onBinding: function() {
            var $ctx = this.$ctx;
            $ctx.on('click', '.rename', function() {
                var $rename = $(this),
                    $module = $rename.closest('a'),
                    id = $module.data('id'),
                    name = $module.data('name'),
                    $input = $('<input type="text" value="' + name + '" />');

                $module.append($input)
                $input.on('blur', function() {
                    var $this = $(this);
                    $.ajax({
                        url: "/api/module/rename/" + id + "/" + $this.val(),
                        dataType: 'json',
                        success: function(data){
                            // rename all module instances on the current screen
                            var $layerModule = $('.modLayerModule');
                            $('.measure[data-module=' + data.id + ']', $layerModule).find('.meta').text(data.name);
                            $rename.text(data.name);
                            $module.data('name', data.name);
                            $this.remove();
                        }
                    });
                }).on('keypress', function(e) {
                    // blur on enter
                    if(e.keyCode == '13') {
                        $(this).blur();
                    }
                }).show().focus();
            });
        }
    });
})(Tc.$);