
(function($) { 
    Tc.Module.LayerTypography = Tc.Module.extend({
        
        active: false,
        open: null,
        tmpl: null,
        
        on: function(callback) { 
            var that = this;
            var $ctx = this.$ctx;

            this.tmpl = doT.template($('#tmpl-layertypography-tooltip-font').text());

            this.sandbox.subscribe('keyboard', this);

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

            $ctx.on('dblclick', '.font', function(e) {
                var $font = $(this);
                e.stopPropagation();
                $.ajax({
                    url: "/api/typography/data/" + $font.data('id'),
                    dataType: 'json',
                    success: function(data){
                        that.edit(data);
                        return false;
                    }
                });
                return false;
            });

            // bind delete with backspace
            $('html').keydown(function(e){
                that.keydown(e);
            });

            callback();
        },

        after: function() {
            var that = this;
            this.fire('RegisterShortcut', {
                'moduleId': that.id,
                'shortcut': 't',
                'modifier': null,
                'description': 'Switch to typography layer',
                'callback': function() {
                    $('.btn-fonts').click();
                }
            });
        },

        edit: function(data) {
            var that = this;
            var $font = $('.font[data-id="' + data.id + '"]', this.$ctx);
            var modal = that.sandbox.getModuleById($('.modModal').data('id'));
            modal.open('font-edit', data, function() {
                var $modal = $(this).closest('.modal');
                var request = {
                    'font': {
                        'id': $font.data('id'),
                        'name': $modal.find('.fld-name').val(),
                        'family': $modal.find('.fld-font-family').val(),
                        'color': $modal.find('.fld-font-color').val(),
                        'color_hover': $modal.find('.fld-hover-font-color').val(),
                        'color_active': $modal.find('.fld-active-font-color').val(),
                        'size': $modal.find('.fld-font-size').val(),
                        'line_height': $modal.find('.fld-font-line-height').val()
                    }
                };

                // preprocess data
                request.font.size.substring(-2) == 'px' ? request.font.size : request.font.size + 'px';

                $.ajax({
                    url: "/api/typography/update/",
                    dataType: 'json',
                    data: JSON.stringify(request),
                    type: 'POST',
                    success: function(data){
                        $font.find('.preview .name').text(request.font.family);
                        $font.find('.preview .size').text(request.font.size);
                        $font.find('.preview .line-height').text(request.font.line_height);
                        $font.find('.preview .name').css('fontFamily', request.font.family);
                        $font.find('.preview .name').css('fontSize', request.font.size);
                        $font.find('.preview .name').css('color', request.font.color);

                        if (request.font.color && request.font.color != '') {
                            $font.find('.color-normal .box').css('backgroundColor', request.font.color);
                            $font.find('.color-normal .name').text(data.font.color_name);
                            $font.find('.color-normal .hex').text(request.font.color);
                            $font.find('.color-normal').fadeIn();
                        } else {
                            $font.find('.color-normal').fadeOut();
                        }
                        
                        if (request.font.color_hover && request.font.color_hover != '') {
                            $font.find('.color-hover .box').css('backgroundColor', request.font.color_hover);
                            $font.find('.color-hover .name').text(data.font.color_hover_name);
                            $font.find('.color-hover .hex').text(request.font.color_hover);
                            $font.find('.color-hover').fadeIn();
                        } else {
                            $font.find('.color-hover').fadeOut();
                        }
                        
                        if (request.font.color_active && request.font.color_active != '') {
                            $font.find('.color-active .box').css('backgroundColor', request.font.color_active);
                            $font.find('.color-active .name').text(data.font.color_active_name);
                            $font.find('.color-active .hex').text(request.font.color_active);
                            $font.find('.color-active').fadeIn();
                        } else {
                            $font.find('.color-active').fadeOut();
                        }
                        
                        modal.cancel();
                        that.drag = false;
                        return false;
                    }
                });
                that.drag = false;
                return false;
            }, function() {
                $.ajax({
                    url: "/api/typography/delete/" + $font.data('id'),
                    dataType: 'json',
                    success: function(data){
                        $font.remove();
                        modal.cancel();
                        that.drag = false;
                    }
                });
                return false;
            });
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
                display: false,
                show: false,
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
                            that.edit({ "id": data.id });
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

        keydown: function(e) {
            if (!this.active) {
                return;
            }
            if (e.srcElement.localName == 'textarea' || e.srcElement.localName == 'input' || e.srcElement.localName == 'select') { 
                return;
            }
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
                    url: "/api/typography/move/" + $(item).data('id') + "/" + new_x + "/" + new_y,
                    dataType: 'json',
                    type: 'POST',
                    success: function(data){ 

                    }
                });
            });
        },

        remove: function() {
            var that = this;
            $.each(that.selected, function(key, item) {
                var id = $(item).data('id');
                $.ajax({
                    url: "/api/typography/delete/" + id,
                    dataType: 'json',
                    success: function(data){
                        $(item).remove();
                        that.selected = [];
                        that.hover = false;
                        if(data.remove) {
                            // delete the module from the module library
                            //$('[data-id=' + data.remove + ']',  $('.modModuleLibrary')).remove();
                        }
                    }
                });
            });
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
            var font_name = font.name ? font.name : 'Untitled';
            var font_family = font.family ? font.family : 'Default Font';
            var data = {
                'id': font.id,
                'font_family': font_family,
                'font_name': font_name,
                'line_height': line_height,
                'color_hex': font.color_hex,
                'color_name': font.color_name,
                'size': size,
                'color_hover_hex': font.color_hover_hex,
                'color_hover_name': font.color_hover_name,
                'color_active_hex': font.color_active_hex,
                'color_active_name': font.color_active_name
            };
            var $font = $(this.tmpl(data));

            // don't propagate mousedown event to avoid further actions
            $font.on('mousedown', function(e) {
                $('.picker').hide();
                that.hover = true;
                var selected = !$font.data('selected');
                var $fonts = $('.font', $ctx);
                $fonts.data('selected', false);
                $fonts.removeClass('selected');
                $fonts.resizable('disable');
                $font.data('selected', selected);
                $font.addClass('selected');
                $font.resizable('enable');
                that.selected = [ $font ];
                e.stopPropagation();
            });

            // enable drag and drop for fonts
            $font.draggable({
                distance: 10,
                cancel: '.meta',
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
            
            $font.resizable({
                disabled: true,
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
            $font.hover(
                function(){
                    that.hover = true;
                },
                function(){
                    that.hover = false;
                }
            );

            // set width, height and position of font
            $font.css({
                left: x + 'px',
                top: y + 'px',
                width: width,
                height: height,
                position: 'absolute'
            });

            $ctx.append($font);
        }

    });
})(Tc.$);