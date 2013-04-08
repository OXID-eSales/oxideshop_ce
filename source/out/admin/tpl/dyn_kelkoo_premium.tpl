[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE_1"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>


<table cellspacing="0" cellpadding="0" border="0"  width="98%">
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
            <select name="acat[]" size="20" multiple class="editinput" style="width: 210px;" [{ $readonly }]>
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
            <input type="text" class="editinput" size="39" maxlength="128" name="search" value="" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            </td>
            <td class="edittext">
            <input type="submit" class="edittext" style="width: 210px;" name="save" value="[{ oxmultilang ident="GENERAL_ESTART" }]" [{ $readonly }]>
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
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportDelCost" value="0,00" [{ $readonly }]> &euro;
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMINSTOCK" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinStock" value="1" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMINPRICE" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinPrice" value="0" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPOSTVARS" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportVars" value="true" checked [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMAINVARS" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportMainVars" value="true" checked [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTCAMPAIGN" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="20" maxlength="10" name="sExportCampaign" value="" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="DYNBASE_ADDCATTOCAMPAIGN" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blAppendCatToCampaign" value="true">
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40" style="padding-right:10px;">
            [{ oxmultilang ident="GENERAL_EXPORTDELTIMEINSTOCK" }]
            </td>
            <td class="edittext" width="180" height="40">
            <select class="editinput" name="sExportDelivTimeInStock" style="max-width:180px;" [{ $readonly }]>
                <option value="bis 5 Tage" selected>[{ oxmultilang ident="GENERAL_EXPORTDELTIMELESS5" }]</option>
                <option value="bis 10 Tage">[{ oxmultilang ident="GENERAL_EXPORTDELTIMELESS10" }]</option>
                <option value="über 10 Tage">[{ oxmultilang ident="GENERAL_EXPORTDELTIMEMORE10" }]</option>
            </select>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40" style="padding-right:10px;">
            [{ oxmultilang ident="GENERAL_EXPORTDELTIMENOSTOCK" }]
            </td>
            <td class="edittext" width="180" height="40">
            <select class="editinput" name="sExportDelivTimeNoStock" style="max-width:180px;" [{ $readonly }]>
                <option value="bis 5 Tage" selected>[{ oxmultilang ident="GENERAL_EXPORTDELTIMELESS5" }]</option>
                <option value="bis 10 Tage">[{ oxmultilang ident="GENERAL_EXPORTDELTIMELESS10" }]</option>
                <option value="über 10 Tage">[{ oxmultilang ident="GENERAL_EXPORTDELTIMEMORE10" }]</option>
            </select>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40" style="padding-right:10px;">
            [{ oxmultilang ident="GENERAL_EXPORTSTATUS" }]
            </td>
            <td class="edittext" width="180" height="40">
            <select class="editinput" name="sExportStatus" style="max-width:180px;" [{ $readonly }]>
                <option value="neu" selected>[{ oxmultilang ident="GENERAL_EXPORTSTATUSNEW" }]</option>
                <option value="gebraucht">[{ oxmultilang ident="GENERAL_EXPORTSTATUSUSED" }]</option>
            </select>
            </td>
        </tr>
        </table>

        <!--
        Bitte Land f&uuml;r Versandkosten w&auml;hlen : <br>
        <select name="country" class="editinput" style="width: 210px;" [{ $readonly }]>
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