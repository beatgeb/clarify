<?php require TERRIFIC . 'modules/Modal/modal.phtml' ?>
<div class="mod modKeyboardHandler">
<?php require TERRIFIC . 'modules/KeyboardHandler/keyboardhandler.phtml' ?>
</div>
<script type="text/javascript" src="<?php print R ?>js/app.<?php print (config('cache.js.enabled') && is_file(TERRIFIC . 'js/app.js') ? 'js' : 'php') ?>"></script>
<script type="text/javascript">
(function($) {
    $(document).ready(function() {
        var $page = $('body');
        var application = new Tc.Application($page);
        application.registerModules();
        application.start();
    });
})(Tc.$);
</script>
<?php if (config('feedback.uservoice.enabled')) { ?>
<script type="text/javascript">
  var uvOptions = {};
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/u5QnuwlRcK4saGqrAKVZEA.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>
<?php } ?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-652147-17']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>