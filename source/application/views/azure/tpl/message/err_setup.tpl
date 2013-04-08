[{capture append="oxidBlock_pageBody"}]
<h1 class="pageHead">[{ oxmultilang ident="MESSAGE_ERR_SETUP_TITLE" }]</h1>
[{capture append="_error_content"}]
<div>
  <div>[{ oxmultilang ident="MESSAGE_ERR_SETUP_OXIDESHOPERROR" }]</div>
  <div>[{ oxmultilang ident="MESSAGE_ERR_SETUP_VERSIONEXPIRED1" }] [{ $oViewConf->getBaseDir() }][{ oxmultilang ident="MESSAGE_ERR_SETUP_VERSIONEXPIRED2" }]</div>
</div>
[{/capture}]
[{include file="message/error.tpl" statusMessage=""|implode:$_error_content}]
[{/capture}]
[{include file="layout/base.tpl"}]
