[{assign var=sContentName value=$oView->getRDFaDeliveryChargeSpecLoc()}]
[{assign var=oDeliveryMethods value=$oView->getRDFaDeliverySetMethods()}]
[{foreach from=$oDeliveryMethods item=oDelSetMethod}]
    [{if $oDelSetMethod->oxdeliveryset__oxobjectid->value}]
        <div rel="gr:availableDeliveryMethods" resource="http://purl.org/goodrelations/v1#[{$oDelSetMethod->oxdeliveryset__oxobjectid->value}]"></div>
    [{else}]
        [{oxifcontent ident=$sContentName object="oCont"}]
        <div rel="gr:availableDeliveryMethods" resource="[{$oCont->getLink()}]#[{$oDelSetMethod->oxdeliveryset__oxtitle->value|strip:''|cat:'_'|cat:$oDelSetMethod->oxdeliveryset__oxid->value}]"></div>
        [{/oxifcontent}]
    [{/if}]
[{/foreach}]
[{oxifcontent ident=$sContentName object="oCont"}]
[{foreach from=$oView->getProductsDeliveryList() item=oDelivery}]
    <div rel="gr:hasPriceSpecification" resource="[{$oCont->getLink()}]#[{$oDelivery->getId()}]"></div>
[{/foreach}]
[{/oxifcontent}]