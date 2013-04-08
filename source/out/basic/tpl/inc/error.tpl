[{if count($Errorlist)>0 }]
<div class="errorbox[{if $errdisplay == 'inbox'}] inbox[{/if}]">
    [{foreach from=$Errorlist item=oEr key=key }]
        <p>[{ $oEr->getOxMessage()}]</p>
    [{/foreach}]
</div>
[{/if}]