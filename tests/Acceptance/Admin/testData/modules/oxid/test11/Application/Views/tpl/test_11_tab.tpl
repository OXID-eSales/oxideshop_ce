<script src="[{$shopUrl}]out/admin/src/js/libs/jquery.min.js"></script>
<script>$.noConflict();</script>
<script>
    var Test11AjaxController = (function ($) {

        return {
            init: function () {
                $('#test_11_ajax_controller').click(this.callTest11Controller());
            },
            callTest11Controller: function () {
                var url = '[{$shopUrl}]admin/oxajax.php?container=test_11_ajax_controller';

                $.ajax({
                    url: url
                })
                    .success(function () {
                        $('#test_11_ajax_controller_result').html('success')
                    })
                    .error(function () {
                        $('#test_11_ajax_controller_result').html('error')
                    })
                ;
            }
        };

    })(jQuery);
    jQuery(Test11AjaxController.init());
</script>

<div>
    <a id="test_11_ajax_controller" href="#">call test_11_ajax_controller</a>
</div>
<div id="test_11_ajax_controller_result"></div>
