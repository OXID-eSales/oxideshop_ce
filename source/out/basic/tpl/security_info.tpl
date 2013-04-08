[{assign var="template_title" value="SECURITY_INFO_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

<strong id="test_securityInfoHeader" class="boxhead">[{$template_title}]</strong>
<div class="box info">
  [{ oxcontent ident="oxsecurityinfo" }]
</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
