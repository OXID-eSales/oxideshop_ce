[{include file="headitem.tpl" box="none "
    meta_refresh_sec=$refresh
    meta_refresh_url=$oViewConf->getSelfLink()|cat:"`&cl=`$sClassDo`&iStart=`$iStart`&fnc=run"
}]
[{ assign var='blShowStatus' value=true }]

<table cellspacing="2" cellpadding="0" width="100%">
  <colgroup width="50%"></colgroup>
  <colgroup width="50%"></colgroup>
[{if !isset($refresh) }]
    [{if $iError == -2 }]
    <tr>
        <td class="edittext" colspan="2">
            [{ oxmultilang ident="VOUCHERSERIE_EXPORTDONE" }]
            <b><a href="[{$oView->getDownloadUrl()}]" target="_blank">[{ oxmultilang ident="VOUCHERSERIE_EXPORTDOWNLOAD" }]</a></b><br>
        </td>
    </tr>
    [{/if}]
[{else}]
    [{ assign var='blShowStatus' value=false }]
    <tr>
        <td class="edittext" colspan="2">
            [{ oxmultilang ident="VOUCHERSERIE_EXPORTING" }]<br>
            [{ oxmultilang ident="VOUCHERSERIE_EXPORTED" }] [{$iExpItems|default:0}]
        </td>
    </tr>
[{/if}]

[{ if $blShowStatus }]
    [{ assign var='status' value=$oView->getStatus() }]
    <tr>
        <td class="edittext">
            [{ oxmultilang ident="GENERAL_SUM" }]:
        </td>
        <td class="edittext">
            <b>[{ $status.total }]</b>
        </td>
    </tr>
    <tr>
        <td class="edittext">
            [{ oxmultilang ident="VOUCHERSERIE_MAIN_AVAILABLE" }]:
            </td>
            <td class="edittext">
            <b>[{$status.available}]</b>
        </td>
    </tr>
    <tr>
        <td class="edittext">
            [{ oxmultilang ident="VOUCHERSERIE_MAIN_USED" }]:
            </td>
            <td class="edittext">
            <b>[{$status.used}]</b>
        </td>
    </tr>
[{/if}]
</table>

[{include file="bottomitem.tpl"}]