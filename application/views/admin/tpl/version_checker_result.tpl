
<table>
<tr><td colspan=2><h3><a id="chkversion"></a>[{ oxmultilang ident='OXDIAG_VERSIONCHECKER'}]</h3></td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_EDITION'}]</b></td><td>[{ $sEdition }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_VERSION'}]</b></td><td>[{ $sVersion }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_REVISION'}]</b></td><td>[{ $sRevision }]</td></tr>

<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>
<tr><td colspan="2"><h4>[{ oxmultilang ident='OXDIAG_SUMMARY'}]</h4></td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_OK'}]</b></td><td>[{ $aResultSummary.OK }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_MODIFIED'}]</b></td><td>[{ $aResultSummary.MODIFIED }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_VERSION_MISMATCH'}]</b></td><td>[{ $aResultSummary.VERSIONMISMATCH }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_UNKNOWN'}]</b></td><td>[{ $aResultSummary.UNKNOWN }]</td></tr>
<tr><td><b>[{ oxmultilang ident='OXDIAG_NUMBER_OF_INVESTIGATED_FILES'}]:</b>   </td><td>[{ $aResultSummary.FILES }]</td></tr>

<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>

[{ if $aResultSummary.SHOP_OK }]
<tr><td colspan="2"><b><span style="color:green">[{ oxmultilang ident='OXDIAG_SHOP_ORIGINAL'}]</span></b></td></tr>
[{else}]
<tr><td colspan="2"><b><span style="color:red">[{ oxmultilang ident='OXDIAG_SHOP_DOES_NOT_FIT'}] [{ $sEdition }]_[{ $sVersion }]_[{ $sRevision }]</span></b></td></tr>
[{ /if}]

[{ if ( $aResultSummary.MODIFIED > 0 ) || ( $aResultSummary.VERSIONMISMATCH  > 0 ) }]
<tr><td colspan="2"><b>&nbsp;</b></td></tr>
<tr><td colspan="2"><h4>[{ oxmultilang ident='OXDIAG_HINTS'}]:</h4></td></tr>

    [{ if $aResultSummary.MODIFIED > 0 }]
    <tr><td colspan="2">* [{ oxmultilang ident='OXDIAG_MODIFIEDHINTS1'}]</td></tr>
    <tr><td colspan="2">* [{ oxmultilang ident='OXDIAG_MODIFIEDHINTS2'}]</td></tr>
    [{ /if}]

    [{ if $aResultSummary.VERSIONMISMATCH > 0 }]
    <tr><td colspan="2">* [{ oxmultilang ident='OXDIAG_VERSIONMISMATCHHINTS'}]</td></tr>
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