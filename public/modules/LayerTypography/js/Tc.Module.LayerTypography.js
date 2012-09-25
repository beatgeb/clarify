/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.LayerTypography = Tc.Module.extend({
        
        active: false,
        open: null,
        
        on: function(callback) { 
            var that = this;
            var $ctx = this.$ctx;
            $('.btn-fonts').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerModule').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerColor').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerMeasure').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerComment').data('id')).deactivate();
                    that.activate();
                }
            });
            callback();
        },
        
        deactivate: function() {
            var $ctx = this.$ctx;
            $ctx.empty();
            $('.screen').unbind('click');
            $('.screen').unbind('dblclick');
            $('.btn-fonts').removeClass('active');
            this.active = false;
        },
        
        activate: function() {
            
            var $ctx = this.$ctx;
            var that = this;
            var helper;
            var screen = $('.modScreen').data('screen');
            var layer = $('.modScreen').data('layer');
            
            $ctx.empty();
            this.active = true;

            $('.btn-fonts').addClass('active');

            $('.modScreen').eyedrop({
                mode: 'range',
                'display': false,
                start: function(x, y) {
                    if (!that.hover) {
                        $('.font').hide();
                        helper = $('<div class="font-helper"></div>');
                        helper.css({
                            left: x + 'px',
                            top: y + 'px'
                        });
                        $ctx.append(helper);
                    }
                },
                picking: function(x, y, w, h) {
                    if (!that.hover && helper) {
                        var width = w - 2;
                        var height = h - 2;
                        helper.css({
                            width: width + 'px',
                            height: height + 'px',
                            left: x + 'px',
                            top: y + 'px'
                        });
                    }
                },
                pickRange: function(sx, sy, scolor, ex, ey, ecolor) {
                    if (that.resize || that.drag) {
                        return;
                    }
                    var height = ey > sy ? ey - sy : sy - ey;
                    var width = ex > sx ? ex - sx : sx - ex;
                    var screen = $('.modScreen').data('screen');
                    sx = ex > sx ? sx : ex;
                    sy = ey > sy ? sy : ey;
                    height++;
                    width++;
                    if (width == 1 && height == 1) {
                        return;
                    }
                    $.ajax({
                        url: "/api/typography/add/" + screen + "/" + sx + "/" + sy + "/" + width + "/" + height,
                        dataType: 'json',
                        success: function(data){
                            helper.remove();
                            that.addFont(data);
                        }
                    });
                },
                stop: function() {
                    $('.font', $ctx).show();
                }
            });
            
            // initially load comments
            this.load();
            
        },
        
        load: function() {
            var that = this;
            var screen = $('.modScreen').data('screen');
            $.ajax({
                url: "/api/typography/get/" + screen,
                dataType: 'json',
                success: function(data){
                    $.each(data, function(key, font) {
                        that.addFont(font);
                    });
                }
            });
        },
        
        addFont: function(font) {
            var $ctx = this.$ctx;
            var that = this;
            var id = font.id;
            var x = font.x;
            var y = font.y;
            var width = font.width;
            var height = font.height;
            var size = font.size ? font.size + 'px' : 'Default';
            var line_height = font.line_height ? font.line_height : '1';
            var color_name = font.color_name ? font.color_name : 'Default';
            var color_hex = font.color_hex ? '#' + font.color_hex : '#000000';
            var font_name = font.name ? font.name : 'Untitled';
            var font_family = font.family ? font.family : 'Default Font';
            var font = $('<div class="font"><div class="meta"><div class="title">' + font_name + '</div><div>' + font_family + '&nbsp;&nbsp;<i class="icon icon-resize-vertical"></i> <strong>' + size + '</strong></div><div><span class="color" style="background: ' + color_hex + '"></span> ' + color_hex + ', ' + color_name + '</div></div></div>');
            
            // enable drag and drop for fonts
            font.draggable({
                start: function() {
                    that.drag = true;
                },
                stop: function() {
                    var nx = $(this).position().left;
                    var ny = $(this).position().top;
                    $.ajax({
                        url: "/api/typography/move/" + id + "/" + nx + "/" + ny,
                        dataType: 'json',
                        success: function(data){
                            // NOOP
                        }
                    });
                    that.drag = false;
                }
            });
            
            font.resizable({
                handles: 'n, e, s, w, se, ne, nw, sw',
                minHeight: 1,
                minWidth: 1,
                start: function() {
                    that.resize = true;
                },
                stop: function(e, ui) {
                    $.ajax({
                        url: "/api/typography/resize/" + id + "/" + ui.size.width + "/" + ui.size.height,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            // if the position of the element has changed
                            if (ui.originalPosition.top != ui.position.top || 
                                ui.originalPosition.left != ui.position.left) {
                                $.ajax({
                                    url: "/api/typography/move/" + id + "/" + ui.position.left + "/" + ui.position.top,
                                    dataType: 'json',
                                    type: 'POST',
                                    success: function(data){
                                        // NOOP
                                    }
                                });
                            }
                        }
                    });
                    that.resize = false;
                }
            });
            
            // show / hide picker on hover
            font.hover(
                function(){
                    $('.picker').hide();
                    that.hover = true;
                }, 
                function(){
                    $('.picker').show();
                    that.hover = false;
                }
            );
             
            // delete on double click
            font.bind('dblclick', function(e) {
                $.ajax({
                    url: "/api/typography/delete/" + id,
                    dataType: 'json',
                    success: function(data){
                        font.remove();
                    }
                });
                that.hover = false;
            });
            
            // set width, height and position of font
            font.css({ 
                left: x + 'px', 
                top: y + 'px', 
                width: width, 
                height: height,
                cursor: 'move',
                position: 'absolute'
            });
            
            $ctx.append(font);
        }

    });
})(Tc.$);