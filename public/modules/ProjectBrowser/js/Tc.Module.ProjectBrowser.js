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
            $('.btn-add-collaborator', $ctx).on('click', function() {
                $('.add-collaborator', $ctx).slideToggle('fast', function() {
                    $('.collaborator-email', $ctx).focus();
                });
            });
            $('.add-project-submit', $ctx).on('click', function() {
                that.addProject();
            });
            $('.add-collaborator-submit', $ctx).on('click', function() {
                that.addCollaborator();
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
            $('.edit-collaborator', this.$ctx).on('click', function() {
                $('.popbox').popbox();
                return false;
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
                $('.btn-confirm', $modal).text('Close');
                $('.btn-confirm', $modal).on('click', function() {
                    $modal.modal('hide');
                    e.stopPropagation();
                    return false;
                });
                $modal.modal();
            });

            $('.btn-export-sass').on('click', function(e) {
                var $colors = $('.colors a', $ctx);
                var colors = '';
                $colors.each(function(){
                     colors += $(this).data('sass')+"\r\n"; 
                });

                $('h3', $modal).text('Copy & Paste the following Sass Template');
                $('p', $modal).empty();

                var code = $('<pre></pre>').text(colors);
                $('p', $modal).append($('<span>Here are all your colors that you have specified for this project:</span>'));
                $('p', $modal).append(code);
                $('.btn-confirm', $modal).text('Close');
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
        },

        addCollaborator: function() {
            var $ctx = this.$ctx;
            var email = $('.collaborator-email', $ctx).val();
            var project = $('.collaborator-email', $ctx).data('project-id');
            if (email != '') {
                $.ajax({
                    url: "/api/collaborator/add/",
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        'email': email,
                        'project': project
                    },
                    statusCode:{
                        200:function(data){
                            $('ul.collaborators', $ctx).append('<li><a class="edit-collaborator" data-id="' + data.id + '" href="#">' + data.email + ' <span class="remove">×</span></a></li>');
                        },
                        400:function(data){
                            $('.collaborator-email', $ctx).addClass('error');
                            alert(data.responseText);
                        }
                    }
                });
            }
        }
    });
})(Tc.$);