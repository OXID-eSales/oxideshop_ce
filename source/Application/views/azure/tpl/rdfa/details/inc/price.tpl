[{capture name="sRDFaPriceStart"}]
    <div rel="gr:hasPriceSpecification">
        <div typeof="gr:UnitPriceSpecification">
            [{if $oView->getRDFaValidityPeriod("iRDFaPriceValidity")}]
                [{assign var="aRDFaPValidity" value=$oView->getRDFaValidityPeriod("iRDFaPriceValidity")}]
                <div property="gr:validFrom" content="[{$aRDFaPValidity.from}]" datatype="xsd:dateTime"></div>
                <div property="gr:validThrough" content="[{$aRDFaPValidity.through}]" datatype="xsd:dateTime"></div>
            [{/if}]
            [{if $oView->getRDFaVAT() > 0}]
                <div property="gr:valueAddedTaxIncluded" content="[{if $oView->getRDFaVAT() == 1}]true[{else}]false[{/if}]" datatype="xsd:boolean"></div>
            [{/if}]
            <div property="gr:hasUnitOfMeasurement" content="C62" datatype="xsd:string"></div>
            <div property="gr:hasCurrency" content="[{$currency->name}]" datatype="xsd:string"></div>
[{/capture}]
[{if $oProduct->loadAmountPriceInfo()}]
    [{foreach from=$oProduct->loadAmountPriceInfo() item=priceItem name=amountPrice}]
        [{if $smarty.foreach.amountPrice.first}]
            [{assign var="iRDFaMinAmount" value=$priceItem->oxprice2article__oxamount->value}]
        [{/if}]
[{$smarty.capture.sRDFaPriceStart}]
            <div property="gr:hasCurrencyValue" content="[{$priceItem->oxpricealarm__oxprice->value}]" datatype="xsd:float"></div>
            <div rel="gr:hasEligibleQuantity">
                <div typeof="gr:QuantitativeValue">
                    <div property="gr:hasMinValue" content="[{$priceItem->oxprice2article__oxamount->value}]" datatype="xsd:float"></div>
                    <div property="gr:hasMaxValue" content="[{$priceItem->oxprice2article__oxamountto->value}]" datatype="xsd:float"></div>
                    <div property="gr:hasUnitOfMeasurement" content="C62" datatype="xsd:string"></div>
                </div>
            </div>
        </div>
    </div>
    [{/foreach}]
[{/if}]
[{if $oProduct->getPrice()}]
[{$smarty.capture.sRDFaPriceStart}]
            [{assign var=price value=$oProduct->getPrice()}]
            <div property="gr:hasCurrencyValue" content="[{$price->getBruttoPrice()}]" datatype="xsd:float"></div>
            [{if isset($iRDFaMinAmount)}]
                <div rel="gr:hasEligibleQuantity">
                    <div typeof="gr:QuantitativeValue">
                        <div property="gr:hasMinValue" content="1" datatype="xsd:float"></div>
                        <div property="gr:hasMaxValue" content="[{math equation='x-y' x=$iRDFaMinAmount y=1}]" datatype="xsd:float"></div>
                        <div property="gr:hasUnitOfMeasurement" content="C62" datatype="xsd:string"></div>
                    </div>
                </div>
            [{/if}]
        </div>
    </div>
[{/if}]