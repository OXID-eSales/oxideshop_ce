[{block name="pers_params__page_details_inc_productmain"}]
    <div class="persparamBox clear">
        <label for="persistentParam">
            [{ $oView->getPersParamText($sPersParamKey) }]
        </label>
        <input type="text" id="persistentParam" name="persparam[details]" value="[{ $oView->getPersParamValue($sPersParamKey,$sPersParamValue) }]" size="35">
    </div>
[{/block}]
