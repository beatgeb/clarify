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
        open: null,
        
        on: function(callback) { 
            var that = this;
            var $ctx = this.$ctx;

            this.sandbox.subscribe('activity.comment', this);
            this.sandbox.subscribe('keyboard', this);

            $('.btn-comments').bind('click', function(e) {
                if (that.active) {
                    that.deactivate();
                } else {
                    that.sandbox.getModuleById($('.modLayerModule').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerColor').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerMeasure').data('id')).deactivate();
                    that.sandbox.getModuleById($('.modLayerTypography').data('id')).deactivate();
                    that.activate();
                }
            });
            
            $('.btn-embed').on('click', function(e) {
                var screen = $(this).data('screen');
                var code = '&lt;script type="text/javascript" src="' + $(this).data('url') + '"&gt;&lt;/script&gt;';
                var data = { 'code': code, 'screen': screen };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('comment-layer-share', data, function() {
                    $.ajax({
                        url: "/api/screen/setting/" + screen + "/embeddable/true",
                        dataType: 'json',
                        success: function(data){
                            modal.cancel();
                        }
                    });
                });
                return false;
            });
            
            $('.dot').live('click', function(e) {
                var container = $(this).parent();
                var edit = $('.edit', container);
                
                if (edit.is(':visible')) {
                    edit.hide();
                    $(this).parent().css('z-index', 1);
                    that.open = null;
                } else {
                    edit.toggle();
                    edit.find('textarea').focus();
                    $(this).parent().css('z-index', 10);
                    that.open = edit;
                }
            });
            callback();
        },

        after: function() {
            var that = this;
            this.fire('RegisterShortcut', {
                'moduleId': that.id,
                'shortcut': 'a',
                'modifier': null,
                'description': 'Switch to annotations layer',
                'callback': function() {
                    $('.btn-comments').click();
                }
            });
        },

        deactivate: function() {
            var $ctx = this.$ctx;
            $ctx.empty();
            $('.screen').unbind('click');
            $('.screen').unbind('dblclick');
            $('.btn-delete').hide();
            $('.btn-embed').hide();
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
            this.deletemode = false;
            this.fire('layerActivated', 'comment');
            
            $('.btn-delete').show();
            $('.btn-embed').show();
            $('.btn-comments').addClass('active');
            $('.btn-delete').removeClass('delete');
            
            // Add data dots
            $('.screen').on('click', function(e) {
                if (that.open) {
                    that.open.hide();
                    that.open = null;
                    return false;
                }
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
                        $('.def-' + data.id + ' .dot').trigger('click');
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
            var that = this;
            var text = data.content ? data.content : '';
            var $delete = $('<a href="javascript:;" class="delete"><i class="icon icon-trash"></i></a>');
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
            def.find('.edit').append($delete);
            $ctx.append(def);

            // add click event on delete icon
            $delete.on('click', function() {
                var id = data.id;
                $.ajax({
                    url: "/api/comment/remove/" + id,
                    dataType: 'json',
                    type: 'POST',
                    success: function(data){
                        def.remove();
                        that.open = null;
                        that.fire('commentRemoved', id);
                    }
                });
            });

            // add blur event
            $('.edit > textarea', def).on('blur', function(e) {
                var container = $(this).parent();
                var content = $(this).val();
                $.ajax({
                    url: "/api/comment/update/" + container.parent().data('id'),
                    dataType: 'json',
                    type: 'POST',
                    data: "content=" + content,
                    success: function(data){
                        that.fire('commentUpdated', data);
                    }
                });
                container.parent().css('z-index', 3);
                container.hide();
            });

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
                    var w = $('.edit', def).width() - 31;
                    var h = $('.edit', def).height() - 31;
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
            that.fire('commentAdded', data);
        }
    });
})(Tc.$);