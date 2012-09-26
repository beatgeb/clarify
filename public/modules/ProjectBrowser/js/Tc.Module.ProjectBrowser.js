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
        on: function(callback) {

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
            $('.project-name', $ctx).on('keypress', function(e) {
                if (e.keyCode == 13 && $(this).val() != '') {
                    that.addProject();
                }
            });
            $('.add-project-submit', $ctx).on('click', function() {
                that.addProject();
            });
            $('.collaborator-email', $ctx).on('keypress', function(e) {
                if (e.keyCode == 13 && $(this).val() != '') {
                    that.addCollaborator();
                }
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
                var data = { 'code': modules, 'code_single': modules_all };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('export-terrific', data, function() {});
                return false;
            });
            callback();
        },

        addProject: function() {
            var $ctx = this.$ctx;
            $('.add-project-submit', $ctx).addClass('btn-disabled');
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
            $('.add-collaborator-submit', $ctx).addClass('btn-disabled');
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