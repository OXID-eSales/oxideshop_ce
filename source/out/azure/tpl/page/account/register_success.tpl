[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="PAGE_ACCOUNT_REGISTER_SUCCESS_WELCOME"|oxmultilangassign }]
    <h1 id="openAccHeader" class="pageHead">[{ oxmultilang ident="PAGE_ACCOUNT_REGISTER_SUCCESS_WELCOME" }]</h1>
    <div class="box info">
      [{if $oView->getRegistrationStatus() == 1}]
        [{ oxmultilang ident="PAGE_ACCOUNT_REGISTER_SUCCESS_EMAILCONFIRMATION" }]
      [{elseif $oView->getRegistrationStatus() == 2}]
        [{ oxmultilang ident="PAGE_ACCOUNT_REGISTER_SUCCESS_ACTIVATIONEMAIL" }]
      [{/if}]

      [{if $oView->getRegistrationError() == 4}]
        <div>
          [{ oxmultilang ident="PAGE_ACCOUNT_REGISTER_SUCCESS_NOTABLETOSENDEMAIL" }]
        </div>
      [{/if}]
    </div>
    [{ insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{if $oView->isActive('PsLogin') }]
    [{include file="layout/popup.tpl"}]
[{else}]
    [{include file="layout/page.tpl" sidebar="Left"}]
[{/if}]