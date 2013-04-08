[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
function changeFnc( fncName )
{
    var langvar = document.myedit.elements['fnc'];
    if (langvar != null )
        langvar.value = fncName;
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="voucherserie_main">
</form>



<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext" width="355">

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="voucherserie_main">
<input type="hidden" name="fnc" value="save">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxvoucherseries__oxid]" value="[{$oxid}]">
<input type="hidden" name="randomNr" value="true">

        <table cellspacing="2" cellpadding="0" border="0">
        [{block name="admin_voucherserie_main_form"}]
            <tr>
                <td class="edittext" width="160">
                [{ oxmultilang ident="GENERAL_NAME" }]
                </td>
                <td class="edittext" width="195">
                <input class="editinput" type="text" size="36" name="editval[oxvoucherseries__oxserienr]" value="[{$edit->oxvoucherseries__oxserienr->value}]" onClick="this.select()" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_NAME" }]
                </td>
            </tr>
            <tr>
                <td class="edittext" width="90">
                [{ oxmultilang ident="GENERAL_DESCRIPTION" }]
                </td>
                <td class="edittext">
                <input class="editinput" type="text" size="36" name="editval[oxvoucherseries__oxseriedescription]" value="[{$edit->oxvoucherseries__oxseriedescription->value}]" onClick="this.select()" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_DESCRIPTION" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_BEGINDATE" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="27" name="editval[oxvoucherseries__oxbegindate]" value="[{$edit->oxvoucherseries__oxbegindate|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] onClick="this.select()" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_BEGINDATE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_ENDDATE" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="27" name="editval[oxvoucherseries__oxenddate]" value="[{$edit->oxvoucherseries__oxenddate|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] onClick="this.select()" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_ENDDATE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="VOUCHERSERIE_MAIN_DISCOUNT" }]
                </td>
                <td class="edittext">
                <input class="editinput" type="text" size="15" name="editval[oxvoucherseries__oxdiscount]" value="[{$edit->oxvoucherseries__oxdiscount->value}]" onClick="this.select()" [{ $readonly }]>
                <select class="editinput" name="editval[oxvoucherseries__oxdiscounttype]" [{ $readonly }]>
                    <option value="absolute" [{ if $edit->oxvoucherseries__oxdiscounttype->value == "absolute"}]selected[{/if}]>abs</option>
                    <option value="percent" [{ if $edit->oxvoucherseries__oxdiscounttype->value == "percent"}]selected[{/if}]>%</option>
                </select>
                [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_DISCOUNT" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="VOUCHERSERIE_MAIN_MINORDERPRICE" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="15" name="editval[oxvoucherseries__oxminimumvalue]" value="[{$edit->oxvoucherseries__oxminimumvalue->value }]" onClick="this.select()" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_MINORDERPRICE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="VOUCHERSERIE_MAIN_ALLOWSAMESERIES" }]
                </td>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_YES" }]&nbsp;<input type="radio" name="editval[oxvoucherseries__oxallowsameseries]" value="1" [{if $edit->oxvoucherseries__oxallowsameseries->value}]checked[{/if}] [{ $readonly }]>&nbsp;&nbsp;
                [{ oxmultilang ident="GENERAL_NO" }]&nbsp;<input type="radio" name="editval[oxvoucherseries__oxallowsameseries]" value="0" [{if !$edit->oxvoucherseries__oxallowsameseries->value}]checked[{/if}] [{ $readonly }]>
                [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_ALLOWSAMESERIES" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="VOUCHERSERIE_MAIN_ALLOWOTHERSERIES" }]
                </td>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_YES" }]&nbsp;<input type="radio" name="editval[oxvoucherseries__oxallowotherseries]" value="1" [{if $edit->oxvoucherseries__oxallowotherseries->value}]checked[{/if}] [{ $readonly }]>&nbsp;&nbsp;
                [{ oxmultilang ident="GENERAL_NO" }]&nbsp;<input type="radio" name="editval[oxvoucherseries__oxallowotherseries]" value="0" [{if !$edit->oxvoucherseries__oxallowotherseries->value}]checked[{/if}] [{ $readonly }]>
                [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_ALLOWOTHERSERIES" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="VOUCHERSERIE_MAIN_SAMESEROTHERORDER" }]
                </td>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_YES" }]&nbsp;<input type="radio" name="editval[oxvoucherseries__oxallowuseanother]" value="1" [{if $edit->oxvoucherseries__oxallowuseanother->value}]checked[{/if}] [{ $readonly }]>&nbsp;&nbsp;
                [{ oxmultilang ident="GENERAL_NO" }]&nbsp;<input type="radio" name="editval[oxvoucherseries__oxallowuseanother]" value="0" [{if !$edit->oxvoucherseries__oxallowuseanother->value}]checked[{/if}] [{ $readonly }]>
                [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_SAMESEROTHERORDER" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="VOUCHERSERIE_MAIN_CALCULATEONCE" }]
                </td>
                <td class="edittext">
                <input type="hidden" name="editval[oxvoucherseries__oxcalculateonce]" value="0" [{ $readonly }]>
                <input type="checkbox" name="editval[oxvoucherseries__oxcalculateonce]" value="1" [{if $edit->oxvoucherseries__oxcalculateonce->value}]checked[{/if}] [{ $readonly }]>
                [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_CALCULATEONCE" }]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" [{ $readonly }] onClick="Javascript:changeFnc('save');">
            </td>
        </tr>
        </table>

</form>

    </td>
    <td width="355" valign="top">

        [{if $oxid != "-1" }]

        <form name="myexport" id="myexport" action="[{ $oViewConf->getSelfLink() }]" target="dynexport_do" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="">
        <input type="hidden" name="fnc" value="start">
        <input type="hidden" name="voucherid" value="[{$oxid}]">

        <fieldset title="[{ oxmultilang ident="VOUCHERSERIE_MAIN_VOUCHERSTATISTICS" }]" style="padding-left: 5px; padding-right: 5px;">
            <legend>[{ oxmultilang ident="VOUCHERSERIE_MAIN_VOUCHERSTATISTICS" }]</legend>
            <iframe src="[{$oViewConf->getSelfLink()}]&cl=[{$sClassDo}]&voucherid=[{$oxid}]" width="100%" height="80" frameborder="0" name="dynexport_do" align="left"></iframe>
        </fieldset>
        <br>

        <table cellspacing="2" cellpadding="0" width="">
            [{block name="admin_voucherserie_main_genvoucher"}]
                <tr>
                    <td class="edittext" colspan="2">
                        <b>[{ oxmultilang ident="VOUCHERSERIE_MAIN_NEWVOUCHER" }]</b> (optional)<br><br>
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        [{ oxmultilang ident="VOUCHERSERIE_MAIN_RANDOMNUM" }]
                    </td>
                    <td>
                        <input type="radio" name="randomVoucherNr" value="1" checked [{ $readonly }]>
                        [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_RANDOMNUM" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        [{ oxmultilang ident="VOUCHERSERIE_MAIN_VOUCHERNUM" }]
                    </td>
                    <td>
                        <input type="radio" name="randomVoucherNr" id="randomVoucherNr" value="0" [{ $readonly }]>
                        <input class="editinput" size="29" type="text" name="voucherNr" [{ $readonly }] onfocus="document.getElementById('randomVoucherNr').checked='true';">
                        [{ oxinputhelp ident="HELP_VOUCHERSERIE_MAIN_VOUCHERNUM" }]
                    </td>
                </tr>
                <tr>
                    <td class="edittext">
                        [{ oxmultilang ident="GENERAL_SUM" }]
                    </td>
                    <td>
                        <input type="text" size="29" class="editinput" name="voucherAmount" value="0" [{ $readonly }]>
                        [{ oxinputhelp ident="HELP_GENERAL_SUM" }]
                    </td>
                </tr>
            [{/block}]
            <tr>
                <td></td>
                <td><br>
                    <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="VOUCHERSERIE_MAIN_GENERATE" }]" [{ $readonly }] onClick="Javascript:document.myexport.cl.value='voucherserie_generate';">
                    <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="VOUCHERSERIE_MAIN_EXPORT" }]" [{ $readonly }] onClick="Javascript:document.myexport.cl.value='voucherserie_export';">
                </td>
            </tr>
        </table>

        </form>
        [{/if}]

    </td>
    </tr>
</table>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
