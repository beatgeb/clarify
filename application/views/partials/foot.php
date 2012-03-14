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