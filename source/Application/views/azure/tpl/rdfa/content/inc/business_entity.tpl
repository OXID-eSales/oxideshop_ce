[{assign var=aExtends value=$oView->getBusinessEntityExtends()}]
<div typeof="gr:BusinessEntity" about="[{$sRDFaUrl}]#companydata">
    <div property="gr:legalName vcard:fn" content="[{$oxcmp_shop->oxshops__oxcompany->value}]" datatype="xsd:string"></div>
    <div rel="vcard:adr">
        <div typeof="vcard:Address">
            <div property="vcard:country-name" content="[{$oxcmp_shop->oxshops__oxcountry->value}]"></div>
            <div property="vcard:locality" content="[{$oxcmp_shop->oxshops__oxcity->value}]"></div>
            <div property="vcard:postal-code" content="[{$oxcmp_shop->oxshops__oxzip->value}]"></div>
            <div property="vcard:street-address" content="[{$oxcmp_shop->oxshops__oxstreet->value}]"></div>
        </div>
    </div>
[{if $aExtends.sRDFaLatitude && $aExtends.sRDFaLongitude}]
    <div rel="vcard:geo">
        <div typeof="rdf:Description">
            <div property="vcard:latitude" content="[{$aExtends.sRDFaLatitude}]" datatype="xsd:float"></div>
            <div property="vcard:longitude" content="[{$aExtends.sRDFaLongitude}]" datatype="xsd:float"></div>
        </div>
    </div>
[{/if}]
    <div property="vcard:tel" content="[{$oxcmp_shop->oxshops__oxtelefon->value}]"></div>
    <div property="vcard:fax" content="[{$oxcmp_shop->oxshops__oxtelefax->value}]"></div>
[{if $aExtends.sRDFaLogoUrl}]
    <div rel="vcard:logo foaf:logo" resource="[{$aExtends.sRDFaLogoUrl}]"></div>
[{/if}]
[{if $aExtends.sRDFaDUNS}]
    <div property="gr:hasDUNS" content="[{$aExtends.sRDFaDUNS}]" datatype="xsd:string"></div>
[{/if}]
[{if $aExtends.sRDFaISIC}]
    <div property="gr:hasISICv4" content="[{$aExtends.sRDFaISIC}]" datatype="xsd:int"></div>
[{/if}]
[{if $aExtends.sRDFaGLN}]
    <div property="gr:hasGlobalLocationNumber" content="[{$aExtends.sRDFaGLN}]" datatype="xsd:string"></div>
[{/if}]
[{if $aExtends.sRDFaNAICS}]
    <div property="gr:hasNAICS" content="[{$aExtends.sRDFaNAICS}]" datatype="xsd:int"></div>
[{/if}]
</div>