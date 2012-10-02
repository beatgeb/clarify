/**
 * Clarify.
 *
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.ApiBrowser = Tc.Module.extend({
        on: function(callback) {
            var $ctx = this.$ctx;
            var that = this;

            $('.btn-run').on('click', function() {
                that.run();
            });
            //that.run();
            callback();
        },

        run: function() {
            var request_count = 0;
            var response_count = 0;
            var $li = $('.services li');
            request_count = $li.length;
            $('.icon', $li).removeClass('icon-ok').addClass('icon-question-sign');
            $.each($li, function(key, li) {
                var $item = $(li);
                var start_time = new Date().getTime();
                var url = $item.data('url');
                var json = $item.data('json');
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'POST',
                    data: json,
                    success: function(data){
                        var request_time = new Date().getTime() - start_time;
                        $item.find('.time').text(request_time + 'ms');
                        $item.find('.icon').removeClass('icon-question-sign').addClass('icon-ok');
                        response_count++;
                        if (request_count == response_count) {
                            $('h2 > i').removeClass('icon-question-sign').addClass('icon-ok');
                        }
                    }
                });
            });
        }

    });
})(Tc.$);