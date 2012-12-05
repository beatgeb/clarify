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
                var data = { 'screen': screen, 'name': title };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('rename-screen', data, function() {
                    var $input = $(this).closest('.modal').find('.fld-name');
                    $.ajax({
                        url: "/api/screen/setting/" + screen + "/title/" + encodeURIComponent($input.val()),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                });
                return false;
            });

            $('.btn-create-set').on('click', function(e) {
                var project = $(this).data('project');
                var data = { };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('create-set', data, function() {
                    var $input = $(this).closest('.modal').find('.fld-name');
                    $.ajax({
                        url: "/api/set/create/" + project + "/" + encodeURIComponent($input.val()),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                });
                return false;
            });

            $('.btn-account-settings').on('click', function(e) {
                var data = { 'name': $(this).data('name') };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('account-settings', data, function() {
                    var $input = $(this).closest('.modal').find('.fld-name');
                    $.ajax({
                        url: "/api/user/setting/name/" + encodeURIComponent($input.val()),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                }, function() {
                    alert('Not possible yet.');
                    /*
                    $.ajax({
                        url: "/api/user/account/delete",
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                     */
                });
                return false;
            });

            $('.delete-screen').on('click', function(e) {
                var screen = $(this).data('screen');
                var data = { 'screen': screen };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('delete-screen', data, function() {
                    $.ajax({
                        url: "/api/screen/delete/" + screen,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                });
                return false;
            });

            $('.delete-set').on('click', function(e) {
                var set = $(this).data('set');
                var data = { 'set': set };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('delete-set', data, function() {
                    $.ajax({
                        url: "/api/set/delete/" + set,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                });
                return false;
            });
            
            $('.btn-project-settings').on('click', function() {
                var project = $(this).data('project');
                var name = $(this).data('name');
                var screen_background_color = $(this).data('screen-background-color');
                var data = {
                    'project': project,
                    'name': name,
                    'screen_background_color': screen_background_color ? screen_background_color : '#FFFFFF'
                };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('project-settings', data, function() {
                    var $input = $(this).closest('.modal').find('.fld-name');
                    $.ajax({
                        url: "/api/project/setting/" + project + "/name/" + encodeURIComponent($input.val()),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.reload();
                        }
                    });
                }, function() {
                    modal.open('delete-project', data, function() {
                        $.ajax({
                            url: "/api/project/delete/" + project,
                            dataType: 'json',
                            type: 'POST',
                            success: function(data){
                                location.href='/';
                            }
                        });
                    });
                });
                return false;
            });

            $('.btn-export-css').on('click', function(e) {
                var colors = '';
                $colors.each(function(){
                     colors += $(this).data('less')+"\r\n";
                });
                var data = { 'code': colors };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('export-color-library-less', data, function() {});
                return false;
            });

            $('.btn-export-sass').on('click', function(e) {
                var colors = '';
                $colors.each(function(){
                     colors += $(this).data('sass')+"\r\n";
                });
                var data = { 'code': colors };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('export-color-library-sass', data, function() {});
                return false;
            });

            $('.btn-create-project', $ctx).on('click', function() {
                $(this).fadeOut('fast');
                $('.add-project').slideToggle('fast', function() {
                    $('.project-name').focus();
                });
            });

            $('.color', this.$ctx).tooltip();


            $(".screen", $ctx).draggable({ 
                handle: "img", 
                cursor: "move", 
                cursorAt: { top: 50, left: 50 },
                revert: "invalid",
                zIndex: 10,
                opacity: 0.9, 
                helper: function(event) {
                    return $(this).find('img').clone();
                }
            });
            $(".set", $ctx).droppable({
                hoverClass: 'ui-state-hover',
                drop: function(event, ui) {
                    var $count = $(this).find('.screen_count');
                    var $image1 = $(this).find('.image-1');
                    var $image2 = $(this).find('.image-2');
                    var set = $(this).data('id');
                    var screen = ui.draggable.data('id');
                    $.ajax({
                        url: "/api/set/add/" + set + "/" + screen,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            $image2.fadeOut(function() {
                                $image2.attr('src', data.set.image);
                                $image2.fadeIn();
                            });
                            $image1.fadeOut(function() {
                                $image1.attr('src', data.set.image);
                                $image1.fadeIn('fast');
                            });
                            $count.text(data.set.screen_count + " Screen(s)");
                            $('.screen-' + screen).fadeOut();
                        }
                    });
                }
            });

            callback();
        }
    });
})(Tc.$);