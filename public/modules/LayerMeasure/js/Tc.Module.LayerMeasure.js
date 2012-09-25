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
        
        on: function(callback) {
            var that = this;
            $('.btn-measure').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerModule').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerColor').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerComment').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerFont').data('id')).deactivate();
                    that.activate();
                }
            });
            callback();
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
                        helper = $('<div class="measure-helper"><div class="meta">0 x 0</div></div>');
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
                        helper.find('.meta').text(w + ' x ' + h);
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
                resize: function(e, ui) {
                    $('.meta', measure).text(ui.size.width + ' x ' + ui.size.height);
                },
                stop: function(e, ui) {
                    $.ajax({
                        url: "/api/measure/resize/" + id + "/" + ui.size.width + "/" + ui.size.height,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            // if the position of the element has changed
                            if (ui.originalPosition.top != ui.position.top || 
                                ui.originalPosition.left != ui.position.left) {
                                $.ajax({
                                    url: "/api/measure/move/" + id + "/" + ui.position.left + "/" + ui.position.top,
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