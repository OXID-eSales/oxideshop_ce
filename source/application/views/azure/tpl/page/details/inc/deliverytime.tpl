[{if $oDetailsProduct->oxarticles__oxmindeltime->value || $oDetailsProduct->oxarticles__oxmaxdeltime->value}]
<span id="productDeliveryTime">
[{oxmultilang ident="DELIVERYTIME_DELIVERYTIME" suffix="COLON"}]
[{if $oDetailsProduct->oxarticles__oxmindeltime->value && $oDetailsProduct->oxarticles__oxmindeltime->value != $oDetailsProduct->oxarticles__oxmaxdeltime->value}]
    [{$oDetailsProduct->oxarticles__oxmindeltime->value}] -
[{/if}]
[{if $oDetailsProduct->oxarticles__oxmaxdeltime->value}]
    [{assign var="unit" value=$oDetailsProduct->oxarticles__oxdeltimeunit->value}]
    [{assign var="ident" value=DELIVERYTIME_$unit}]
    [{if $oDetailsProduct->oxarticles__oxmaxdeltime->value > 1}]
        [{assign var="ident" value=$ident|cat:"S"}]
    [{/if}]
    [{oxmultilang ident=$ident args=$oDetailsProduct->oxarticles__oxmaxdeltime->value}]
[{/if}]
</span>
[{/if}]