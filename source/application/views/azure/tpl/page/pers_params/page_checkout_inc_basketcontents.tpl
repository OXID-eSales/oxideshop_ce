[{block name="pers_params__page_checkout_inc_basketcontents"}]
    [{if !$editable }]
        [{if $oView->showPersParam($sPersParamKey) }]
            <br />
            <strong>
                [{ $oView->getPersParamText($sPersParamKey) }]
            </strong>
            [{ $oView->getPersParamValue($sPersParamKey,$sPersParamValue) }]
        [{/if}]
    [{else}]
        [{if $oView->showPersParam($sPersParamKey) }]
            <p>
                <label class="persParamLabel">
                    [{ $oView->getPersParamText($sPersParamKey) }]
                </label>
                <input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][[{ $sPersParamKey }]]" value="[{ $oView->getPersParamValue($sPersParamKey,$sPersParamValue) }]">
            </p>
        [{else}]
            <input type="hidden" name="aproducts[[{ $basketindex }]][persparam][[{ $sPersParamKey }]]" value="[{ $oView->getPersParamValue($sPersParamKey,$sPersParamValue) }]">
        [{/if}]
    [{/if}]
[{/block}]
