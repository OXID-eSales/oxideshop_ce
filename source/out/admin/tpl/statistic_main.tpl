[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="statistic_main">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" target="edit">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="statistic_main">
<input type="hidden" name="fnc" value="save">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxstatistics__oxid]" value="[{$oxid}]">

<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td class="edittext" valign="top">
        [{block name="admin_statistic_main_form"}]
            [{ oxmultilang ident="STASTISTIC_MAIN_SAVEUNDER" }]<br>
            <input type="text" class="editinput" size="32" maxlength="[{$edit->oxstatistics__oxtitle->fldmax_length}]" name="editval[oxstatistics__oxtitle]" value="[{$edit->oxstatistics__oxtitle->value}]" [{ $readonly }]>
            [{ oxinputhelp ident="HELP_STASTISTIC_MAIN_SAVEUNDER" }]
            <br>
        [{/block}]
        <br><input type="submit" class="edittext" id="_savereportsbtn" name="save" value="[{ oxmultilang ident="STASTISTIC_MAIN_SAVE" }]" onClick="Javascript:document.myedit.target='edit';document.myedit.fnc.value='save'"><br>
    </td>
    <td valign="top" class="edittext">
        [{ if $oxid && $oxid != '-1' }]
        <input [{ $readonly }] type="button" value="[{ oxmultilang ident="STASTISTIC_MAIN_ASSIGNREPORT" }]" class="edittext" onclick="JavaScript:showDialog('&cl=statistic_main&aoc=1&oxid=[{ $oxid }]');">
        [{/if}]
    </td>
    <td valign="top" class="edittext">
        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td class="edittext">
            [{block name="admin_statistic_main_genreport"}]
                [{ oxmultilang ident="GENERAL_INTIME" }]
                <select name="timeframe" class="editinput" [{ $readonly }]>
                <option value="7">1 [{ oxmultilang ident="STASTISTIC_MAIN_WEEK" }]</option>
                <option value="14">2 [{ oxmultilang ident="STASTISTIC_MAIN_WEEKS" }]</option>
                <option value="31">1 [{ oxmultilang ident="GENERAL_MONTH" }]</option>
                <option value="62">2 [{ oxmultilang ident="GENERAL_MONTHS" }]</option>
                <option value="186">6 [{ oxmultilang ident="GENERAL_MONTHS" }]</option>
                </select>
                [{ oxinputhelp ident="HELP_GENERAL_INTIME" }]
                <br>
                <br>
                [{ oxmultilang ident="STASTISTIC_MAIN_ORDERFROM" }] <input type="text" name="time_from" value="" size="12" class="editinput"> [{ oxmultilang ident="STASTISTIC_MAIN_TILL" }] <input type="text" name="time_to" value="" size="12" class="editinput"><br>
                <br>
            [{/block}]
            <input type="submit" class="edittext" id="_genreportsbtn" name="save" value="[{ oxmultilang ident="STASTISTIC_MAIN_GENERATE" }]" onClick="Javascript:document.myedit.target='_new';document.myedit.fnc.value='generate'" [{if !$ireports}]readonly disabled[{/if}]>
            [{ oxinputhelp ident="HELP_STASTISTIC_MAIN_ORDERFROM" }]
            <br>
          </td>
        </tr>
        </table>
    </td>
    </tr>

</table>

</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
