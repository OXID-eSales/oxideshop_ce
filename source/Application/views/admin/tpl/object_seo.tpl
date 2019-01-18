[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <input type="hidden" name="language" value="[{$actlang}]">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>


<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="language" value="[{$actlang}]">

        <table border="0" width="98%">

        [{block name="admin_object_seo_form"}]
            [{if $oView->showCatSelect()}]
            <tr>
                <td class="edittext" width="120">
                [{oxmultilang ident="GENERAL_SEO_ACTCAT"}]
                </td>
                <td class="edittext">
                    <select [{$readonly}] onChange="document.myedit.submit();" name="aSeoData[oxparams]">
                        [{assign var="sActId" value=$oView->getActCatId()}]
                        [{assign var="iActLang" value=$oView->getActCatLang()}]
                        [{foreach from=$oView->getSelectionList() item=aLangList key=sListType}]
                            [{foreach from=$aLangList item=aList key=iLang}]
                                [{assign var="blCat" value="1"}]

                                [{if $sListType == "oxcategory"}]
                                    [{assign var="sLabel" value="GENERAL_SEO_CAT"|oxmultilangassign}]
                                [{elseif $sListType == "oxvendor"}]
                                    [{assign var="sLabel" value="GENERAL_SEO_VND"|oxmultilangassign}]
                                [{elseif $sListType == "oxmanufacturer"}]
                                    [{assign var="sLabel" value="GENERAL_SEO_MANUFACTURER"|oxmultilangassign}]
                                [{block name="object_seo_extended"}]
                                [{/block}]
                                [{/if}]

                                <optgroup label="[{$sLabel}]">
                                    [{foreach from=$aList item=oListItem}]
                                        <option value="[{$sListType}]#[{$oListItem->getId()}]#[{$oListItem->getLanguage()}]" [{if $sActId == $oListItem->getId() && $iActLang == $oListItem->getLanguage()}]selected[{/if}]>[{$oListItem->getTitle()}]</option>
                                    [{/foreach}]
                                </optgroup>
                            [{/foreach}]
                        [{/foreach}]

                        [{if !$blCat}]
                            <option value="">--</option>
                        [{/if}]
                    </select>
                    [{oxinputhelp ident="HELP_GENERAL_SEO_ACTCAT"}]
                </td>
            </tr>
            [{/if}]


            <tr>
                <td class="edittext" width="120">
                [{oxmultilang ident="GENERAL_SEO_FIXED"}]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="aSeoData[oxfixed]" value='1' [{if $oView->isEntryFixed()}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_SEO_FIXED"}]
                </td>
            </tr>

            [{if $oView->isSuffixSupported()}]
                <tr>
                    <td class="edittext" width="120">
                    [{oxmultilang ident="GENERAL_SEO_SHOWSUFFIX"}]
                    </td>
                    <td class="edittext">
                    <input class="edittext" type="checkbox" name="blShowSuffix" value='1' [{if $oView->isEntrySuffixed()}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_GENERAL_SEO_SHOWSUFFIX"}]
                    </td>
                </tr>
            [{/if}]

            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_SEO_URL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" style="width: 100%;" name="aSeoData[oxseourl]" value="[{$oView->getEntryUri()}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_SEO_URL"}]
                </td>
            </tr>

            <tr>
                <td class="edittext" valign="top">
                  [{oxmultilang ident="GENERAL_SEO_OXKEYWORDS"}]
                </td>
                <td class="edittext">
                  <textarea type="text" class="editinput" style="width: 100%; height: 78px"  name="aSeoData[oxkeywords]" [{$readonly}]>[{$oView->getEntryMetaData("oxkeywords")}]</textarea>
                  [{oxinputhelp ident="HELP_GENERAL_SEO_OXKEYWORDS"}]
                </td>
            </tr>

            <tr>
                <td class="edittext" valign="top">
                  [{oxmultilang ident="GENERAL_SEO_OXDESCRIPTION"}]
                </td>
                <td class="edittext">
                  <textarea type="text" class="editinput" style="width: 100%; height: 78px"  name="aSeoData[oxdescription]" [{$readonly}]>[{$oView->getEntryMetaData("oxdescription")}]</textarea>
                  [{oxinputhelp ident="HELP_GENERAL_SEO_OXDESCRIPTION"}]
                </td>
            </tr>
        [{/block}]

        [{if $oxid != "-1"}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{block name="object_seo_custreadonly"}]
                [{/block}]
                [{include file="language_edit.tpl" custreadonly=$custreadonly}]
            </td>
        </tr>
        [{/if}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext">
            <input type="submit" class="edittext" onclick="document.getElementById('myedit').fnc.value='save';" name="saveArticle" value="[{oxmultilang ident="GENERAL_SAVE"}]" [{$readonly}]><br>
            </td>
        </tr>


        </table>


</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
