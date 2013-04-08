[{block name="admin_inc_error"}]
    [{if count($Errors.default)>0 }]
    <div class="errorbox">
        [{foreach from=$Errors.default item=oEr key=key }]
            <p>[{ $oEr->getOxMessage()}]</p>
        [{/foreach}]
    </div>
    [{/if}]
[{/block}]