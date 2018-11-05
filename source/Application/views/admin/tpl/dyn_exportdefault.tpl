[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign skip_onload="true"}]

<script type="text/javascript">
    if(top)
    {
        top.sMenuItem    = "[{oxmultilang ident="GENEXPORT_MENUITEM"}]";
        top.sMenuSubItem = "[{oxmultilang ident="GENEXPORT_MENUSUBITEM"}]";
        top.sWorkArea    = "[{$_act}]";
        top.setTitle();
    }
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="98%">

<tr>
    <td valign="top" class="edittext">
        <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" target="dynexport_do" method="post">
        <table cellspacing="0" cellpadding="0" border="0">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="[{$sClassDo}]">
        <input type="hidden" name="fnc" value="start">
        <tr>
            <td class="edittext" width="180" height="40" valign="top">
            [{oxmultilang ident="GENERAL_CATEGORYSELECT"}]
            </td>
            <td class="edittext">
            <select name="acat[]" size="20" multiple class="editinput" style="width: 210px;" [{$readonly}]>
            [{foreach from=$cattree item=oCat}]
            <option value="[{$oCat->getId()}]">[{$oCat->oxcategories__oxtitle->value}]</option>
            [{/foreach}]
            </td>
            </select>
            [{oxinputhelp ident="HELP_GENERAL_CATEGORYSELECT"}]
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="GENERAL_SEARCHKEY"}]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="39" maxlength="128" name="search" value="" [{$readonly}]>
            [{oxinputhelp ident="HELP_GENERAL_SEARCHKEY"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            </td>
            <td class="edittext">
            <input type="submit" class="edittext" style="width: 210px;" name="save" value="[{oxmultilang ident="GENERAL_ESTART"}]" [{$readonly}]>
            </td>
        </tr>
        </table>

    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left">

        <table cellspacing="0" cellpadding="0" border="0">
        <!--<tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="GENERAL_EXPORTDELCOST"}]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportDelCost" value="0,00" [{$readonly}]> &euro;
            [{oxinputhelp ident="HELP_GENERAL_EXPORTDELCOST"}]
            </td>
        </tr>-->
        <tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="GENERAL_EXPORTMINSTOCK"}]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinStock" value="1" [{$readonly}]>
            [{oxinputhelp ident="HELP_GENERAL_EXPORTMINSTOCK"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="GENERAL_EXPORTMINPRICE"}]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinPrice" value="0" [{$readonly}]>
            [{oxinputhelp ident="HELP_GENERAL_EXPORTMINPRICE"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="GENERAL_EXPOSTVARS"}]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportVars" value="true" checked [{$readonly}]>
            [{oxinputhelp ident="HELP_GENERAL_EXPOSTVARS"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="GENERAL_EXPORTMAINVARS"}]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportMainVars" value="true" checked [{$readonly}]>
            [{oxinputhelp ident="HELP_GENERAL_EXPORTMAINVARS"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="GENERAL_EXPORTCAMPAIGN"}]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="20" maxlength="10" name="sExportCampaign" value="" [{$readonly}]>
            [{oxinputhelp ident="HELP_GENERAL_EXPORTCAMPAIGN"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{oxmultilang ident="DYNBASE_ADDCATTOCAMPAIGN"}]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blAppendCatToCampaign" value="true" [{$readonly}]>
            [{oxinputhelp ident="HELP_DYNBASE_ADDCATTOCAMPAIGN"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
                [{oxmultilang ident="GENERAL_EXPORTLANGUAGE"}]
            </td>
            <td class="edittext">
            <select name="iExportLanguage" class="saveinnewlanginput" [{$readonly}]>
                [{foreach from=$aLangs key=lang item=olang}]
                <option value="[{$lang}]"[{if $olang->selected}]SELECTED[{/if}]>[{$olang->name}]</option>
                [{/foreach}]
            </select>
            [{oxinputhelp ident="HELP_GENERAL_EXPORTLANGUAGE"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
                [{oxmultilang ident="GENERAL_EXPORTCUSTOMHEADER"}]
            </td>
            <td class="edittext">
                <input type="text" class="editinput" size="50" name="sExportCustomHeader" value="" [{$readonly}]>
            [{oxinputhelp ident="HELP_GENERAL_EXPORTCUSTOMHEADER"}]
            </td>
        </tr>
        </table>
        </form>
        <!--
        Bitte Land f&uuml;r Versandkosten w&auml;hlen : <br>
        <select name="country" class="editinput" style="width: 210px;" [{$readonly}]>
        [{foreach from=$countrylist item=oCountry}]
        <option value="[{$oCountry->oxcountry__oxid->value}]">[{$oCountry->oxcountry__oxtitle->value}]</option>
        [{/foreach}]
        -->
    </td>

</tr>
</table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]