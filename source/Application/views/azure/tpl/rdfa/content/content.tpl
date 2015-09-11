[{assign var="sTplName" value=$oView->getContentPageTpl()}]
[{if $sTplName}]
    [{assign var=sRDFaUrl value=$oView->getLink()}]
    <div xmlns="http://www.w3.org/1999/xhtml"
        xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
        xmlns:gr="http://purl.org/goodrelations/v1#"
        xmlns:foaf="http://xmlns.com/foaf/0.1/"
        xmlns:vcard="http://www.w3.org/2006/vcard/ns#"
        xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
        xml:base="[{$sRDFaUrl}]">
        [{foreach from=$oView->getContentPageTpl() item=sTplName}]
            [{include file=$sTplName}]
        [{/foreach}]
    </div>
[{/if}]