[{assign var="currency" value=$oView->getActCurrency()}]
[{foreach from=$oView->getNotMappedToRDFaDeliverySets() item=oNewDeliveryMethod}]
    <div about="[{$sRDFaUrl}]#[{$oNewDeliveryMethod->oxdeliveryset__oxtitle->value|strip:''|cat:'_'|cat:$oNewDeliveryMethod->oxdeliveryset__oxid->value}]" typeof="gr:DeliveryMethod">
        <div property="rdfs:label" content="[{$oNewDeliveryMethod->oxdeliveryset__oxtitle->value}]"></div>
    </div>
[{/foreach}]

[{assign var="oDelChargeSpecs" value=$oView->getDeliveryChargeSpecs()}]
[{foreach from=$oDelChargeSpecs item=oDelChargeSpec}]
    <div typeof="gr:DeliveryChargeSpecification" about="[{$sRDFaUrl}]#[{$oDelChargeSpec->getId()}]">
        <div property="rdfs:comment" content="[{$oDelChargeSpec->oxdelivery__oxtitle->value}]" [{if $oView->getActiveLangAbbr()}] xml:lang="[{$oView->getActiveLangAbbr()}]"[{/if}]></div>
        [{assign var="oPriceValidity" value=$oView->getRdfaPriceValidity()}]
        <div property="gr:validFrom" content="[{$oPriceValidity.validfrom}]" datatype="xsd:dateTime"></div>
        <div property="gr:validThrough" content="[{$oPriceValidity.validthrough}]" datatype="xsd:dateTime"></div>
[{if $oView->getRdfaVAT()}]
        <div property="gr:valueAddedTaxIncluded" content="[{if $oView->getRdfaVAT() eq 1}]true[{else}]false[{/if}]" datatype="xsd:boolean"></div>
[{/if}]
        <div property="gr:hasCurrency" content="[{$currency->name}]" datatype="xsd:string"></div>
        <div property="gr:hasCurrencyValue" content="[{$oDelChargeSpec->oxdelivery__oxaddsum->value}]" datatype="xsd:float"></div>
[{if $oDelChargeSpec->oxdelivery__oxdeltype->value eq "p"}]
        <div rel="gr:eligibleTransactionVolume">
            <div typeof="gr:UnitPriceSpecification">
                <div property="gr:hasUnitOfMeasurement" content="C62" datatype="xsd:string"></div>
                <div property="gr:hasCurrency" content="[{$currency->name}]" datatype="xsd:string"></div>
[{if $oDelChargeSpec->oxdelivery__oxparam->value}]
                <div property="gr:hasMinCurrencyValue" content="[{$oDelChargeSpec->oxdelivery__oxparam->value}]" datatype="xsd:float"></div>
[{/if}]
[{if $oDelChargeSpec->oxdelivery__oxparamend->value}]
                <div property="gr:hasMaxCurrencyValue" content="[{$oDelChargeSpec->oxdelivery__oxparamend->value}]" datatype="xsd:float"></div>
[{/if}]
            </div>
        </div>
[{/if}]
[{assign var=oDeliverySetMethods value=$oDelChargeSpec->deliverysetmethods}]
[{foreach from=$oDeliverySetMethods item=oDelSetMethod}]
    [{if $oDelSetMethod->oxdeliveryset__oxobjectid->value}]
        <div rel="gr:appliesToDeliveryMethod" resource="http://purl.org/goodrelations/v1#[{$oDelSetMethod->oxdeliveryset__oxobjectid->value}]"></div>
    [{else}]
        <div rel="gr:availableDeliveryMethods" resource="[{$sRDFaUrl}]#[{$oDelSetMethod->oxdeliveryset__oxtitle->value|strip:''|cat:'_'|cat:$oDelSetMethod->oxdeliveryset__oxid->value}]"></div>
    [{/if}]
[{/foreach}]
[{foreach from=$oDelChargeSpec->getCountriesISO() item=sRegion}]
        <div rel="gr:eligibleRegions" content="[{$sRegion}]" datatype="xsd:string"></div>
[{/foreach}]
    </div>
[{/foreach}]