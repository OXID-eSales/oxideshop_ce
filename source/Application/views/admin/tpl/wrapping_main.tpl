[{include file="headitem.tpl" title="WRAPPING_MAIN_TITLE"|oxmultilangassign}]

<span class="popUpStyle" id="wrapping_new" style="position: absolute;visibility: hidden;">Neue Geschenkverpackung/Grusskarte</span>

<script type="text/javascript">
<!--
function DeletePic( sField )
{
    var oForm = document.getElementById("myedit");
    document.getElementById(sField).value="";
    oForm.fnc.value='save';
    oForm.submit();
}
//-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="wrapping_main">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>


<form name="myedit" id="myedit" enctype="multipart/form-data" action="[{$oViewConf->getSelfLink()}]" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="wrapping_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxwrapping__oxid]" value="[{$oxid}]">

[{if $oViewConf->isAltImageServerConfigured()}]
    <div class="warning">[{oxmultilang ident="ALTERNATIVE_IMAGE_SERVER_NOTE"}] [{oxinputhelp ident="HELP_ALTERNATIVE_IMAGE_SERVER_NOTE"}]</div>
[{/if}]

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_wrapping_main_form"}]
            <tr>
                <td class="edittext" width="90">
                [{oxmultilang ident="GENERAL_ACTIVE"}]
                </td>
                <td class="edittext" colspan="2">
                <input class="edittext" type="checkbox" name="editval[oxwrapping__oxactive]" value='1' [{if $edit->oxwrapping__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_TYPE"}]
                </td>
                 <td class="edittext" colspan="2">
                <select name="editval[oxwrapping__oxtype]" class="editinput" [{$readonly}]>
                    <option value="WRAP" [{if $edit->oxwrapping__oxtype->value == "WRAP"}]SELECTED[{/if}]>[{oxmultilang ident="WRAPPING_MAIN_PRESENTPACKUNG"}]</option>
                    <option value="CARD" [{if $edit->oxwrapping__oxtype->value == "CARD"}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_CARD"}]</option>
                </select>
                [{oxinputhelp ident="HELP_GENERAL_TYPE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_NAME"}]
                </td>
                <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxwrapping__oxname->fldmax_length}]" name="editval[oxwrapping__oxname]" value="[{$edit->oxwrapping__oxname->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_NAME"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_PRICE"}] ([{$oActCur->sign}])
                </td>
                <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="10" maxlength="[{$edit->oxwrapping__oxprice->fldmax_length}]" name="editval[oxwrapping__oxprice]" value="[{$edit->oxwrapping__oxprice->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_PRICE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="WRAPPING_MAIN_PICTURE"}]
                </td>
                <td class="edittext">
                <input id="oxpic" type="text" class="editinput" size="42" maxlength="[{$edit->oxwrapping__oxpic->fldmax_length}]" name="editval[oxwrapping__oxpic]" value="[{$edit->oxwrapping__oxpic->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_WRAPPING_MAIN_PICTURE"}]
                [{if (!($edit->oxwrapping__oxpic->value=="nopic.jpg" || $edit->oxwrapping__oxpic->value=="" || $edit->oxwrapping__oxpic->value=="nopic_ico.jpg"))}]
                </td>
                <td class="edittext">
                <a href="Javascript:DeletePic('oxpic');" class="delete left" [{include file="help.tpl" helpid=item_delete}]></a>
                [{/if}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="WRAPPING_MAIN_PICUPLOAD"}] ([{oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
                </td>
                <td class="edittext" colspan="2">
                <input class="editinput" name="myfile[WP@oxwrapping__oxpic]" size="26" type="file" [{$readonly}]>
                [{oxinputhelp ident="HELP_WRAPPING_MAIN_PICUPLOAD"}]
                </td>
            </tr>
        [{/block}]
        [{if $oxid != "-1"}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext" colspan="2"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>
        [{/if}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext" colspan="2"><br>
            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
            </td>
        </tr>
        </table>
    </td>
    <td width="35">
    <img src="[{$oViewConf->getImageUrl()}]/grayline_vert.gif" width="2" height="270" alt="" border="0">
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
        [{block name="admin_wrapping_main_picture"}]
            [{if $oxid != "-1"}]
                [{if $edit->oxwrapping__oxpic->value}]
                    [{oxmultilang ident="WRAPPING_MAIN_PICTURE"}] <br>
                    <img src="[{$edit->getPictureUrl()}]" border="0" hspace="0" vspace="0">
                [{/if}]
            [{/if}]
        [{/block}]
    </td>
    </tr>
</table>

</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]