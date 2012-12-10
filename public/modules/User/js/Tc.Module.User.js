/**
 * Clarify.
 *
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.User = Tc.Module.extend({

        on: function(callback) {
            var $ctx = this.$ctx;
            var that = this;

            $('.btn-account-settings', $ctx).on('click', function(e) {
                var $btn = $(this);
                var data = { 'name': $btn.data('name'), 'email': $btn.data('email') };
                var modal = that.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('account-settings', data, function() {
                    var $input = $(this).closest('.modal').find('.fld-name');
                    $.ajax({
                        url: "/api/user/setting/name/" + encodeURIComponent($input.val()),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data){
                            $('.username', $ctx).text($input.val());
                            $btn.data('name', $input.val());
                            modal.cancel();
                        }
                    });
                }, function() {
                    alert('Not possible yet.');
                });
                return false;
            });

            $('.btn-plan', this.$ctx).tooltip({'placement': 'left'});

            callback();
        }
    });
})(Tc.$);