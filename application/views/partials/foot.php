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
<script type="text/javascript">
  var _kmq = _kmq || [];
  var _kmk = _kmk || 'afa554d74fcdf58054259fe70815c4f0b35f44a0';
  function _kms(u){
    setTimeout(function(){
      var d = document, f = d.getElementsByTagName('script')[0],
      s = d.createElement('script');
      s.type = 'text/javascript'; s.async = true; s.src = u;
      f.parentNode.insertBefore(s, f);
    }, 1);
  }
  _kms('//i.kissmetrics.com/i.js');
  _kms('//doug1izaerwt3.cloudfront.net/' + _kmk + '.1.js');
  _kmq.push(['identify', '<?= user('email') ?>']);
</script>