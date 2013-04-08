[{if $oView->getMediaFiles()}]
<strong class="boxhead">[{ oxmultilang ident="MEDIA"}]</strong>
<div class="box media">
    [{foreach from=$oView->getMediaFiles() item=oMediaUrl}]
    <p>[{$oMediaUrl->getHtml()}]</p>
    [{/foreach}]
</div>
[{/if}]