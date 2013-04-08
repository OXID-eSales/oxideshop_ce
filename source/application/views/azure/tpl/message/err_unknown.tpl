[{capture append="oxidBlock_pageBody"}]

  <div class="errorBox">
      <div class="errHead">[{ oxmultilang ident="[{ oxmultilang ident="ERROR_MESSAGE_UNKNOWN_ERROR" }] #[{ $oView->getErrorNumber() }] !</div>
      <div class="errBody">[{ oxmultilang ident="MESSAGE_PLEASE_CONTACT_SUPPORT" }]</div>
  </div>

[{/capture}]
[{include file="layout/base.tpl"}]