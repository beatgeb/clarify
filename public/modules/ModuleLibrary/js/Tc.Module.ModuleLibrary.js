
(function($) { 
    Tc.Module.ModuleLibrary = Tc.Module.extend({
        on: function(callback) {
            var $ctx = this.$ctx;
            var that = this;
            this.sandbox.subscribe('keyboard', this);
            $ctx.on('click', '.module', function() {
                that.fire('editLibraryModule', $(this).data('id'));
            });
            /*
            $ctx.on('click', '.module', function() {
                var $module = $(this),
                    $rename = $('.rename', $module),
                    $desc = $('.desc', $module),
                    id = $module.data('id'),
                    name = $module.data('name'),
                    $input = $('<input type="text" value="' + name + '" />');

                $desc.hide();

                $rename.prepend($input);
                $input.on('blur', function() {
                    var $this = $(this);
                    $.ajax({
                        url: "/api/module/rename/" + id,
                        dataType: 'json',
                        data: { 'name': $this.val() },
                        success: function(data){
                            // rename all module instances on the current screen
                            var $layerModule = $('.modLayerModule');
                            $('.measure[data-module=' + data.id + ']', $layerModule).find('.meta .desc').text(data.name);
                            $desc.text(data.name).show();
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
            */
           

            callback();
        }
    });
})(Tc.$);