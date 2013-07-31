
<table>
<tr><td colspan=2><h2>oxchkversion detected at <a href="[{ $sSelfLink }]">[{ $sSelfLink }]</a> at [{ $sDateTime }]</h2></td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_EDITION'}]</b></td><td>[{ $sEdition }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_VERSION'}]</b></td><td>[{ $sVersion }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_REVISION'}]</b></td><td>[{ $sRevision }]</td></tr>

<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>
<tr><td colspan="2"><h2>[{ oxmultilang ident='OXCHKVERSION_SUMMARY'}]</h2></td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_OK'}]</b></td><td>[{ $aResultCount.OK }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_MODIFIED'}]</b></td><td>[{ $aResultCount.MODIFIED }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_VERSION_MISMATCH'}]</b></td><td>[{ $aResultCount.VERSIONMISMATCH }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_UNKNOWN'}]</b></td><td>[{ $aResultCount.UNKNOWN }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXCHKVERSION_NUMBER_OF_INVESTIGATED_FILES'}]:</b>   </td><td>[{ $iFilesCount }]</td></tr>

<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>

[{ if !empty($blShopIsOK) }]
<tr><td colspan="2"><b><span style="color:green">[{ oxmultilang ident='OXCHKVERSION_SHOP_ORIGINAL'}]</span></b></td></tr>
[{else}]
<tr><td colspan="2"><b><span style="color:red">[{ oxmultilang ident='OXCHKVERSION_SHOP_DOES_NOT_FIT'}] [{ $sVersionTag }]</span></b></td></tr>
[{ /if}]

[{ if ( $aResultCount.MODIFIED > 0 ) || ( $aResultCount.VERSIONMISMATCH  > 0 ) }]
<tr><td colspan="2"><b>&nbsp;</b></td></tr>
<tr><td colspan="2"><h2>[{ oxmultilang ident='OXCHKVERSION_HINTS'}]:</h2></td></tr>

    [{ if $aResultCount.MODIFIED > 0 }]
    <tr><td colspan="2">* [{ oxmultilang ident='OXCHKVERSION_MODIFIEDHINTS1'}]</td></tr>
    <tr><td colspan="2">* [{ oxmultilang ident='OXCHKVERSION_MODIFIEDHINTS2'}]</td></tr>
    [{ /if}]

    [{ if $aResultCount.VERSIONMISMATCH > 0 }]
    <tr><td colspan="2">* [{ oxmultilang ident='OXCHKVERSION_VERSIONMISMATCHHINTS'}]</td></tr>
    [{ /if}]
[{ /if}]

[{ if $aResultOutput|@count > 0 }]
    <tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>
    [{foreach from=$aResultOutput item=aResultItem }]
    <tr><td>[{ $aResultItem.file }]</td>
        <td><b style="color: [{ $aResultItem.color }]">[{ $aResultItem.message }]</b></td>
    </tr>
    [{/foreach}]
[{ /if}]

</table>

</body>
</html>