[{include file="headitem.tpl" box="none "
    meta_refresh_sec=$refresh
    meta_refresh_url=$oViewConf->getSelfLink()|cat:"&cl=`$sClassDo`&iStart=`$iStart`&fnc=run"
}]
[{assign var='blShowStatus' value=true}]

<table cellspacing="2" cellpadding="0" width="100%">
  <colgroup width="50%"></colgroup>
  <colgroup width="50%"></colgroup>
[{if !isset($refresh)}]
    [{if $iError == -2}]
    <tr>
        <td class="edittext" colspan="2">
            [{oxmultilang ident="VOUCHERSERIE_GENERATEDONE"}]
        </td>
    </tr>
    [{/if}]
[{else}]
    [{assign var='blShowStatus' value=false}]
    <tr>
        <td class="edittext" colspan="2">
            [{oxmultilang ident="VOUCHERSERIE_GENERATING"}]<br>
            [{oxmultilang ident="VOUCHERSERIE_GENERATED"}] [{$iExpItems|default:0}]
        </td>
    </tr>
[{/if}]

[{if $blShowStatus}]
    [{assign var='status' value=$oView->getStatus()}]
    <tr>
        <td class="edittext">
            [{oxmultilang ident="GENERAL_SUM"}]:
        </td>
        <td class="edittext">
            <b>[{$status.total|default:"0"}]</b>
        </td>
    </tr>
    <tr>
        <td class="edittext">
            [{oxmultilang ident="VOUCHERSERIE_MAIN_AVAILABLE"}]:
            </td>
            <td class="edittext">
            <b>[{$status.available|default:"0"}]</b>
        </td>
    </tr>
    <tr>
        <td class="edittext">
            [{oxmultilang ident="VOUCHERSERIE_MAIN_USED"}]:
            </td>
            <td class="edittext">
            <b>[{$status.used|default:"0"}]</b>
        </td>
    </tr>
[{/if}]
</table>

[{include file="bottomitem.tpl"}]