[{block name="admin_inc_error"}]
    [{if $Errors.default|is_array}]
    <div class="errorbox">
        [{foreach from=$Errors.default item=oEr key=key}]
            <p>[{$oEr->getOxMessage()}]</p>
        [{/foreach}]
    </div>
    [{/if}]
[{/block}]