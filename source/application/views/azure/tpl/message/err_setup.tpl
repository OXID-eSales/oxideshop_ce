[{capture append="oxidBlock_pageBody"}]
<h1 class="pageHead">[{ oxmultilang ident="ERROR_MESSAGE_OXID_SHOP_ERROR" }]</h1>
[{capture append="_error_content"}]
<div>
  <div>[{ oxmultilang ident="ERROR_MESSAGE_OXID_SHOP_ERROR" }]</div>
  <div>[{ oxmultilang ident="MESSAGE_PLEASE_DELETE_DIRECTORY_SET" args=$oViewConf->getBaseDir()}]</div>
</div>
[{/capture}]
[{include file="message/error.tpl" statusMessage=""|implode:$_error_content}]
[{/capture}]
[{include file="layout/base.tpl"}]
