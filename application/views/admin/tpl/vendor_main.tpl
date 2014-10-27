[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
    var oField = top.oxid.admin.getLockTarget();
    oField.onchange = oField.onkeyup = oField.onmouseout = top.oxid.admin.unlockSave;
}

//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="oxidCopy" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="vendor_main">
    <input type="hidden" name="language" value="[{ $actlang }]">
</form>

<form name="myedit" id="myedit" enctype="multipart/form-data" action="[{ $oViewConf->getSelfLink() }]" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="vendor_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="voxid" value="[{ $oxid }]">
<input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
<input type="hidden" name="editval[oxvendor__oxid]" value="[{ $oxid }]">
<input type="hidden" name="language" value="[{ $actlang }]">

[{if $oViewConf->isAltImageServerConfigured() }]
    <div class="warning">[{ oxmultilang ident="ALTERNATIVE_IMAGE_SERVER_NOTE" }] [{ oxinputhelp ident="HELP_ALTERNATIVE_IMAGE_SERVER_NOTE" }]</div>
[{/if}]

<table border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_vendor_main_form"}]
            <tr>
                <td class="edittext" width="120">
                [{ oxmultilang ident="GENERAL_ACTIVE" }]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[oxvendor__oxactive]" value='1' [{if $edit->oxvendor__oxactive->value == 1}]checked[{/if}] [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_ACTIVE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_TITLE" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="40" maxlength="[{$edit->oxvendor__oxtitle->fldmax_length}]" id="oLockTarget" name="editval[oxvendor__oxtitle]" value="[{$edit->oxvendor__oxtitle->value}]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_TITLE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_SHORTDESC" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="40" maxlength="[{$edit->oxvendor__oxshortdesc->fldmax_length}]" name="editval[oxvendor__oxshortdesc]" value="[{$edit->oxvendor__oxshortdesc->value}]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_SHORTDESC" }]
                </td>
            </tr>

            <tr>
                <td class="edittext">
                [{ oxmultilang ident="GENERAL_ICON" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxvendor__oxicon->fldmax_length}]" name="editval[oxvendor__oxicon]" value="[{$edit->oxvendor__oxicon->value}]" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_GENERAL_ICON" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="VENDOR_MAIN_ICONUPLOAD" }] ([{ oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{ oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}]):<br>
                </td>
                <td class="edittext">
                <input class="editinput" name="myfile[VICO@oxvendor__oxicon]" type="file" [{ $readonly }]>
                [{ oxinputhelp ident="HELP_VENDOR_MAIN_ICONUPLOAD" }]
                </td>
            </tr>
        [{/block}]
        [{if $oxid != "-1"}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>
        [{/if}]
        <tr>
            <td class="edittext"><br><br>
            </td>
            <td class="edittext"><br><br>
            <input type="submit" class="edittext" id="oLockButton" name="saveArticle" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }] [{ if !$edit->oxvendor__oxtitle->value && !$oxparentid }]disabled[{/if}] [{ $readonly }]><br>
            </td>
        </tr>


        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="55%">
    [{ if $oxid != "-1"}]
    <input [{ $readonly }] type="button" value="[{ oxmultilang ident="GENERAL_ASSIGNARTICLES" }]" class="edittext" onclick="JavaScript:showDialog('&cl=vendor_main&aoc=1&oxid=[{ $oxid }]');" [{ $readonly }]>
    [{ /if}]
    </td>
    <!-- Ende rechte Seite -->

    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]