[{* Offering *}]
[{assign var="currency" value=$oView->getActCurrency()}]
[{assign var="oProduct" value=$oView->getProduct()}]
[{assign var="sRDFaUrl" value=$oView->getLink()}]

<div xmlns="http://www.w3.org/1999/xhtml"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
    xmlns:gr="http://purl.org/goodrelations/v1#"
    xmlns:foaf="http://xmlns.com/foaf/0.1/"
    xmlns:v="http://rdf.data-vocabulary.org/#"
    xml:base="[{$sRDFaUrl}]"
    typeof="gr:Offering" about="[{$sRDFaUrl}]#offeringdata">
    <div rel="foaf:page" resource="[{$sRDFaUrl}]"></div>
[{oxifcontent ident=$oView->getRDFaBusinessEntityLoc() object="oCont"}]
    <div rev="gr:offers" resource="[{$oCont->getLink()}]#companydata"></div>
[{/oxifcontent}]
[{if $oProduct->oxarticles__oxtitle->value}]
    <div property="gr:name" content="[{$oProduct->oxarticles__oxtitle->value|strip_tags|strip}]" [{if $oView->getActiveLangAbbr()}] xml:lang="[{$oView->getActiveLangAbbr()}]"[{/if}]></div>
[{/if}]
[{oxhasrights ident="SHOWLONGDESCRIPTION"}]
[{assign var="oLongdesc" value=$oProduct->getLongDescription()}]
[{if $oLongdesc->value}]
    [{capture assign="oLongdescEvaluated"}][{oxeval var=$oLongdesc->value}][{/capture}]
    <div property="gr:description" content="[{oxeval var=$oLongdescEvaluated|strip_tags|strip}]" [{if $oView->getActiveLangAbbr()}] xml:lang="[{$oView->getActiveLangAbbr()}]"[{/if}]></div>
[{/if}]
[{if !$oProduct->oxarticles__oxbundleid->value}]
    <div property="gr:hasStockKeepingUnit" content="[{$oProduct->oxarticles__oxartnum->value}]" datatype="xsd:string"></div>
    [{if $oProduct->oxarticles__oxmpn->value}]
        <div property="gr:hasMPN" content="[{$oProduct->oxarticles__oxmpn->value}]" datatype="xsd:string"></div>
    [{/if}]
[{/if}]
[{/oxhasrights}]
[{include file="rdfa/details/inc/object.tpl"}]
[{if $oView->getRDFaNormalizedRating()}]
    <div rel="v:hasReview">
        <div typeof="v:Review-aggregate">
            [{assign var="aRDFaRating" value=$oView->getRDFaNormalizedRating()}]
            <div property="v:count" content="[{$aRDFaRating.count}]" datatype="xsd:float"></div>
            <div property="v:rating" content="[{$aRDFaRating.value}]" datatype="xsd:float"></div>
        </div>
    </div>
[{/if}]
[{if $oView->showRDFaProductStock()}]
    <div rel="gr:hasInventoryLevel">
        <div typeof="gr:QuantitativeValue">
            <div property="gr:hasMinValue" content="[{if $oProduct->getStockStatus() == -1}]0[{else}][{$oProduct->oxarticles__oxstock->value}][{/if}]" datatype="xsd:float"></div>
            <div property="gr:hasUnitOfMeasurement" content="C62" datatype="xsd:string"></div>
        </div>
    </div>
[{/if}]
[{oxhasrights ident="SHOWARTICLEPRICE"}]
[{include file="rdfa/details/inc/price.tpl"}]
[{/oxhasrights}]
[{if $oProduct->getDeliveryDate()}]
    <div property="gr:validFrom" content="[{$oProduct->getDeliveryDate()}]T00:00:00" datatype="xsd:dateTime"></div>
[{elseif $oView->getRDFaValidityPeriod('iRDFaOfferingValidity')}]
    [{assign var="aRDFaValidity" value=$oView->getRDFaValidityPeriod('iRDFaOfferingValidity')}]
    <div property="gr:validFrom" content="[{$aRDFaValidity.from}]" datatype="xsd:dateTime"></div>
    <div property="gr:validThrough" content="[{$aRDFaValidity.through}]" datatype="xsd:dateTime"></div>
[{/if}]
[{if $oView->getRDFaBusinessFnc()}]
    <div rel="gr:hasBusinessFunction" resource="http://purl.org/goodrelations/v1#[{$oView->getRDFaBusinessFnc()}]"></div>
[{/if}]
[{if $oView->getRDFaCustomers()}]
    [{foreach from=$oView->getRDFaCustomers() item=Customer}]
        <div rel="gr:eligibleCustomerTypes" resource="http://purl.org/goodrelations/v1#[{$Customer}]"></div>
    [{/foreach}]
[{/if}]
[{if $oViewConf->getCountryList()}]
[{foreach from=$oViewConf->getCountryList() item=oRegion}]
    <div property="gr:eligibleRegions" content="[{$oRegion->oxcountry__oxisoalpha2->value}]" datatype="xsd:string"></div>
[{/foreach}]
[{/if}]
[{oxhasrights ident="SHOWARTICLEPRICE"}]
[{include file="rdfa/details/inc/payment.tpl"}]
[{if $oProduct->oxarticles__oxfreeshipping->value}]
    <div rel="gr:hasPriceSpecification">
        <div typeof="gr:DeliveryChargeSpecification">
        [{if $oView->getRDFaValidityPeriod("iRDFaPriceValidity")}]
        [{assign var="aRDFaPValidity" value=$oView->getRDFaValidityPeriod("iRDFaPriceValidity")}]
            <div property="gr:validFrom" content="[{$aRDFaPValidity.from}]" datatype="xsd:dateTime"></div>
            <div property="gr:validThrough" content="[{$aRDFaPValidity.through}]" datatype="xsd:dateTime"></div>
        [{/if}]
        [{if $oView->getRDFaVAT() > 0}]
            <div property="gr:valueAddedTaxIncluded" content="[{if $oView->getRDFaVAT() == 1}]true[{else}]false[{/if}]" datatype="xsd:boolean"></div>
        [{/if}]
        <div property="gr:hasCurrency" content="[{$currency->name}]" datatype="xsd:string"></div>
        <div property="gr:hasCurrencyValue" content="0.00" datatype="xsd:float"></div>
    </div>
</div>
[{else}]
[{include file="rdfa/details/inc/delivery.tpl"}]
[{/if}]
[{/oxhasrights}]
    <div rel="foaf:depiction v:image" resource="[{$oView->getActPicture()}]"></div>
</div>
