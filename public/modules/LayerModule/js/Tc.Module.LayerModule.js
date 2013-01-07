
(function($) { 
    Tc.Module.LayerModule = Tc.Module.extend({
        
        active: false,
        resize: false,
        drag: false,
        hover: false,
        next: 1,
        selected: [],
        
        on: function(callback) {
            var that = this;

            this.sandbox.subscribe('keyboard', this);
            
            $('.btn-modules').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerMeasure').data('id')).deactivate();
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
            
            callback();
        },

        after: function() {
            var that = this;
            this.fire('RegisterShortcut', {
                'moduleId': that.id,
                'shortcut': 'o',
                'modifier': null,
                'description': 'Switch to modules layer',
                'callback': function() {
                    $('.btn-modules').click();
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
            var $library = $('.modModuleLibrary');
            var $modules = $('.module', $library);
            $('.btn-modules').addClass('active');

            // load modules
            this.load();

            // fill module library
            $.each($modules, function() {
                that.addLibraryModule($(this));
            });

            // show module library
            $library.show();

            $('.modScreen').eyedrop({
                mode: 'range',
                'display': false,
                start: function(x, y) {
                    if (!that.hover && !that.drag) {
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
                        var width = w;
                        var height = h;
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
                            var box = $('<a href="javascript:;" title="' + data.name + '" class="module module-' + data.module + '" data-id="' + data.module + '" data-name="' + data.name + '"><div class="rename"><span class="desc">' + data.name + ' </span> <i class="icon icon-white icon-pencil"></i></div><img src="' + data.thumbnail + '" /></a>');
                            $('.modModuleLibrary .scroller').prepend(box);
                            that.addLibraryModule(box);
                        }
                    });
                },
                stop: function() {
                    $('.measure', $ctx).show();
                }
            });
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
                    url: "/api/module/move/" + $(item).data('id') + "/" + new_x + "/" + new_y,
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
                    url: "/api/module/remove/" + id,
                    dataType: 'json',
                    success: function(data){
                        $(item).remove();
                        that.selected = [];
                        that.hover = false;
                        if(data.remove) {
                            // delete the module from the module library
                            $('[data-id=' + data.remove + ']',  $('.modModuleLibrary')).remove();
                        }
                    }
                });
            });
        },

        addModule: function(id, module, x, y, width, height, name) {
            var $ctx = this.$ctx;
            var that = this;
            var measure = $('<div class="measure" data-module="' + module + '"><div class="meta"><span class="desc">' + name + '</span> <a href="#" title="recapture" class="screenshot"><i class="icon icon-white icon-camera"></i></a></div></div>');

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
                disabled: true,
                handles: 'n, e, s, w, se, ne, nw, sw',
                minHeight: 1,
                minWidth: 1,
                start: function() {
                    that.resize = true;
                },
                stop: function(e, ui) {
                    $.ajax({
                        url: "/api/module/resize/" + id + "/" + $(this).width() + "/" + $(this).height(),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            // if the position of the element has changed
                            if (ui.originalPosition.top != ui.position.top ||
                                ui.originalPosition.left != ui.position.left) {
                                $.ajax({
                                    url: "/api/module/move/" + id + "/" + ui.position.left + "/" + ui.position.top,
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
                that.onEditModule($(this).data('id'));
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

            $('.screenshot', measure).on('click', function() {
                var $module = $(this).closest('.measure'),
                    id = $module.data('module'),
                    x = parseInt($module.css('left')),
                    y = parseInt($module.css('top')),
                    width = parseInt($module.css('width')),
                    height = parseInt($module.css('height')),
                    screen = $('.modScreen').data('screen');

                $.ajax({
                    url: "/api/module/recapture/" + screen + "/" + x + "/" + y + "/" + width + "/" + height  + "/"  + id,
                    dataType: 'json',
                    success: function(data){
                        // reload img
                        var $img = $('[data-id=' + data.id + '] img',  $('.modModuleLibrary'));
                        var timestamp = new Date().getTime();
                        $img.attr('src', data.thumbnail + '?time=' + timestamp);
                    }
                });

                return false;
            });

            $ctx.append(measure);

            if(focus) {
                // focus the input meta field
                $('input', measure).click();
            }
        },

        onEditLibraryModule: function(id) {
            var that = this;
            var $ctx = this.$ctx;
            $.ajax({
                url: "/api/module/library/" + id,
                dataType: 'json',
                success: function(data){
                    var module = data.id;
                    $module = $('.modModuleLibrary .module[data-id=' + module + ']');
                    var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                    modal.open('edit-library-module', data, function() {
                        var name = $(this).closest('.modal').find('.fld-name').val();
                        $.ajax({
                            url: "/api/module/rename/" + module,
                            dataType: 'json',
                            data: { 'name': name },
                            success: function(data){
                                $('.measure[data-module=' + module + ']', $ctx).find('.meta .desc').text(data.name);
                                $('.desc', $module).text(data.name).show();
                                $module.data('name', data.name);
                                modal.cancel();
                            }
                        });
                    });
                }
            });
        },

        onEditModule: function(id) {
            var that = this;
            var $ctx = this.$ctx;
            $.ajax({
                url: "/api/module/data/" + id,
                dataType: 'json',
                success: function(data){
                    var module = data.module;
                    $module = $('.modModuleLibrary .module[data-id=' + module + ']');
                    var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                    modal.open('edit-module', data, function() {
                        var name = $(this).closest('.modal').find('.fld-name').val();
                        $.ajax({
                            url: "/api/module/rename/" + module,
                            dataType: 'json',
                            data: { 'name': name },
                            success: function(data){
                                $('.measure[data-module=' + module + ']', $ctx).find('.meta .desc').text(data.name);
                                $('.desc', $module).text(data.name).show();
                                $module.data('name', data.name);
                                modal.cancel();
                            }
                        });
                    }, function() {
                        $.ajax({
                            url: "/api/module/remove/" + id,
                            dataType: 'json',
                            success: function(data){
                                if(data.remove) {
                                    // delete the module from the module library
                                    $('[data-id=' + data.remove + ']',  $('.modModuleLibrary')).remove();
                                }
                                measure.remove();
                                modal.cancel();
                            }
                        });
                    });
                }
            });
        },

        addLibraryModule: function(module) {
            var that = this;
            module.draggable({
                helper: "clone",
                revert: "true",
                cursorAt: {top: 0, left: -20},
                start: function(e) {
                    that.drag = true;
                },
                stop: function(e) {
                    var offset = $('.modScreen').offset();
                    var x = e.pageX - offset.left;
                    var y = e.pageY - offset.top;
                    $.ajax({
                        url: "/api/module/add/" + $('.modScreen').data('screen') + "/" + x + "/" + y + "/100/100/" + module.data('id'),
                        dataType: 'json',
                        success: function(data){
                            that.addModule(data.id, data.module, data.x, data.y, data.width, data.height, data.name);
                            that.drag = false;
                        }
                    });
                }
            });
        }
    });
})(Tc.$);