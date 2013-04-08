[{assign var="template_title" value="ERR_SETUP_TITLE"|oxmultilangassign}]
[{include file="_header_plain.tpl" title=$template_title location=$template_title}]

  <div class="errorbox">
      <div class="errhead">[{ oxmultilang ident="ERR_UPDATE_OXIDESHOPERROR" }]</div>
      <div class="errbody">[{ oxmultilang ident="ERR_UPDATE_DELETEDIRECTORY1" }] [{ $oViewConf->getBaseDir() }][{ oxmultilang ident="ERR_UPDATE_DELETEDIRECTORY2" }]</div>
  </div>

[{include file="_footer_plain.tpl"}]
