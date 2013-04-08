<html>
<head>
    <title>[{ oxmultilang ident="AUCTMASTER_DO_TITLE" }]</title>
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]style.css">


    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
    [{if isset($refresh)}]
    <META HTTP-EQUIV=Refresh CONTENT="[{$refresh}]; URL=[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]&cl=[{$sClassDo}]&iStart=[{ $iStart}]&fnc=run">
    [{/if}]
</head>

<table cellspacing="0" cellpadding="0" height="100%"  width="100%">
<tr>
<td bgcolor="#86A7C1" class="edittext" style="border: 1px #000000; border-style: none none solid none;" width="40">
&nbsp;&nbsp;<img src="[{$oViewConf->getImageUrl()}]/partnerarealogo.jpg" width="30" height="20" alt="" border="0">
</td>
<td class="edittext" bgcolor="#86A7C1" style="border: 1px #000000; border-style: none none solid none; color: #E3ECF4;">
    [{if !isset($refresh)}]
        [{ if !isset($iError) }]
            [{ oxmultilang ident="AUCTMASTER_DO_EXPORTNOTSTARTED" }]
        [{else}]
            [{ oxmultilang ident="AUCTMASTER_DO_EXPORTEND" }]
            [{ if $iError}]

                [{ if $iError == -2}]
                    <strong>[{ oxmultilang ident="AUCTMASTER_DO_SUCCESS" }]<br>
                [{/if}]

                [{ if $iError == -1}][{ oxmultilang ident="AUCTMASTER_DO_UNKNOWNERROR" }][{/if}]
                [{ if $iError == 1 }][{ oxmultilang ident="AUCTMASTER_DO_EXPORTFILE1" }]([{$sOutputFile}]) [{ oxmultilang ident="AUCTMASTER_DO_EXPORTFILE2" }][{/if}]

            [{/if}]
        [{/if}]
    [{else}]
     [{ oxmultilang ident="AUCTMASTER_DO_EXPORTING1" }][{ $iStart|default:0}] [{ oxmultilang ident="AUCTMASTER_DO_EXPORTING2" }] [{ $iEnd }].
    [{/if}]
</td>
</table>

[{include file="bottomitem.tpl"}]
