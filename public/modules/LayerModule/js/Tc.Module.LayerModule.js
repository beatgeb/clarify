/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.LayerModule = Tc.Module.extend({
        
        active: false,
        resize: false,
        drag: false,
        hover: false,
        next: 1,
        
        onBinding: function() {
            var that = this;
            $('.btn-modules').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerMeasure').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerColor').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerComment').data('id')).deactivate();
                    that.activate();
                }
            });
        },
        
        deactivate: function() {
            this.active = false;
            $('.modScreen').unbind('click mousemove mouseup mousedown mouseenter mouseleave');
            $('.modScreen').css('cursor', 'auto');
            $('.btn-modules').removeClass('active');
            $('.picker').hide();
            $('.modModuleLibrary').hide();
            this.$ctx.empty();
        },
        
        activate: function() {
            this.active = true;
            var that = this;
            var $ctx = this.$ctx;
            var helper;
            $('.btn-modules').addClass('active');

            $('.modModuleLibrary').show();
            $('.modScreen').eyedrop({
                mode: 'range',
                'display': false,
                start: function(x, y) {
                    if (!that.hover) {
                        $('.measure').hide();
                        helper = $('<div class="measure-helper"></div>');
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
                    var name =  'Module-' + (that.next++);
                    $.ajax({
                        url: "/api/module/add/" + screen + "/" + sx + "/" + sy + "/" + width + "/" + height + "/" + name,
                        dataType: 'json',
                        success: function(data){
                            helper.remove();
                            that.addModule(data.id, data.module, data.x, data.y, data.width, data.height, data.name);
                            var box = $('<a href="javascript:;" title="' + data.name + '" class="module module-' + data.module + '" data-id="' + data.module + '" data-name="' + data.name + '"><div class="rename"><span class="desc">' + data.name + ' </span><i class="icon icon-white icon-pencil"></i></div><img src="' + data.thumbnail + '" /></a>');
                            $('.modModuleLibrary').append(box);
                        }
                    });
                },
                stop: function() {
                    $('.measure', $ctx).show();
                }
            });
            
            this.load();
        },
        
        load: function() {
            var that = this;
            var $ctx = this.$ctx;
            var screen = $('.modScreen').data('screen');
            $ctx.empty();
            $.ajax({
                url: "/api/module/get/" + screen,
                dataType: 'json',
                success: function(data){
                    $.each(data, function(key, entry) {
                        that.addModule(entry.id, entry.module, entry.x, entry.y, entry.width, entry.height, entry.name);
                    });

                    $('.meta', $ctx).each(function() {
                        var $this = $(this),
                            name = $this.text();

                        if(name.indexOf('-') > 0) {
                            var count = parseInt(name.split('-')[1]);
                            if(count > 0) {
                                that.next = ++count;
                            }
                        }
                    });
                }
            });
        },
        
        addModule: function(id, module, x, y, width, height, name) {
            var $ctx = this.$ctx;
            var that = this;
            var measure = $('<div class="measure" data-module="' + module + '"><div class="meta">' + name + '</div></div>');

            // enable drag and drop for measures
            measure.draggable({
                start: function() {
                    that.drag = true;
                },
                stop: function() {
                    var nx = $(this).position().left;
                    var ny = $(this).position().top;
                    $.ajax({
                        url: "/api/module/move/" + id + "/" + nx + "/" + ny,
                        dataType: 'json',
                        success: function(data){
                            // NOOP
                        }
                    });
                    that.drag = false;
                }
            });
            
            measure.resizable({
                handles: 'n, e, s, w, se, ne, nw, sw',
                minHeight: 1,
                minWidth: 1,
                start: function() {
                    that.resize = true;
                },
                stop: function() {
                    $.ajax({
                        url: "/api/module/resize/" + id + "/" + $(this).width() + "/" + $(this).height(),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                        // NOOP
                        }
                    });
                    that.resize = false;
                }
            });
            
            // show / hide picker on hover
            measure.hover(
                function(){
                    $('.picker').hide();
                    that.hover = true;
                }, 
                function(){
                    $('.picker').show();
                    that.hover = false;
                }
            );
                
            // remove on double click
            measure.bind('dblclick', function(e) {
                $.ajax({
                    url: "/api/module/remove/" + id,
                    dataType: 'json',
                    success: function(data){
                        if(data.remove) {
                            // delete the module from the module library
                            $('[data-id=' + data.remove + ']',  $('.modModuleLibrary')).remove();
                        }
                        measure.remove();
                    }
                });
                that.hover = false;
            });

            // set width, height and position of measurement
            measure.css({ 
                left: x + 'px', 
                top: y + 'px', 
                width: width - 2,
                height: height - 2,
                cursor: 'move',
                position: 'absolute'
            });

            $ctx.append(measure);

            if(focus) {
                // focus the input meta field
                $('input', measure).click();
            }
        }
    });
})(Tc.$);