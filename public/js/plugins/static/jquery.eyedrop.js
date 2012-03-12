/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($){
    
    $.fn.eyedrop = function(options) {
        
        var settings = $.extend({
            'pick': function(x, y, color) {},
            'pickRange': function(startx, starty, startcolor, endx, endy, endcolor) {},
            'offset': 20,
            'width': 5,
            'height': 5,
            'cursor': 'crosshair',
            'display': false,
            'mode': 'single',
            'picker': $('.picker'),
            'start': function(x, y) {},
            'stop': function() {},
            'picking': function(x, y, w, h) {}
        }, options);

        return this.each(function() {
            var $this = $(this);
            var data = $this.data('eyedrop');
            if (!data) {
                $this.data('eyedrop', {});
                data = $this.data('eyedrop');
            }

            var $display = $('.display', settings.picker);
            if (settings.display !== false) {
                settings.picker.addClass('display');
                $display.show();
            } else {
                settings.picker.removeClass('display');
                $display.hide();
            }
            
            // set width, height and background image on source element
            $this.css({
                'cursor': settings.cursor
            });
            
            $this.hover(
                function() {
                    $('.picker').show();
                }, 
                function() {
                    $('.picker').hide();
                }
            );
            
            // create canvas element
            $canvas = $('<canvas width="' + $this.data('width') + '" height="' + $this.data('height') + '"></canvas>');
            $canvas.hide();
            $('body').append($canvas);
            
            // draw image into canvas
            if ($canvas[0].getContext) {
                var imgctx = $canvas[0].getContext("2d"); 
                var img = new Image();  
                img.onload = function(){
                    imgctx.drawImage(img, 0, 0);
                };
                img.src = $this.data('image');
            } else {
                return;
            }
            
            // on click, run callback pick
            $this.bind('click', function(e) {
                // NOOP
            });
            
            $this.bind('mousedown', function(e) {
                settings.start(data.x, data.y);
                settings.pick(data.x, data.y, data.color);
                data.startx = data.x;
                data.starty = data.y;
                data.startcolor = data.color;
                if (e.preventDefault) {
                    e.preventDefault();
                }
            });
            
            $this.bind('mouseup', function(e) {
                settings.pickRange(
                    data.startx, 
                    data.starty, 
                    data.startcolor, 
                    data.x, 
                    data.y, 
                    data.color
                );
                settings.stop();
            });
            
            // on mouse movement, show picker
            $this.bind('mousemove', function(e) {
                
                var offset = $this.offset();
                data.x = e.pageX - offset.left;
                data.y = e.pageY - offset.top;

                // move magnifier to the correct location
                var $picker = settings.picker;

                // make sure picker-windows does't get out of the screen
                var offsetLeft = (data.x + offset.left + settings.offset + $picker.outerWidth() > $(window).width()) ? data.x + offset.left - (settings.offset + $picker.outerWidth()) : data.x + offset.left + settings.offset;

                $picker.css({
                    left: offsetLeft,
                    top: data.y + offset.top + settings.offset
                });

                // get metadata of surrounding pixels
                var pixel = imgctx.getImageData(
                    data.x - ((settings.width-1) / 2), 
                    data.y - ((settings.height-1) / 2), 
                    settings.width, 
                    settings.height
                );

                // draw surrounding pixels into magnifier 
                for (var i = 0; i < settings.width * settings.height; i++) {
                    var r = pixel.data[0+i*4];
                    var g = pixel.data[1+i*4];
                    var b = pixel.data[2+i*4];
                    var a = pixel.data[3+i*4];
                    $('.p' + (i + 1)).css('backgroundColor', 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')');
                    if (i == ((settings.width*settings.height-1)/2)) {
                        var color = Color.rgb(r, g, b, a);
                        var hex = color.hexTriplet();
                        data.color = {
                            r: r, 
                            g: g, 
                            b: b, 
                            a: a, 
                            hex: hex
                        };
                    }
                }
                
                var x = data.startx < data.x ? data.startx : data.x;
                var y = data.starty < data.y ? data.starty : data.y;
                var width = Math.abs(data.x - data.startx) + 1;
                var height = Math.abs(data.y - data.starty) + 1;
                settings.picking(x, y, width, height);

                // update color-display with current hex-value
                if (settings.display !== false) {
                    $display.html(data.color.hex);
                }
            });
        });
    };
    
})(jQuery);