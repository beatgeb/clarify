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
            var project = this.$ctx.data('project'),
                that = this;
            $('.fileupload').fileupload({
                dataType: 'json',
                url: '/api/screen/upload/' + project,
                dropZone: $('.create'),
                done: function (e, data) {
                    window.location.href='/project/' + project;
                }
            });
            
            $('.delete').on('click', function(e) {
                var $this = $(this);
                e.stopPropagation();
                if ($(this).data('delete')) {
                    that.removeConfirm($this);
                    return;
                }

                $(document).on('click', function() {
                    that.removeConfirm($this);
                });

                $(this).data('delete', true);
                var screen = $(this).data('screen');
                var image = $('.image', $(this).parent().parent());
                image.fadeTo('fast', 0.1);
                var confirm = $('<a href="javascript:;" class="delete-confirm">click to delete this screen</a>');
                $(this).parent().parent().append(confirm);
                confirm.on('click', function() {
                    $.ajax({
                        url: "/api/screen/delete/" + screen,
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
        },
        removeConfirm: function(el) {
            var container = el.parent().parent();
            $('.delete-confirm', container).remove();
            $('.image', container).fadeTo('fast', 1);
            el.data('delete', false);
        }
    });
})(Tc.$);