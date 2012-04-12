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
                $('.modal-confirm h3').text('Delete Project');
                $('.modal-confirm p').html('Do you really want to delete this project with all of its data?');
                $('.modal-confirm .btn-confirm').text('Delete Project');
                $('.modal-confirm .btn-confirm').on('click', function() {
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
                $('.modal-confirm').modal();
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