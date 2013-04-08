[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE_1"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>

<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" target="dynexport_do" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="[{$sClassDo}]">
        <input type="hidden" name="fnc" value="start">
        <tr>
            <td class="edittext" width="180" height="40" valign="top">
            [{ oxmultilang ident="GENERAL_CATEGORYSELECT" }]
            </td>
            <td class="edittext">
            <select name="acat[]" size="20" multiple class="editinput" style="width: 210px;">
            [{foreach from=$cattree item=oCat}]
            <option value="[{ $oCat->getId() }]">[{ $oCat->oxcategories__oxtitle->value }]</option>
            [{/foreach}]
            </td>
            </select>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_SEARCHKEY" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="39" maxlength="128" name="search" value="">
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            </td>
            <td class="edittext">
            <input type="submit" class="edittext" style="width: 210px;" name="save" value="[{ oxmultilang ident="GENERAL_ESTART" }]">
            </td>
        </tr>
        </table>

    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left">

        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTDELCOST" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportDelCost" value="0,00"> &euro;
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMINSTOCK" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinStock" value="1">
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMINPRICE" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinPrice" value="0">
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPOSTVARS" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportVars" value="true" checked>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMAINVARS" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportMainVars" value="true" checked>
            </td>
        </tr>
        </table>

        <!--
        Bitte Land f&uuml;r Versandkosten w&auml;hlen : <br>
        <select name="country" class="editinput" style="width: 210px;">
        [{foreach from=$countrylist item=oCountry}]
        <option value="[{ $oCountry->oxcountry__oxid->value }]">[{ $oCountry->oxcountry__oxtitle->value }]</option>
        [{/foreach}]
        -->
    </td>
    </form>
</tr>
</table>

[{include file="bottomnaviitem.tpl" }]
[{include file="bottomitem.tpl"}]
