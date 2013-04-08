[{capture append="oxidBlock_pageBody"}]

  <div class="errorBox">
      <div class="errHead">[{ oxmultilang ident="[{ oxmultilang ident="MESSAGE_ERR_UNKNOWN_OXIDESHOPERROR" }] #[{ $oView->getErrorNumber() }] !</div>
      <div class="errBody">[{ oxmultilang ident="MESSAGE_ERR_UNKNOWN_VERSIONEXPIRED1" }]</div>
  </div>

[{/capture}]
[{include file="layout/base.tpl"}]