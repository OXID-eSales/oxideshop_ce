[{capture append="oxidBlock_content"}]
    <h1 id="openAccHeader" class="pageHead">[{$oView->getTitle()}]</h1>
    <div class="info">
      [{if $oView->getRegistrationStatus() == 1}]
        [{oxmultilang ident="MESSAGE_CONFIRMING_REGISTRATION"}]<br><br>[{oxmultilang ident="THANK_YOU" suffix="."}]
      [{elseif $oView->getRegistrationStatus() == 2}]
        [{oxmultilang ident="MESSAGE_SENT_CONFIRMATION_EMAIL"}]<br><br>[{oxmultilang ident="THANK_YOU" suffix="."}]
      [{/if}]
      [{if $oView->getRegistrationError() == 4}]
        <div>
          [{oxmultilang ident="MESSAGE_NOT_ABLE_TO_SEND_EMAIL"}]<br>[{oxmultilang ident="MESSAGE_VERIFY_YOUR_EMAIL"}]
        </div>
      [{/if}]
    </div>
[{/capture}]
[{if $oView->isActive('PsLogin')}]
    [{include file="layout/popup.tpl"}]
[{else}]
    [{include file="layout/page.tpl" sidebar="Left"}]
[{/if}]