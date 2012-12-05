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
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/<?php print config('feedback.uservoice.key') ?>.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>
<?php } ?>