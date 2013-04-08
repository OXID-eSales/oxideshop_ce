[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="MESSAGE_WELCOME_REGISTERED_USER"|oxmultilangassign}]
    <h1 id="openAccHeader" class="pageHead">[{ oxmultilang ident="MESSAGE_WELCOME_REGISTERED_USER" }]</h1>
    <div class="box info">
      [{ oxmultilang ident="MESSAGE_ACCOUNT_REGISTRATION_CONFIRMED" }]
    </div>
    [{insert name="oxid_tracker" title=$template_title}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]

