[{capture append="oxidBlock_pageBody"}]
    <div class="errorBox">
          [{if count($Errors)>0 && count($Errors.default) > 0}]
          <div class="status error corners">
              [{foreach from=$Errors.default item=oEr key=key }]
                  <p>[{ $oEr->getOxMessage()}]</p>

                  <p class="stackTrace">[{ $oEr->getStackTrace()|nl2br }];</p>
              [{/foreach}]
          </div>
          [{/if}]
    </div>
[{/capture}]

[{include file="layout/base.tpl"}]