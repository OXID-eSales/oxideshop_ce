[{strip}]
<div id="breadCrumb">
    <span>[{ oxmultilang ident="YOU_ARE_HERE" suffix="COLON" }]</span>
[{foreach from=$oView->getBreadCrumb() item=sCrum}]
    &nbsp;/&nbsp;[{if $sCrum.link }]<a href="[{ $sCrum.link }]" title="[{ $sCrum.title|escape:'html'}]">[{/if}][{$sCrum.title}][{if $sCrum.link }]</a>[{/if}]
[{/foreach}]
</div>
[{/strip}]
