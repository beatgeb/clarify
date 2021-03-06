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

        templates: [],

        on: function(callback) {
            var $ctx = this.$ctx;
            var that = this;
            var $screens = $('.screens', $ctx);
            var $colors = $('.colors .color', $ctx);

            // initialize project and set id
            var project = $screens.data('project');
            var set = $screens.data('set');

            // compile templates
            this.templates['screen'] = doT.template($('#tmpl-screenbrowser-screen').text());

            // define upload url
            var upload_url = '/api/screen/upload/' + project;
            if (set > 0) {
                upload_url += '/' + set;
            }

            $('.fileupload').fileupload({
                dataType: 'json',
                url: upload_url,
                dropZone: $('.create'),
                add: function (e, data) {
                    var screen = {
                        'title': data.files[0].name,
                        'count_module': 0,
                        'count_measure': 0,
                        'count_font': 0,
                        'count_comment': 0,
                        'count_color': 0
                    };
                    data.context = $(that.templates['screen'](screen)).appendTo($('.screens', $ctx));
                    data.submit();
                },
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('.size', data.context).text(progress + '%');
                },
                done: function (e, data) {
                    if (data.result) {
                        var id = data.result[0].id;
                        var thumbnail_url = data.result[0].thumbnail_url;
                        var screen = {
                            'id': id,
                            'title': data.files[0].name,
                            'count_module': 0,
                            'count_measure': 0,
                            'count_font': 0,
                            'count_comment': 0,
                            'count_color': 0,
                            'thumbnail_url': thumbnail_url,
                            'editable': true,
                            'width': data.result[0].width,
                            'height': data.result[0].height
                        };
                        var $screen = $(that.templates['screen'](screen)).appendTo($('.screens', $ctx));
                        data.context.replaceWith($screen);
                    } else {

                    }
                }
            });

            $('.fileupload-replace').fileupload({
                dataType: 'json',
                dropZone: null,
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

            $ctx.on('click', '.screen .title > a', function(e) {
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

            $('.set .title > a').on('click', function(e) {
                var set = $(this).data('set');
                var name = $(this).data('name');
                var data = { 'set': set, 'name': name };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('rename-set', data, function() {
                    var $input = $(this).closest('.modal').find('.fld-name');
                    $.ajax({
                        url: "/api/set/setting/" + set + "/name/" + encodeURIComponent($input.val()),
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

            $ctx.on('click', '.delete-screen', function(e) {
                var screen = $(this).data('screen');
                var data = { 'screen': screen };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('delete-screen', data, function() {
                    $.ajax({
                        url: "/api/screen/delete/" + screen,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            modal.cancel();
                            $('.screen-' + data.id).fadeOut('fast', function() {
                                $(this).remove();
                            });
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
                $('.intro', this.$ctx).fadeOut('fast');
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