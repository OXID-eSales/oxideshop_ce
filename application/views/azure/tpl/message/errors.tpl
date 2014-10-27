[{if count($Errors)>0 && count($Errors.default) > 0}]
<div class="status error corners">
    [{foreach from=$Errors.default item=oEr key=key }]
        <p>[{ $oEr->getOxMessage()}]</p>
    [{/foreach}]
</div>
[{/if}]