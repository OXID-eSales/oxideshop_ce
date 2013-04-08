[{include file="_header_plain.tpl"}]
    <div class="errorBox" style="width: auto;">
          [{if count($Errors)>0 && count($Errors.default) > 0}]
              [{foreach from=$Errors.default item=oEr key=key }]
                  <div class="errhead">[{ $oEr->getOxMessage()}]<div>
                  <div class="errbody">[{ $oEr->getStackTrace()|nl2br }];</div>
              [{/foreach}]
          [{/if}]
    </div>
[{include file="_footer_plain.tpl"}]