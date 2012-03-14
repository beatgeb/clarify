/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) { 
    Tc.Module.LayerComment = Tc.Module.extend({
        
        active: false,
        deletemode: false,
        
        onBinding: function() { 
            var that = this;
            var $ctx = this.$ctx;
            $('.btn-comments').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerColor').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerMeasure').data('id')).deactivate();
                    that.activate();
                }
            });
            
            $('.btn-delete').bind('click', function(e) {
                that.deletemode = !that.deletemode;
                if (that.deletemode) {
                    $('.def', $ctx).addClass('def-delete');
                    $('.edit', $ctx).hide();
                } else {
                    $('.def', $ctx).removeClass('def-delete');
                }
            });
            
            $('.dot').live('click', function(e) {
                var container = $(this).parent();
                if (that.deletemode) {
                    $.ajax({
                        url: "/api/comment/remove/" + container.data('id'),
                        dataType: 'json',
                        success: function(data){
                            $('.edit', container).fadeOut('fast');
                            $('.dot', container).effect('puff');
                        }
                    });
                    return;
                }
                var edit = $('.edit', container);
                if (edit.is(':visible')) {
                    // save changes
                    var content = $('textarea', edit).val();
                    $.ajax({
                        url: "/api/comment/update/" + container.data('id'),
                        dataType: 'json',
                        type: 'POST',
                        data: "content=" + content,
                        success: function(data){
                            // NOOP
                        }
                    });
                    edit.toggle();
                    container.css('z-index', 3);
                } else {
                    edit.toggle();
                    $(this).parent().css('z-index', 10);
                }
            });
        },
        
        deactivate: function() {
            var $ctx = this.$ctx;
            $ctx.empty();
            $('.screen').unbind('click');
            $('.btn-delete').hide();
            $('.btn-comments').removeClass('active');
            this.active = false;
            this.deletemode = false;
        },
        
        activate: function() {
            
            var $ctx = this.$ctx;
            var that = this;
            var screen = $('.modScreen').data('screen');
            var layer = $('.modScreen').data('layer');
            
            $ctx.empty();
            this.active = true;
            
            $('.btn-delete').show();
            $('.btn-comments').addClass('active');
            
            // Add data dots
            $('.screen').bind('click', function(e) {
                var offsetLeft = $('.modScreen').offset().left;
                var offsetTop = $('.modScreen').offset().top;
                var x = e.pageX - offsetLeft - 15;
                var y = e.pageY - offsetTop - 15;
                var screen = $('.modScreen').data('screen');
                var layer = $('.modScreen').data('layer');
                $.ajax({
                    url: "/api/comment/add/" + screen + "/" + x + "/" + y,
                    dataType: 'json',
                    success: function(data){
                        that.addComment(data);
                    }
                });
            });
            
            // initially load comments
            this.load();
            
        },
        
        load: function() {
            var that = this;
            var screen = $('.modScreen').data('screen');
            var layer = $('.modScreen').data('layer');
            $.ajax({
                url: "/api/comment/get/" + screen,
                dataType: 'json',
                success: function(data){
                    $.each(data, function(key, comment) {
                        that.addComment(comment);
                    });
                }
            });
        },
        
        addComment: function(data) {
            var $ctx = this.$ctx;
            var text = data.content ? data.content : '';
            var def = $('<div class="def def-' + data.id + '"><a href="javascript:;" class="dot"><span class="nr">' + data.nr + '</span></a><div class="edit"><textarea>' + text + '</textarea></div></div>');
            def.css('left', data.x + 'px');
            def.css('top', data.y + 'px');
            def.data('id', data.id);
            if (data.w > 0 && data.h > 0) {
                $('.edit > textarea', def).css({
                    width: data.w, 
                    height: data.h
                });
            }
            $ctx.append(def);

            // Activate drag'n'drop on dots
            def.draggable({
                stop: function() {
                    var x = $(this).position().left;
                    var y = $(this).position().top;
                    var id = $(this).data('id');
                    $.ajax({
                        url: "/api/comment/move/" + id + "/" + x + "/" + y,
                        dataType: 'json',
                        success: function(data){
                        // moved
                        }
                    });
                }
            });
            $('.edit', def).resizable({
                stop: function() {
                    var w = $('.edit', def).width() - 22;
                    var h = $('.edit', def).height() - 16;
                    $.ajax({
                        url: "/api/comment/resize/" + data.id + "/" + w + "/" + h,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                        // NOOP
                        }
                    });
                    $('.edit > textarea', def).css({
                        width: w, 
                        height: h
                    });
                }
            });
        }
    });
})(Tc.$);