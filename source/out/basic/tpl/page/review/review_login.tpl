[{assign var="template_title" value="REVIEW_LOGIN_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title noindex=1}]

[{include file="inc/cmp_login.tpl"}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
