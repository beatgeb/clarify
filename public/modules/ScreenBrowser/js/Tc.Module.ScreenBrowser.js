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

        on: function(callback) {
            var project = this.$ctx.data('project'),
                $ctx = this.$ctx,
                that = this;

            var $modal = $('.modal-confirm');
            var $colors = $('.colors .color', $ctx);

            $('.fileupload').fileupload({
                dataType: 'json',
                url: '/api/screen/upload/' + project,
                dropZone: $('.create'),
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('.create').find('.meta').text(progress + '%');
                },
                done: function (e, data) {
                    window.location.reload();
                }
            });

            $('.fileupload-replace').fileupload({
                dataType: 'json',
                url: '/api/screen/replace/',
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('.create').find('.meta').text(progress + '%');
                },
                done: function (e, data) {
                    window.location.reload();
                }
            });
            
            // replace action
            $('.btn-replace').on('click', function(e) {
                $('.fileupload-replace').fileupload('option', 'url', '/api/screen/replace/' + $(this).data('screen'));
                $('.fileupload-replace').click();
                e.stopPropagation();
                return false;
            });

            $('.create').on('click', function(e) {
                that.screen = null;
                $('.fileupload').click();
                e.stopPropagation();
                return false;
            });
            $('.title > a').on('click', function(e) {
                var screen = $(this).data('screen');
                var title = $(this).data('title');
                $('.modal-body').find('input').remove();
                $('.modal-confirm h3').text('Rename Screen');
                $('.modal-confirm p').html('New title for this screen:');
                var input = $('<input class="fld" type="text" value="' + title + '" />');
                $('.modal-body').append(input);
                $('.modal-confirm .btn-confirm').text('Rename Screen');
                $('.modal-confirm .btn-confirm').on('click', function() {
                    $.ajax({
                        url: "/api/screen/setting/" + screen + "/title/" + encodeURIComponent(input.val()),
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
                input.focus();
                return false;
            })
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

            $('.btn-delete-project').on('click', function() {
                var project = $(this).data('project');
                $('.modal-confirm h3').text('Delete Project');
                $('.modal-confirm p').html('Do you really want to delete this project with all of its data?');
                $('.modal-confirm .btn-confirm').text('Delete Project');
                $('.modal-confirm .btn-confirm').on('click', function() {
                    $.ajax({
                        url: "/api/project/delete/" + project,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.href='/';
                        }
                    });
                    e.stopPropagation();
                    return false;
                });
                $('.modal-confirm').modal();
            });
            
            $('.btn-rename-project').on('click', function() {
                var project = $(this).data('project');
                var name = $(this).data('name');
                $('.modal-body').find('input').remove();
                $('.modal-confirm h3').text('Rename Project');
                $('.modal-confirm p').html('New title for this project:');
                var input = $('<input class="fld" type="text" value="' + name + '" />');
                $('.modal-body').append(input);
                $('.modal-confirm .btn-confirm').text('Rename Project');
                $('.modal-confirm .btn-confirm').on('click', function() {
                    $.ajax({
                        url: "/api/project/setting/" + project + "/name/" + encodeURIComponent(input.val()),
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
                input.focus();
                return false;
            });

            $('.btn-export-css').on('click', function(e) {
                var colors = '';
                $colors.each(function(){
                     colors += $(this).data('less')+"\r\n"; 
                });

                $('h3', $modal).text('Copy & Paste the following LESS Template');
                $('p', $modal).empty();

                var code = $('<pre></pre>').text(colors);
                $('p', $modal).append($('<span>Here are all your colors that you have specified for this project:</span>'));
                $('p', $modal).append(code);
                $('.btn-confirm', $modal).text('Close');
                $('.btn-confirm', $modal).on('click', function() {
                    $modal.modal('hide');
                    e.stopPropagation();
                    return false;
                });
                $modal.modal();
            });

            $('.btn-export-sass').on('click', function(e) {
                var colors = '';
                $colors.each(function(){
                     colors += $(this).data('sass')+"\r\n"; 
                });

                $('h3', $modal).text('Copy & Paste the following Sass Template');
                $('p', $modal).empty();

                var code = $('<pre></pre>').text(colors);
                $('p', $modal).append($('<span>Here are all your colors that you have specified for this project:</span>'));
                $('p', $modal).append(code);
                $('.btn-confirm', $modal).text('Close');
                $('.btn-confirm', $modal).on('click', function() {
                    $modal.modal('hide');
                    e.stopPropagation();
                    return false;
                });
                $modal.modal();
            });

            $('.btn-create-project', $ctx).on('click', function() {
                $(this).fadeOut('fast');
                $('.add-project').slideToggle('fast', function() {
                    $('.project-name').focus();
                });
            });

            $('.color', this.$ctx).tooltip();

            callback();
        }
    });
})(Tc.$);