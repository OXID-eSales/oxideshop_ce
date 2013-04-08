[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="PAGE_ACCOUNT_REGISTER_CONFIRM_WELCOME"|oxmultilangassign}]
    <h1 id="openAccHeader" class="pageHead">[{ oxmultilang ident="PAGE_ACCOUNT_REGISTER_CONFIRM_WELCOME" }]</h1>
    <div class="box info">
      [{ oxmultilang ident="PAGE_ACCOUNT_REGISTER_CONFIRM_CONFIRMED" }]
    </div>
    [{insert name="oxid_tracker" title=$template_title}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]

