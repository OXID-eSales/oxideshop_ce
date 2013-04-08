[{assign var="template_title" value="ERR_404TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=""}]
    <h1 id="test_errorHeader" class="boxhead">[{$template_title}]</h1>
    <div id="test_errorBody" class="box">
        [{if $sUrl}]
            [{ oxmultilang ident="ERR_404_PREURL" }] <i><strong>'[{$sUrl|escape}]'</strong></i> [{ oxmultilang ident="ERR_404_POSTURL" }]
        [{else}]
            [{ oxmultilang ident="ERR_404" }]
        [{/if}]
    </div>
[{include file="_footer.tpl"}]
