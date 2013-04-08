[{assign var="template_title" value="ERR_UNKNOWN_TITLE"|oxmultilangassign}]
[{include file="_header_plain.tpl" title=$template_title location=$template_title}]

  <div class="errorbox">
      <div class="errhead">[{ oxmultilang ident="[{ oxmultilang ident="ERR_UNKNOWN_OXIDESHOPERROR" }] #[{ $oView->getErrorNumber() }] !</div>
      <div class="errbody">[{ oxmultilang ident="ERR_UNKNOWN_VERSIONEXPIRED1" }]</div>
  </div>

[{include file="_footer_plain.tpl"}]
