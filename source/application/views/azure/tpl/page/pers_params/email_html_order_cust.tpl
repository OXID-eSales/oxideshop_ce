[{block name="pers_params__email_html_order_cust"}]
    [{if $oView->showPersParam($sPersParamKey) }]
        <li style="padding: 3px;">[{ $oView->getPersParamText($sPersParamKey) }] : [{ $oView->getPersParamValue($sPersParamKey,$sPersParamValue) }]</li>
    [{/if}]
[{/block}]
