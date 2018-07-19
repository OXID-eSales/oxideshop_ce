<table cellspacing="0" cellpadding="0" border="0">
    [{block name="admin_category_main_form"}]
        <tr>
            <td class="edittext" width="120">
                [{oxmultilang ident="CATEGORY_MAIN_ACTIVE"}]
            </td>
            <td class="edittext" colspan="2">
                <input class="edittext" type="checkbox" name="editval[oxcategories__oxactive]" value='1' [{if $edit->oxcategories__oxactive->value == 1}]checked[{/if}] [{$readonly}]>&nbsp;&nbsp;&nbsp;
                [{oxmultilang ident="CATEGORY_MAIN_HIDDEN"}]&nbsp;&nbsp;&nbsp;
                <input class="edittext" type="checkbox" name="editval[oxcategories__oxhidden]" value='1' [{if $edit->oxcategories__oxhidden->value == 1}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_ACTIVE"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_TITLE"}]
            </td>
            <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxcategories__oxtitle->fldmax_length}]" name="editval[oxcategories__oxtitle]" value="[{$edit->oxcategories__oxtitle->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_TITLE"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_DESCRIPTION"}]
            </td>
            <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxcategories__oxdesc->fldmax_length}]" name="editval[oxcategories__oxdesc]" value="[{$edit->oxcategories__oxdesc->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_DESCRIPTION"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_PARENTID"}]
            </td>
            <td class="edittext" colspan="2">
                <select name="editval[oxcategories__oxparentid]" class="editinput" [{$readonly}]>
                    [{foreach from=$cattree->aList item=pcat}]
                        <option value="[{if $pcat->oxcategories__oxid->value}][{$pcat->oxcategories__oxid->value}][{else}]oxrootid[{/if}]" [{if $pcat->selected}]SELECTED[{/if}]>[{$pcat->oxcategories__oxtitle->getRawValue()|oxtruncate:33:"..":true}]</option>
                    [{/foreach}]
                </select>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_PARENTID"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_SORT"}]
            </td>
            <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxcategories__oxsort->fldmax_length}]" name="editval[oxcategories__oxsort]" value="[{$edit->oxcategories__oxsort->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_SORT"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_THUMB"}]
            </td>
            <td class="edittext">
                <input id="oxthumb" type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxthumb->fldmax_length}]" name="editval[oxcategories__oxthumb]" value="[{$edit->oxcategories__oxthumb->value}]">
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_THUMB"}]
            </td>
            <td class="edittext">
                [{if (!($edit->oxcategories__oxthumb->value=="nopic.jpg" || $edit->oxcategories__oxthumb->value=="" || $edit->oxcategories__oxthumb->value=="nopic_ico.jpg"))}]
                    <a href="Javascript:DeletePic('oxthumb');" class="delete left" [{include file="help.tpl" helpid=item_delete}]></a>
                [{/if}]
                <input class="editinput" name="myfile[TC@oxcategories__oxthumb]" type="file"  size="26" [{$readonly}]>
                ([{oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_ICON"}]
            </td>
            <td class="edittext">
                <input id="oxicon" type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxicon->fldmax_length}]" name="editval[oxcategories__oxicon]" value="[{$edit->oxcategories__oxicon->value}]">
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_ICON"}]
            </td>
            <td class="edittext">
                [{if (!($edit->oxcategories__oxicon->value=="nopic.jpg" || $edit->oxcategories__oxicon->value=="" || $edit->oxcategories__oxicon->value=="nopic_ico.jpg"))}]
                    <a href="Javascript:DeletePic('oxicon');" class="delete left" [{include file="help.tpl" helpid=item_delete}]></a>
                [{/if}]
                <input class="editinput" name="myfile[CICO@oxcategories__oxicon]" type="file" size="26" >
                ([{oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_PROMOTION_ICON"}]
            </td>
            <td class="edittext">
                <input id="oxpromoicon" type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxpromoicon->fldmax_length}]" name="editval[oxcategories__oxpromoicon]" value="[{$edit->oxcategories__oxpromoicon->value}]">
                [{ oxinputhelp ident="HELP_CATEGORY_MAIN_PROMOTION_ICON" }]
            </td>
            <td class="edittext">
                [{ if (!($edit->oxcategories__oxpromoicon->value=="nopic.jpg" || $edit->oxcategories__oxpromoicon->value=="" || $edit->oxcategories__oxpromoicon->value=="nopic_ico.jpg")) }]
                    <a href="Javascript:DeletePic('oxpromoicon');" class="delete left" [{include file="help.tpl" helpid=item_delete}]></a>
                [{/if}]
                <input class="editinput" name="myfile[PICO@oxcategories__oxpromoicon]" type="file" size="26" >
                ([{ oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{ oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_EXTLINK"}]
            </td>
            <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxextlink->fldmax_length}]" name="editval[oxcategories__oxextlink]" value="[{$edit->oxcategories__oxextlink->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_EXTLINK"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_TEMPLATE"}]
            </td>
            <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxtemplate->fldmax_length}]" name="editval[oxcategories__oxtemplate]" value="[{$edit->oxcategories__oxtemplate->value}]" [{include file="help.tpl" helpid=article_template}] [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_TEMPLATE"}]
            </td>
        </tr>

        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_DEFSORT"}]
            </td>
            <td class="edittext" colspan="2">
                <select name="editval[oxcategories__oxdefsort]" class="editinput" onChange="JavaScript:SchnellSortManager(this);">
                    <option value="">[{oxmultilang ident="CATEGORY_MAIN_NONE"}]</option>
                    [{foreach from=$sortableFields key=field item=desc}]
                        [{assign var="ident" value=GENERAL_ARTICLE_$desc}]
                        [{assign var="ident" value=$ident|oxupper}]
                        <option value="[{$desc}]" [{if $defsort == $desc}]SELECTED[{/if}]>[{oxmultilang|oxtruncate:20:"..":true ident=$ident}]</option>
                    [{/foreach}]
                </select>
                <input type="radio" class="editinput" name="editval[oxcategories__oxdefsortmode]" [{if !$defsort}]disabled[{/if}] value="0" [{if $edit->oxcategories__oxdefsortmode->value=="0"}]checked[{/if}]>[{oxmultilang ident="CATEGORY_MAIN_ASC"}]
                <input type="radio" class="editinput" name="editval[oxcategories__oxdefsortmode]" [{if !$defsort}]disabled[{/if}] value="1" [{if $edit->oxcategories__oxdefsortmode->value=="1"}]checked[{/if}]>[{oxmultilang ident="CATEGORY_MAIN_DESC"}]
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_DEFSORT"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_PRICEFROMTILL"}] ([{$oActCur->sign}])
            </td>
            <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcategories__oxpricefrom->fldmax_length}]" name="editval[oxcategories__oxpricefrom]" value="[{$edit->oxcategories__oxpricefrom->value}]" [{$readonly}]>&nbsp;
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcategories__oxpriceto->fldmax_length}]" name="editval[oxcategories__oxpriceto]" value="[{$edit->oxcategories__oxpriceto->value}]" onchange="JavaScript:LockAssignment(this);" onkeyup="JavaScript:LockAssignment(this);" onmouseout="JavaScript:LockAssignment(this);" [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_PRICEFROMTILL"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_VAT"}]
            </td>
            <td class="edittext" colspan="2">
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcategories__oxvat->fldmax_length}]" name="editval[oxcategories__oxvat]" value="[{$edit->oxcategories__oxvat->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_VAT"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                [{oxmultilang ident="CATEGORY_MAIN_SKIPDISCOUNTS"}]
            </td>
            <td class="edittext" colspan="2">
                <input type="hidden" name="editval[oxcategories__oxskipdiscounts]" value='0' [{$readonly_fields}]>
                <input class="edittext" type="checkbox" name="editval[oxcategories__oxskipdiscounts]" value='1' [{if $edit->oxcategories__oxskipdiscounts->value == 1}]checked[{/if}] [{$readonly_fields}]>
                [{oxinputhelp ident="HELP_CATEGORY_MAIN_SKIPDISCOUNTS"}]
            </td>
        </tr>
    [{/block}]
    <tr>
        <td class="edittext">
        </td>
        <td class="edittext" colspan="2"><br>
            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="CATEGORY_MAIN_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]><br>
        </td>
    </tr>
    <tr>
        <td class="edittext">
        </td>
        <td class="edittext" colspan="2"><br>
            [{include file="language_edit.tpl"}]
        </td>
    </tr>
</table>

</td>
<!-- Anfang rechte Seite -->
<td valign="top" class="edittext" align="left" width="50%">
    [{block name="admin_category_main_assign_articles"}]
        [{if $oxid != "-1"}]
            <input [{$readonly}] type="button" name="assignArticle" value="[{oxmultilang ident="GENERAL_ASSIGNARTICLES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=category_main&aoc=1&oxid=[{$oxid}]');" [{if $edit->oxcategories__oxpriceto->value > 0}] disabled [{/if}]>
        [{/if}]
    [{/block}]
