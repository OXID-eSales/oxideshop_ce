[{assign var="template_title" value="IMPRESUSUM_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

<strong id="test_impressumHeader" class="boxhead">[{ oxmultilang ident="IMPRESUSUM_IMPRESUSUM" }]</strong>
<div class="box info">[{ oxcontent ident="oximpressum" }]</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
