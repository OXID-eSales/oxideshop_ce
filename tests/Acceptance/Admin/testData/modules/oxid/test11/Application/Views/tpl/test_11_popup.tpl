[{include file="popups/headitem.tpl" title="POPUP_HERE"}]


<script type="text/javascript">
    initAoc = function()
    {
        YAHOO.oxid.container1 = new YAHOO.oxid.aoc( 'container1',
            '[{$oxajax}]',
            '[{$oViewConf->getAjaxLink()}]&container=test_11_ajax_controller'
        );
    }
    $E.onDOMReady( initAoc );
</script>

<table width="100%">
    <tr>
        <td valign="top" id="container1"></td>[{$oxajax_result}]
    </tr>
</table>

</body>
</html>
