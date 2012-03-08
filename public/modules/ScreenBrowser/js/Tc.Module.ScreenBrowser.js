/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.ScreenBrowser = Tc.Module.extend({
        onBinding: function() {
            var project = this.$ctx.data('project');
            $('.fileupload').fileupload({
                dataType: 'json',
                url: '?view=api&action=screen.upload&project=' + project,
                dropZone: $('.create'),
                done: function (e, data) {
                    window.location.href='/?project=' + project;
                }
            });
            
            $('.delete').bind('click', function(e) {
                var screen = $(this).data('screen');
                var image = $('.image', $(this).parent().parent());
                image.fadeTo('fast', 0.1);
                var confirm = $('<a href="javascript:;" class="delete-confirm">click to delete this screen</a>');
                $(this).parent().parent().append(confirm);
                confirm.on('click', function() {
                    $.ajax({
                        url: "?view=api&action=screen.delete&screen=" + screen,
                        dataType: 'json',
                        success: function(data){
                            $('.screen-' + screen).fadeOut('fast', function() {
                                $(this).remove();
                            });
                        }
                    });
                });
            });
            $('.screen').hover(
                function(e) {
                    $('.delete', $(this)).show();
                },
                function(e) {
                    $('.delete', $(this)).hide();
                }
            );
        }
    });
})(Tc.$);