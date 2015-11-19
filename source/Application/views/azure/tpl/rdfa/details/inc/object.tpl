<div rel="gr:includes">
    [{if $oProduct->getVariantsCount() || $oView->drawParentUrl()}]
        <div typeof="gr:ProductOrServiceModel" about="[{$sRDFaUrl}]#productdata">
    [{else}]
        <div typeof="gr:SomeItems" about="[{$sRDFaUrl}]#productdata">
    [{/if}]
              [{if $oProduct->oxarticles__oxtitle->value}]
                <div property="gr:name" content="[{$oProduct->oxarticles__oxtitle->value|strip_tags|strip}]" [{if $oView->getActiveLangAbbr()}] xml:lang="[{$oView->getActiveLangAbbr()}]"[{/if}]></div>
               [{/if}]
               [{oxhasrights ident="SHOWLONGDESCRIPTION"}]
               [{assign var="oLongdesc" value=$oProduct->getLongDescription()}]
               [{if $oLongdesc->value}]
                   [{capture assign="oLongdescEvaluated"}][{oxeval var=$oLongdesc->value}][{/capture}]
                   <div property="gr:description" content="[{oxeval var=$oLongdescEvaluated|strip_tags|strip}]" [{if $oView->getActiveLangAbbr()}] xml:lang="[{$oView->getActiveLangAbbr()}]"[{/if}]></div>
               [{/if}]
            [{/oxhasrights}]
            <div rel="foaf:depiction v:image" resource="[{$oView->getActPicture()}]"></div>
            <div property="gr:hasStockKeepingUnit" content="[{$oProduct->oxarticles__oxartnum->value}]" datatype="xsd:string"></div>
            [{if $oProduct->oxarticles__oxmpn->value}]
                <div property="gr:hasMPN" content="[{$oProduct->oxarticles__oxmpn->value}]" datatype="xsd:string"></div>
            [{/if}]
            [{if $oProduct->oxarticles__oxean->value}]
                <div property="gr:hasGTIN-14" content="[{$oProduct->oxarticles__oxean->value}]" datatype="xsd:string"></div>
            [{elseif $oProduct->oxarticles__oxdistean->value}]
                <div property="gr:hasGTIN-14" content="[{$oProduct->oxarticles__oxdistean->value}]" datatype="xsd:string"></div>
            [{/if}]
            [{if $oView->getRDFaGenericCondition()}]
                <div property="gr:condition" content="[{$oView->getRDFaGenericCondition()}]" xml:lang="en"></div>
            [{/if}]
            [{foreach from=$oView->getCatTreePath() item=oCatPath name="detailslocation"}]
                [{if $smarty.foreach.detailslocation.last}]
                    <div property="gr:category" content="[{$oCatPath->oxcategories__oxtitle->value|strip_tags}]" [{if $oView->getActiveLangAbbr()}] xml:lang="[{$oView->getActiveLangAbbr()}]"[{/if}]></div>
                [{/if}]
            [{/foreach}]
            [{if $oProduct->oxarticles__oxlength->value}]
                <div rel="gr:depth">
                    <div typeof="gr:QuantitativeValue">
                        <div property="gr:hasValue" content="[{$oProduct->oxarticles__oxlength->value}]" datatype="xsd:float"></div>
                        <div property="gr:hasUnitOfMeasurement" content="MTR" datatype="xsd:string"></div>
                    </div>
                </div>
            [{/if}]
            [{if $oProduct->oxarticles__oxwidth->value}]
                <div rel="gr:width">
                    <div typeof="gr:QuantitativeValue">
                        <div property="gr:hasValue" content="[{$oProduct->oxarticles__oxwidth->value}]" datatype="xsd:float"></div>
                        <div property="gr:hasUnitOfMeasurement" content="MTR" datatype="xsd:string"></div>
                    </div>
                </div>
            [{/if}]
            [{if $oProduct->oxarticles__oxheight->value}]
                <div rel="gr:height">
                    <div typeof="gr:QuantitativeValue">
                        <div property="gr:hasValue" content="[{$oProduct->oxarticles__oxheight->value}]" datatype="xsd:float"></div>
                        <div property="gr:hasUnitOfMeasurement" content="MTR" datatype="xsd:string"></div>
                    </div>
                </div>
            [{/if}]
            [{if $oProduct->oxarticles__oweight->value}]
                <div rel="gr:weight">
                    <div typeof="gr:QuantitativeValue">
                        <div property="gr:hasValue" content="[{$oProduct->oxarticles__oweight->value}]" datatype="xsd:float"></div>
                        <div property="gr:hasUnitOfMeasurement" content="KGM" datatype="xsd:string"></div>
                    </div>
                </div>
            [{/if}]
            </div>
        </div>
        [{if $oView->getBundleArticle()}]
            [{assign var="oBundleProduct" value=$oView->getBundleArticle()}]
            <div rel="gr:includes" resource="[{$oBundleProduct->getLink()}]#productdata"></div>
        [{/if}]
