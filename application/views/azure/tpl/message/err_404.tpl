[{capture append="oxidBlock_content"}]
<h1 class="pageHead">[{ oxmultilang ident="ERROR" }]</h1>
<p>
    [{if $sUrl}]
        [{assign var="sModifiedUrl" value=$sUrl|escape }]
        [{assign var="sModifiedUrl" value="<i><strong>'"|cat:$sModifiedUrl|cat:"'</strong></i>"}]
        [{ oxmultilang ident="ERROR_404" args=$sModifiedUrl }]
    [{else}]
        [{ oxmultilang ident="ERROR_404" args=''}]
    [{/if}]
</p>
[{/capture}]
[{include file="layout/page.tpl"}]