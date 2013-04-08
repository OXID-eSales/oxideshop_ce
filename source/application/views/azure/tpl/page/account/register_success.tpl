[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="MESSAGE_WELCOME_REGISTERED_USER"|oxmultilangassign }]
    <h1 id="openAccHeader" class="pageHead">[{ oxmultilang ident="MESSAGE_WELCOME_REGISTERED_USER" }]</h1>
    <div class="box info">
      [{if $oView->getRegistrationStatus() == 1}]
        [{ oxmultilang ident="MESSAGE_CONFIRMING_REGISTRATION" }]<br><br>[{ oxmultilang ident="THANK_YOU" SUFFIC="." }]
      [{elseif $oView->getRegistrationStatus() == 2}]
        [{ oxmultilang ident="MESSAGE_SENT_CONFIRMATION_EMAIL" }]<br><br>[{ oxmultilang ident="THANK_YOU" SUFFIC="." }]
      [{/if}]

      [{if $oView->getRegistrationError() == 4}]
        <div>
          [{ oxmultilang ident="MESSAGE_NOT_ABLE_TO_SEND_EMAIL" }]<br>[{ oxmultilang ident="MESSAGE_VERIFY_YOUR_EMAIL" }]
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