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
        selected: [],
        
        on: function(callback) {
            var that = this;
            $('.btn-measure').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerModule').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerColor').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerComment').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerTypography').data('id')).deactivate();
                    that.activate();
                }
            });

            // bind delete with backspace
            $('html').keydown(function(e){
                that.keydown(e);
            });
            
            // subscribe to the keyboard-channel
            this.sandbox.subscribe('keyboard', this);

            callback();
        },

        after: function() {
            var that = this;
            // bind layer switcher to m
            this.fire('RegisterShortcut', {
                'moduleId': that.id,
                'shortcut': 'm',
                'modifier': null,
                'description': 'Switch to measure layer',
                'callback': function() {
                    $('.btn-measure').click();
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

        keydown: function(e) {
            var that = this;
            switch (e.keyCode) {
                case 8: // backspace
                case 46: // delete
                    that.remove();
                    e.preventDefault();
                    return false;
                case 39: // arrow right
                    that.move(1, 0);
                    e.preventDefault();
                    return false;
                case 40: // arrow down
                    that.move(0, 1);
                    e.preventDefault();
                    return false;
                case 38: // arrow up
                    that.move(0, -1);
                    e.preventDefault();
                    return false;
                case 37: // arrow left
                    that.move(-1, 0);
                    e.preventDefault();
                    return false;
            }
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

        // move selected measures by defined x and y offsets
        move: function(x, y) {
            var that = this;
            $.each(that.selected, function(key, item) {
                var new_x = $(item).position().left + x;
                var new_y = $(item).position().top + y;
                $(item).css({
                    left: new_x + 'px',
                    top: new_y + 'px'
                });
                $.ajax({
                    url: "/api/measure/move/" + $(item).data('id') + "/" + new_x + "/" + new_y,
                    dataType: 'json',
                    type: 'POST',
                    success: function(data){ }
                });
            });
        },

        remove: function() {
            var that = this;
            $.each(that.selected, function(key, item) {
                var id = $(item).data('id');
                $.ajax({
                    url: "/api/measure/delete/" + id,
                    dataType: 'json',
                    success: function(data){
                        $(item).remove();
                        that.selected = [];
                        that.hover = false;
                    }
                });
            });
        },
        
        addMeasure: function(id, x, y, width, height) {
            var $ctx = this.$ctx;
            var that = this;
            var label = width + ' x ' + height;
            var measure = $('<div class="measure"><div class="meta">' + label + '</div><div class="handle handle-ne"></div><div class="handle handle-n"></div><div class="handle handle-nw"></div><div class="handle handle-e"></div><div class="handle handle-w"></div><div class="handle handle-se"></div><div class="handle handle-s"></div><div class="handle handle-sw"></div></div>');
            
            // set id attribute
            measure.data('id', id);

            // don't propagate mousedown event to avoid further actions
            measure.on('mousedown', function(e) {
                $('.picker').hide();
                that.hover = true;
                var selected = !measure.data('selected');
                $('.measure', $ctx).data('selected', false);
                $('.measure', $ctx).removeClass('selected');
                $('.measure', $ctx).resizable('disable');
                measure.data('selected', selected);
                measure.addClass('selected');
                measure.resizable('enable');
                that.selected = [ measure ];
                e.stopPropagation();
            });


            // enable drag and drop for measures
            measure.draggable({
                distance: 10,
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
            
            // enable resizing of measures
            measure.resizable({
                disabled: true,
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
                            width = ui.size.width;
                            height = ui.size.height;
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

            measure.on('dblclick', function(e) {
                if ($(this).is('.ui-draggable-dragging')) {
                    return;
                }
                var data = { 'width': width, 'height': height };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('edit-dimension', data, function() {
                    var new_width = $(this).closest('.modal').find('.fld-width').val();
                    var new_height = $(this).closest('.modal').find('.fld-height').val();
                    $.ajax({
                        url: "/api/measure/resize/" + id + "/" + new_width + "/" + new_height,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            measure.css({
                                width: new_width,
                                height: new_height
                            });
                            width = new_width;
                            height = new_height;
                            $('.meta', measure).text(new_width + ' x ' + new_height);
                            modal.cancel();
                        }
                    });
                }, function() {
                    $.ajax({
                        url: "/api/measure/delete/" + id,
                        dataType: 'json',
                        success: function(data){
                            measure.remove();
                            modal.cancel();
                        }
                    });
                });
                e.stopPropagation();
                return false;
            });
           
            // set width, height and position of measurement
            measure.css({
                left: x + 'px',
                top: y + 'px',
                width: width,
                height: height,
                position: 'absolute'
            });
            
            $ctx.append(measure);
        }
    });
})(Tc.$);