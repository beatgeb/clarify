/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($){
    window.$canvas = null;
    $.fn.eyedrop = function(options) {
        var settings = $.extend({
            'pick': function(x, y, color) {},
            'pickRange': function(startx, starty, startcolor, endx, endy, endcolor) {},
            'offset': 20,
            'width': 5,
            'height': 5,
            'cursor': 'crosshair',
            'display': false,
            'mode': 'point',
            'picker': $('.picker'),
            '_start': function(x, y) {
                settings._started = true;
                settings.start(x, y);
            },
            'start': function(x, y) {},
            'stop': function() {},
            '_stop': function() {
                settings._started = false;
                settings.stop();
            },
            'picking': function(x, y, w, h) {},
            '_pickRange': function(startx, starty, startcolor, endx, endy, endcolor) {
                if (!settings._started) { return; }
                settings.pickRange(startx, starty, startcolor, endx, endy, endcolor);
            },
            '_started': false
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
            var imgctx; 
            if(!$canvas){
                $canvas = $('<canvas class="eyedrop" height="'+$this.data('height')+'" width="'+$this.data('width')+'"></canvas>');

                // draw image into canvas
                if ($canvas[0].getContext) {
                    imgctx = $canvas[0].getContext("2d"); 
        
                    var img = new Image();  
                    img.onload = function(){
                        imgctx.drawImage(img, 0, 0);
                    };
                    img.src = $this.data('image');
                } else {
                    return;
                }
            } else {
                imgctx = $canvas[0].getContext("2d");
            }

            // in case of range we need to handle mousedown+move+mouseup and click+move+click
            if (options.mode === 'range') {
                var mouseClicked = false;

                $this.on('mousedown', function(e) {
                    if (mouseClicked === true) {
                        mouseClicked = false;
                        settings._pickRange(
                            data.startx,
                            data.starty,
                            data.startcolor,
                            data.x,
                            data.y,
                            data.color
                        );
                        settings._stop();
                    } else {
                        mouseClicked = true;
                        settings._start(data.x, data.y);
                        settings.pick(data.x, data.y, data.color);
                        data.startx = data.x;
                        data.starty = data.y;
                        data.startcolor = data.color;
                        if (e.preventDefault) {
                            e.preventDefault();
                        }
                    }
                });

                $this.on('mouseup', function() {
                    if (data.startx !== data.x && data.starty !== data.y && mouseClicked === true) {
                        mouseClicked = false;
                        settings._pickRange(
                            data.startx,
                            data.starty,
                            data.startcolor,
                            data.x,
                            data.y,
                            data.color
                        );
                        settings._stop();
                    }
                });
            } else {
                $this.on('mousedown', function() {
                    settings.pick(data.x, data.y, data.color);
                    settings._stop();
                });
            }



            var ps = [];
            for (var i = 0; i < settings.width * settings.height; i++) {
                ps[i] = $('#picker' + (i + 1));
            }
            
            // on mouse movement, show picker
            $this.on('mousemove', function(e) {
                
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

                    ps[i].css('backgroundColor', 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')');

                    if (i == ((settings.width*settings.height-1)/2)) {

                        // invert shadow to be more visible on dark backgrounds (not really inverted but top bit of each component of the RGB triple is toggled)
                        var inverted_rgba = 'inset 0px 0px 1px 1px rgba(' + (r ^ 0x80) + ', ' + (g ^ 0x80) + ', ' + (b ^ 0x80) + ', 0.7)';
                        
                        ps[i].css({
                            '-webkit-box-shadow': inverted_rgba,
                            '-moz-box-shadow': inverted_rgba,
                            'box-shadow': inverted_rgba
                        });

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