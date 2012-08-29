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
            
            $('.edit-collaborator a', this.$ctx).on('click', function() {
                var id = $(this).parent().data('id');
                $.ajax({
                    url: "/api/collaborator/remove/" + id,
                    dataType: 'json',
                    type: 'POST',
                    success: function(data){
                        location.reload();
                    }
                });
                return false;
            });

            $('.btn-export-terrific').on('click', function(e) {
                var $modules = $('.modules a', $ctx);
                var modules = '';
                var modules_all = 'terrific:generate:modules';
                $modules.each(function(){
                     modules += $(this).data('terrific')+"\r\n"; 
                     modules_all += ' ' + $(this).data('module');
                });

                $('h3', $modal).text('Execute the following Terrific Composer Commands');
                $('p', $modal).empty();

                var code = $('<pre></pre>').text(modules);
                $('p', $modal).append($('<span>Learn more about Terrific & Modules on <a href="http://terrifically.org">http://terrifically.org</a><br />Here are all your specified modules for this project:</span>'));
                $('p', $modal).append(code);
                $('p', $modal).append($('<br /><span>Or all in one command:</span>'));
                code = $('<pre></pre>').text(modules_all);
                $('p', $modal).append(code);
                $('.btn-confirm', $modal).text('Close');
                $('.btn-confirm', $modal).on('click', function() {
                    $modal.modal('hide');
                    e.stopPropagation();
                    return false;
                });
                $modal.modal();
            });
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
                    success: function(data) {
                        location.reload();
                    }
                });
            }
        }
    });
})(Tc.$);