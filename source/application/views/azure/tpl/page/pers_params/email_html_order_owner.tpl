[{block name="pers_params__email_html_order_owner"}]
    [{if $oView->showPersParam($sPersParamKey) }]
        ,&nbsp;<em>[{ $oView->getPersParamText($sPersParamKey) }] : [{ $oView->getPersParamValue($sPersParamKey,$sPersParamValue) }]</em>
    [{/if}]
[{/block}]
