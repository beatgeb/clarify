/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.ProjectBrowser = Tc.Module.extend({
        onBinding: function() {
            var $ctx = this.$ctx;
            var that = this;
			var $modal = $('.modal-confirm');
			
            $('.btn-add-project', $ctx).on('click', function() {
                $('.add-project', $ctx).slideToggle('fast', function() {
                    $('.project-name', $ctx).focus();
                });
            });
            $('.add-project-submit', $ctx).on('click', function() {
                that.addProject();
            });
            $('.project-name', $ctx).on('keypress', function(e) {
                if (e.keyCode == 13) {
                    that.addProject();
                }
            });
            $('.btn-delete-project').on('click', function() {
                var project = $(this).data('project');
                $('h3', $modal).text('Delete Project');
                $('p', $modal).html('Do you really want to delete this project with all of its data?');
                $('.btn-confirm', $modal).text('Delete Project');
                $('.btn-confirm', $modal).on('click', function() {
                    $.ajax({
                        url: "/api/project/delete/" + project,
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            location.href='/';
                        }
                    });
                    e.stopPropagation();
                    return false;
                });
                $modal.modal();
            });

            $('.btn-export-css').on('click', function(e) {
				var $colors = $('.colors a', $ctx);
				var colors = '';
				$colors.each(function(){
					 colors += $(this).data('less')+"\r\n"; 
				});
				
                $('h3', $modal).text('Copy & Paste the following LESS Template');
                $('p', $modal).empty();
				
                var code = $('<pre></pre>').text(colors);
                $('p', $modal).append($('<span>Here are all your colors that you have specified for this project:</span>'));
                $('p', $modal).append(code);
                $('.btn-confirm', $modal).text('THANK YOU');
                $('.btn-confirm', $modal).on('click', function() {
					$modal.modal('hide');
                    e.stopPropagation();
                    return false;
                });
                $modal.modal();
            });
            
            $('.color', this.$ctx).tooltip();
        },
        
        addProject: function() {
            var $ctx = this.$ctx;
            var name = $('.project-name', $ctx).val();
            if (name != '') {
                $.ajax({
                    url: "/api/project/add/",
                    dataType: 'json',
                    type: 'POST',
                    data: 'name=' + encodeURIComponent(name),
                    success: function(data){
                        location.href = data.url;
                    }
                });
            }
        }
    });
})(Tc.$);