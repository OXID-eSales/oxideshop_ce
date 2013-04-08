[{include file="headitem.tpl" box="export "
    title="AUCTMASTER_DO_TITLE"|oxmultilangassign
    meta_refresh_sec=$refresh
    meta_refresh_url=$oViewConf->getSelfLink()|cat:"&cl=`$sClassDo`&iStart=`$iStart`&fnc=run"
}]

[{if !isset($refresh)}]
    [{ if !isset($iError) }]
        [{ oxmultilang ident="AUCTMASTER_DO_EXPORTNOTSTARTED" }]
    [{else}]
        [{ if $iError}]
            [{ if $iError == -2}]
                [{ oxmultilang ident="AUCTMASTER_DO_EXPORTEND" }]
                <b>[{ oxmultilang ident="DYNBASE_DO_SUCCESS" }] <a href="[{$sDownloadFile}]" target="_blank">[{ oxmultilang ident="DYNBASE_DO_HERE" }]</a> [{ oxmultilang ident="DYNBASE_DO_DOWNLOAD" }]</b><br>
                [{ oxmultilang ident="DYNBASE_DO_LINK" }]<em>[{$sDownloadFile}]</em>
            [{/if}]

            [{ if $iError == -1}][{ oxmultilang ident="AUCTMASTER_DO_UNKNOWNERROR" }][{/if}]
            [{ if $iError == 1 }][{ oxmultilang ident="AUCTMASTER_DO_EXPORTFILE1" }] ([{$sOutputFile}]) [{ oxmultilang ident="AUCTMASTER_DO_EXPORTFILE2" }][{/if}]
        [{/if}]
    [{/if}]
[{else}]
  [{ oxmultilang ident="GENEXPORT_EXPRUNNING" }] [{ oxmultilang ident="GENEXPORT_EXPORTEDITEMS" }] [{$iExpItems|default:0}]
[{/if}]

[{include file="bottomitem.tpl"}]
