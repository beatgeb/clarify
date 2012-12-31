
(function($) { 
    Tc.Module.LayerColor = Tc.Module.extend({
        
        active: false,
        drag: false,
        hover: false,
        
        on: function(callback) {
            var that = this;

            this.sandbox.subscribe('keyboard', this);

            $('.btn-color').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerModule').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerComment').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerMeasure').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerTypography').data('id')).deactivate();
                    that.activate();
                }
            });
            callback();
        },
        
        load: function() {
            var that = this;
            var screen = $('.modScreen').data('screen');
            $.ajax({
                url: "/api/color/get/" + screen,
                dataType: 'json',
                success: function(data){
                    $.each(data, function(key, color) {
                        that.addColor(color, false);
                    });
                }
            });
        },

        after: function() {
            var that = this;
            this.fire('RegisterShortcut', {
                'moduleId': that.id,
                'shortcut': 'i',
                'modifier': null,
                'description': 'Switch to colors layer',
                'callback': function() {
                    $('.btn-color').click();
                }
            });
        },

        activate: function() {
            this.fire('layerActivated', 'color');
            var that = this;
            this.active = true;
            
            var screen = $('.modScreen');
            $('.btn-color').addClass('active');

            $('.modColorLibrary').show();
            $('.modScreen').eyedrop({
                'display': true,
                'mode': 'point',
                pick: function(x, y, color) {
                    if (that.drag || that.hover) {
                        return;
                    }
                    $.ajax({
                        url: "/api/color/add/" + screen.data('screen') + "/" + x + "/" + y + "/" + color.r + "/" + color.g + "/" + color.b + "/" + color.a + "/" + encodeURIComponent(color.hex.substring(1)),
                        dataType: 'json',
                        success: function(data){
                            if (data.result == 'NEW') {
                                that.addLibraryColor(data);
                            }
                            that.addColor(data, true);
                        }
                    });
                }
            });
            this.load();
        },
        
        deactivate: function() {
            this.fire('layerDeactivated', 'color');
            this.active = false;
            this.$ctx.empty();
            $('.modColorLibrary').hide();
            $('.modScreen').unbind('click mousemove mouseup mousedown mouseenter mouseleave');
            $('.modScreen').css('cursor', 'auto');
            $('.btn-color').removeClass('active');
            $('.picker').hide();
        },
        
        addLibraryColor: function(data) {
            var that = this;
            this.fire('colorAdded', data);
        },
        
        addColor: function(color, fade) {
            var that = this;
            var slug = color.name_css;
            var label = color.name;
            var hex = '#' + color.hex;
            if (!label) {
                label = hex;
            }
            var $meta = $('<a href="#" class="meta" data-slug="' + slug + '" data-hex="' + hex + '" data-name="' + color.name + '"><span class="name">' + label + '</span><br /><span class="hex">#' + color.hex + '</span></a>');
            var $element = $('<div class="color" data-project-color="' + color.color + '"><div class="p"></div></div>');

            $element.on('click', function() {
                if ($(this).is('.ui-draggable-dragging')) {
                    return;
                }
                var $meta = $(this).find('.meta');
                var data = {
                    'hex': $meta.data('hex').toUpperCase(),
                    'name': $meta.data('name'),
                    'slug': $meta.data('slug')
                };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('color-edit', data, function() {
                    var $name = $(this).closest('.modal').find('.fld-name');
                    var $hex = $(this).closest('.modal').find('.fld-hex');
                    var $slug = $(this).closest('.modal').find('.fld-slug');
                    $.ajax({
                        url: "/api/color/update/" + color.id + "/" + $hex.val().substring(1,7) + "/" + $slug.val() + "/" + encodeURIComponent($name.val()),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            $meta.parent().find('.p').css('backgroundColor', $hex.val());
                            $meta.find('.name').text($name.val());
                            $meta.find('.hex').text($hex.val());
                            $meta.data('name', $name.val());
                            $meta.data('hex', $hex.val());
                            $meta.data('slug', $slug.val());
                            modal.cancel();
                        }
                    });
                }, function() {
                    $.ajax({
                        url: "/api/color/remove/" + color.id,
                        dataType: 'json',
                        success: function(data){
                            $element.remove();
                            if (data.remove > 0) {
                                $('.color-' + data.remove).remove();
                                that.fire('colorRemoved', data.remove);
                            }
                            modal.cancel();
                        }
                    });
                });
                return false;
            });

            $element.append($meta);
            $element.css({
                left: color.x + 'px',
                top: color.y + 'px',
                position: 'absolute'
            });
            $element.find('.p').css('backgroundColor', '#' + color.hex);
            
            // show / hide picker on hover
            $element.hover(
                function(){
                    $('.picker').hide();
                    that.hover = true;
                },
                function(){
                    $('.picker').show();
                    that.hover = false;
                }
            );

            // enable drag and drop for colors
            /*
            $element.draggable({
                distance: 30,
                start: function() {
                    // NOOP
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
                }
            });
            */
            
            // draw element
            this.$ctx.append($element);
            $element.show();
        }
    });
})(Tc.$);