
(function($) { 
    Tc.Module.Auth = Tc.Module.extend({        
        on: function(callback) {
            var that = this;
            var $ctx = this.$ctx;
            $('.password', $ctx).on('keypress', function(e) {
                if (e.keyCode == 13) {
                    that.authClarify();
                }
            });
            $('.password-confirm', $ctx).on('keypress', function(e) {
                if (e.keyCode == 13) {
                    that.signup();
                }
            });
            $('.btn-signin,.btn-signin-ldap', $ctx).on('click', function(e) {
                that.authClarify();
            });
            $('.btn-twitter', $ctx).on('click', function(e) {
                that.authTwitter();
            });
            $('.btn-signup', $ctx).on('click', function(e) {
                that.signup();
            });
            $('.btn-signup-twitter', $ctx).on('click', function(e) {
                that.authTwitter();
            });
            $('.btn-lost-password', $ctx).on('click', function() {
                $('.btn-signin, .btn-twitter, .password', $ctx).fadeOut('fast', function() {
                    $('.btn-request-password', $ctx).fadeIn('fast');
                });
            });
            $('.btn-request-password', $ctx).on('click', function() {
                that.requestPassword();
            });
            $('input:first', $ctx).focus();
            callback();
        },

        signup: function() {
            var that = this;
            $('input', that.$ctx).removeClass('error');
            var name = $('.name').val();
            var email = $('.email').val();
            var password = $('.password').val();
            var password_confirm = $('.password-confirm').val();
            if (name == '') {
                $('.name', that.$ctx).addClass('error');
                return false;
            }
            if (email == '') {
                $('.email', that.$ctx).addClass('error');
                return false;
            }
            if (password == '' || password != password_confirm) {
                $('.password, .password-confirm', that.$ctx).addClass('error');
                return false;
            }
            $.ajax({
                url: "/api/user/create",
                type: "POST",
                dataType: 'json',
                data: { 
                    'email': email, 
                    'password': password,
                    'name': name 
                },
                success: function(data){
                    if (data.success) {
                        location.href = '/';
                    } else {
                        $('.email', that.$ctx).addClass('error');
                    }
                }
            });
        },

        requestPassword: function() {
            var that = this;
            $('input', that.$ctx).removeClass('error');
            $('.btn-request-password', this.$ctx).addClass('btn-disabled');
            $.ajax({
                url: "/api/auth/requestpassword",
                type: "POST",
                dataType: 'json',
                data: { 
                    'email': $('.email').val()
                },
                success: function(data){
                    if (data.success) {
                        alert("You've got mail!");
                        location.href = '/';
                    } else {
                        $('input', that.$ctx).addClass('error');
                    }
                }
            });
        },

        authClarify: function() {
            var that = this;
            $('input', that.$ctx).removeClass('error');
            $('.btn-signin,.btn-signin-ldap', this.$ctx).addClass('btn-disabled');
            $.ajax({
                url: "/api/auth/authenticate",
                type: "POST",
                dataType: 'json',
                data: { 
                    'username': $('.username').val(),
                    'email': $('.email').val(), 
                    'password': $('.password').val() 
                },
                success: function(data){
                    if (data.success) {
                        location.href = '/';
                    } else {
                        $('input', that.$ctx).addClass('error');
                    }
                }
            });
        },
        
        authTwitter: function() {
            $('.btn-twitter', this.$ctx).addClass('btn-disabled');
            location.href = '/auth/?start=1';
        }
    });
})(Tc.$);