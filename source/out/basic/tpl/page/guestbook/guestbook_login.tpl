[{assign var="template_title" value="GUESTBOOK_LOGIN_LOGIN"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

[{include file="inc/cmp_login.tpl"}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
