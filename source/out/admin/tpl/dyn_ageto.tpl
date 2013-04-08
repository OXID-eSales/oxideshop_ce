[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

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

<table cellspacing="0" cellpadding="0" border="0" width="99%" height="100%">
<tr>
<td width="100%" align="center" valign="top" bgcolor="#E7EAED" style="border : 1px #000000; border-style : none none solid none;">


<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
<tr height="10">
    <td></td>
    <td></td>
    <td></td>
</tr>
<tr>
    <td width="15"></td>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" target="dynexport_do">
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


</td>
</tr>

[{include file="bottomnaviitem.tpl" }]
</table>
[{include file="bottomitem.tpl"}]
