[{if $oDetailsProduct->oxarticles__oxmindeltime->value || $oDetailsProduct->oxarticles__oxmaxdeltime->value}]
<span id="productDeliveryTime">
[{oxmultilang ident="PAGE_DETAILS_DELIVERYTIME_DELIVERYTIME"}]
[{if $oDetailsProduct->oxarticles__oxmindeltime->value && $oDetailsProduct->oxarticles__oxmindeltime->value != $oDetailsProduct->oxarticles__oxmaxdeltime->value}]
    [{$oDetailsProduct->oxarticles__oxmindeltime->value}] -
[{/if}]
[{if $oDetailsProduct->oxarticles__oxmaxdeltime->value}]
    [{assign var="unit" value=$oDetailsProduct->oxarticles__oxdeltimeunit->value}]
    [{assign var="ident" value=PAGE_DETAILS_DELIVERYTIME_$unit}]
    [{if $oDetailsProduct->oxarticles__oxmaxdeltime->value > 1}]
        [{assign var="ident" value=$ident|cat:"S"}]
    [{/if}]
    [{$oDetailsProduct->oxarticles__oxmaxdeltime->value}] [{oxmultilang ident=$ident}]
[{/if}]
</span>
[{/if}]