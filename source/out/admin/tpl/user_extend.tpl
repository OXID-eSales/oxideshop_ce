[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="user_extend">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="user_extend">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxuser__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
<tr>
    <td width="15"></td>
    <td valign="top" class="edittext">
        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_user_extend_form"}]
            <tr>
                <td class="edittext" width="120">
                [{ oxmultilang ident="USER_EXTEND_PRIVATFON" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxuser__oxprivfon->fldmax_length}]" name="editval[oxuser__oxprivfon]" value="[{$edit->oxuser__oxprivfon->value}]" [{ $readonly}]>
                [{ oxinputhelp ident="HELP_USER_EXTEND_PRIVATFON" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_EXTEND_MOBILFON" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxuser__oxmobfon->fldmax_length}]" name="editval[oxuser__oxmobfon]" value="[{$edit->oxuser__oxmobfon->value}]" [{ $readonly}]>
                [{ oxinputhelp ident="HELP_USER_EXTEND_MOBILFON" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_EXTEND_NEWSLETTER" }]
                </td>
                <td class="edittext">
                    <input type="hidden" name="editnews" value='0'>
                    <input class="edittext" type="checkbox" name="editnews" value='1' [{if $edit->sDBOptin == 1}]checked[{/if}] [{ $readonly}]>
                    [{ oxinputhelp ident="HELP_USER_EXTEND_NEWSLETTER" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_EXTEND_EMAILFAILED" }]
                </td>
                <td class="edittext">
                    <input type="hidden" name="emailfailed" value='0'>
                    <input class="edittext" type="checkbox" name="emailfailed" value='1' [{if $edit->sEmailFailed == 1}]checked[{/if}] [{ $readonly}]>
                    [{ oxinputhelp ident="HELP_USER_EXTEND_EMAILFAILED" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_EXTEND_BONI" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxuser__oxboni->fldmax_length}]" name="editval[oxuser__oxboni]" value="[{$edit->oxuser__oxboni->value}]" [{ $readonly}]>
                [{ oxinputhelp ident="HELP_USER_EXTEND_BONI" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_URL" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxuser__oxurl->fldmax_length}]" name="editval[oxuser__oxurl]" value="[{$edit->oxuser__oxurl->value}]" [{ $readonly}]>
                [{ oxinputhelp ident="HELP_GENERAL_URL" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="USER_EXTEND_CREDITPOINTS" }]
                </td>
                <td class="edittext">
                [{$edit->oxuser__oxpoints->value}]
                </td>
            </tr>
            <tr>
                <td class="edittext wrap">
                [{ oxmultilang ident="USER_EXTEND_DISABLEAUTOGROUP" }]
                </td>
                <td class="edittext">
                 <input type="hidden" name="editval[oxuser__oxdisableautogrp]" value='0'>
                <input class="edittext" type="checkbox" name="editval[oxuser__oxdisableautogrp]" value='1' [{if $edit->oxuser__oxdisableautogrp->value == 1}]checked[{/if}] [{ $readonly}]>
                [{ oxinputhelp ident="HELP_USER_EXTEND_DISABLEAUTOGROUP" }]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly}]>
            </td>
        </tr>
        </table>
    </td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td valign="top" class="edittext">
        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="copypastetext" id="test_userAddress">
            [{$edit->oxuser__oxsal->value|oxmultilangsal}]<br>
            [{$edit->oxuser__oxfname->value }] [{$edit->oxuser__oxlname->value }]<br>
            [{$edit->oxuser__oxcompany->value }]<br>
            [{$edit->oxuser__oxstreet->value }] [{$edit->oxuser__oxstreetnr->value }]<br>
            [{$edit->getState()}]
            [{$edit->oxuser__oxzip->value }] [{$edit->oxuser__oxcity->value }]<br>
            [{$edit->oxuser__oxaddinfo->value }]<br>
            [{$edit->oxuser__oxcountry->value }]<br>
            [{$edit->oxuser__oxfon->value }]
            </td>
        </tr>
        </table>
   </td>
    <!-- Anfang rechte Seite -->
   <td valign="top" class="edittext" align="left" width="50%">
    </td>

    </tr>
</table>
</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
