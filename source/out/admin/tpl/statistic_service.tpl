[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="statistic_service">
</form>


<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="statistic_service">
<input type="hidden" name="fnc" value="save">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxstatistics__oxid]" value="[{$oxid}]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_statistic_service_form"}]
            <tr>
                <td class="edittext" height="20">
                [{ oxmultilang ident="STASTISTIC_SERVICE_LOGCOUNT" }]
                </td>
                <td class="edittext">
                [{ $iLogCount }]
                </td>
            </tr>
            <tr>
                <td class="edittext" width="120" height="20">
                [{ oxmultilang ident="GENERAL_INTIME" }]<br><br>
                </td>
                <td class="edittext">
                <select name="timeframe" class="editinput" [{ $readonly }]>
                <option value="186">[{ oxmultilang ident="STASTISTIC_SERVICE_OLDER" }] 6 [{ oxmultilang ident="GENERAL_MONTHS" }]</option>
                <option value="62">[{ oxmultilang ident="STASTISTIC_SERVICE_OLDER" }] 2 [{ oxmultilang ident="GENERAL_MONTHS" }]</option>
                <option value="31">[{ oxmultilang ident="STASTISTIC_SERVICE_OLDER" }] 1 [{ oxmultilang ident="GENERAL_MONTH" }]</option>
                <option value="1">[{ oxmultilang ident="STASTISTIC_SERVICE_OLDER" }] 1 [{ oxmultilang ident="STASTISTIC_SERVICE_DAY" }]</option>
                </select>
                [{ oxinputhelp ident="HELP_GENERAL_INTIME" }]
                <br><br>
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext"></td>
            <td class="edittext">
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="STASTISTIC_SERVICE_DELETE" }]" onClick="Javascript:document.myedit.fnc.value='cleanup'""><br>
            </td>
        </tr>
        </table>


    </td>
    </tr>

</table>

</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
