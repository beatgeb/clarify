<div class="modal modal-confirm">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3></h3>
  </div>
  <div class="modal-body">
    <p></p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="#" class="btn btn-danger btn-confirm"></a>
  </div>
</div>
<script type="text/javascript" src="<?= R ?>js/js.php"></script>
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
<script type="text/javascript">
  var uvOptions = {};
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/u5QnuwlRcK4saGqrAKVZEA.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>