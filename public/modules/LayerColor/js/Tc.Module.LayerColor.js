/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.LayerColor = Tc.Module.extend({
        
        active: false,
        drag: false,
        hover: false,
        
        on: function(callback) {
            var that = this;
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
        
        activate: function() {
            var that = this;
            this.active = true;
            var colors = $('.color');
            $.each(colors, function(key, color) {
                that.addLibraryColor(color);
            });
            
            var screen = $('.modScreen');
            $('.btn-color').addClass('active');
            
            $('.modColorLibrary').show();
            $('.modScreen').eyedrop({
                'display': true,
                pick: function(x, y, color) {
                    if (that.drag || that.hover) {
                        return;
                    }
                    $.ajax({
                        url: "/api/color/add/" + screen.data('screen') + "/" + x + "/" + y + "/" + color.r + "/" + color.g + "/" + color.b + "/" + color.a + "/" + encodeURIComponent(color.hex.substring(1)),
                        dataType: 'json',
                        success: function(data){
                            if (data.result == 'NEW') {
                                var box = $('<a href="javascript:;" rel="tooltip" title="' + data.name + ' - ' + color.hex + '" class="color color-' + data.color + '" data-id="' + data.color + '" data-color="' + color.hex.substring(1) + '"></a>');
                                box.css('backgroundColor', color.hex);
                                $('.modColorLibrary').append(box);
                                box.tooltip();
                                that.addLibraryColor(box);
                            }
                            that.addColor(data, true);
                        }
                    });
                }
            });
            this.load();
        },
        
        deactivate: function() {
            this.active = false;
            this.$ctx.empty();
            $('.modColorLibrary').hide();
            $('.modScreen').unbind('click mousemove mouseup mousedown mouseenter mouseleave');
            $('.modScreen').css('cursor', 'auto');
            $('.btn-color').removeClass('active');
            $('.picker').hide();
        },
        
        addLibraryColor: function(color) {
            var that = this;
            $(color).css('backgroundColor', '#' + $(color).data('color'));
            $(color).draggable({
                helper: "clone",
                revert: "true",
                cursorAt: {top: 0, left: -20},
                stop: function(e) {
                    var offset = $('.modScreen').offset();
                    var x = e.pageX - offset.left;
                    var y = e.pageY - offset.top;
                    $.ajax({
                        url: "/api/color/add/" + $('.modScreen').data('screen') + "/" + x + "/" + y + "/" + $(color).data('id'),
                        dataType: 'json',
                        success: function(data){
                            that.addColor(data, true);
                        }
                    });
                }
            });
        },
        
        addColor: function(color, fade) {
            var that = this;
            var label = color.name;
            var hex = '#' + color.hex;
            if (!label) {
                label = hex;
            }
            var element = $('<div class="color"><div class="p"></div><div class="meta">' + label + '<br /><span>#' + color.hex + '</span></div></div>');
            element.css({
                left: color.x + 'px',
                top: color.y + 'px',
                position: 'absolute'
            });
            $('.p', element).css('backgroundColor', '#' + color.hex);

            element.bind('click', function(e) {
                e.stopPropagation();
                return false;
            })
            
            // show / hide picker on hover
            element.hover(
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
            element.bind('dblclick', function(e) {
                $.ajax({
                    url: "/api/color/remove/" + color.id,
                    dataType: 'json',
                    success: function(data){
                        element.remove();
                        if (data.remove > 0) {
                            $('.color-' + data.remove).remove();
                        }
                    }
                });
                that.hover = false;
                e.stopPropagation();
                return false;
            });
            
            // draw element
            this.$ctx.append(element);
            if (fade) {
                element.fadeIn('fast');
            } else {
                element.show();
            }
        }
    });
})(Tc.$);