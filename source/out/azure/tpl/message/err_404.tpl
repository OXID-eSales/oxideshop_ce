[{capture append="oxidBlock_content"}]
<h1 class="pageHead">[{ oxmultilang ident="MESSAGE_ERR_404TITLE" }]</h1>
<p>
    [{if $sUrl}]
        [{ oxmultilang ident="MESSAGE_ERR_404_PREURL" }] <i><strong>'[{$sUrl|escape}]'</strong></i> [{ oxmultilang ident="MESSAGE_ERR_404_POSTURL" }]
    [{else}]
        [{ oxmultilang ident="MESSAGE_ERR_404" }]
    [{/if}]
</p>
[{/capture}]
[{include file="layout/page.tpl"}]