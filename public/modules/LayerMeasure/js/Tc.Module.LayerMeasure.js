/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.LayerMeasure = Tc.Module.extend({
        
        active: false,
        resize: false,
        drag: false,
        hover: false,
        
        onBinding: function() {
            var that = this;
            $('.btn-measure').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
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
            $('.btn-measure').removeClass('active');
            $('.picker').hide();
            this.$ctx.empty();
        },
        
        activate: function() {
            this.active = true;
            var that = this;
            var $ctx = this.$ctx;
            var helper;
            $('.btn-measure').addClass('active');
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
                        helper.css({
                            width: w + 'px',
                            height: h + 'px',
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
                        url: "/api/measure/add/" + screen + "/" + sx + "/" + sy + "/" + width + "/" + height,
                        dataType: 'json',
                        success: function(data){
                            helper.remove();
                            that.addMeasure(data.id, data.x, data.y, data.width, data.height);
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
                url: "/api/measure/get/" + screen,
                dataType: 'json',
                success: function(data){
                    $.each(data, function(key, entry) {
                        that.addMeasure(entry.id, entry.x, entry.y, entry.width, entry.height);
                    });
                }
            });
        },
        
        addMeasure: function(id, x, y, width, height) {
            var $ctx = this.$ctx;
            var that = this;
            var label = width + ' x ' + height;
            var measure = $('<div class="measure"><div class="meta">' + label + '</div></div>');
            
            // enable drag and drop for measures
            measure.draggable({
                start: function() {
                    that.drag = true;
                },
                stop: function() {
                    var nx = $(this).position().left;
                    var ny = $(this).position().top;
                    $.ajax({
                        url: "/api/measure/move/" + id + "/" + nx + "/" + ny,
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
                resize: function() {
                    $('.meta', measure).text($(this).width() + ' x ' + $(this).height());
                },
                stop: function() {
                    $.ajax({
                        url: "/api/measure/resize/" + id + "/" + $(this).width() + "/" + $(this).height(),
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
                
            // delete on double click
            measure.bind('dblclick', function(e) {
                $.ajax({
                    url: "/api/measure/delete/" + id,
                    dataType: 'json',
                    success: function(data){
                        measure.remove();
                    }
                });
                that.hover = false;
            });
            
            // set width, height and position of measurement
            measure.css({ 
                left: x + 'px', 
                top: y + 'px', 
                width: width, 
                height: height,
                cursor: 'move',
                position: 'absolute'
            });
            
            $ctx.append(measure);
        }
    });
})(Tc.$);