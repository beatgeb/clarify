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
                send: function(e, data) {
                    // todo: show progress
                },
                done: function (e, data) {
                    window.location.reload();
                }
            });
            
            $('.create').on('click', function(e) {
               $('.fileupload').click();
               e.stopPropagation();
               return false;
            });
            
            $('.delete').on('click', function(e) {
                var screen = $(this).data('screen');
                $('.modal-confirm h3').text('Delete Screen');
                $('.modal-confirm p').html('Do you really want to delete this screen with all of its data?');
                $('.modal-confirm .btn-confirm').text('Delete Screen');
                $('.modal-confirm .btn-confirm').on('click', function() {
                    $.ajax({
                        url: "/api/screen/delete/" + screen,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                    e.stopPropagation();
                    return false;
                });
                $('.modal-confirm').modal();
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