[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="usergroup_main">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="usergroup_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxgroups__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>

    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_usergroup_main_form"}]
            <tr>
                <td class="edittext" width="70">
                [{ oxmultilang ident="GENERAL_ACTIVE" }]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[oxgroups__oxactive]" value='1' [{if $edit->oxgroups__oxactive->value == 1}]checked[{/if}] [{ $readonly }] [{ $disableSharedEdit }]>
                [{ oxinputhelp ident="HELP_GENERAL_ACTIVE" }]
                </td>
            </tr>
            <tr>
                <td class="edittext" width="100">
                [{ oxmultilang ident="GENERAL_NAME" }]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="25" maxlength="[{$edit->oxgroups__oxtitle->fldmax_length}]" name="editval[oxgroups__oxtitle]" value="[{$edit->oxgroups__oxtitle->value}]" [{ $readonly }] [{ $disableSharedEdit }]>
                [{ oxinputhelp ident="HELP_GENERAL_NAME" }]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }] [{ $disableSharedEdit }]>
            </td>
        </tr>
        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
    [{ if $oxid != "-1"}]

        <input [{ $readonly }] type="button" value="[{ oxmultilang ident="GENERAL_ASSIGNUSERS" }]" class="edittext" onclick="JavaScript:showDialog('&cl=usergroup_main&aoc=1&oxid=[{ $oxid }]');">

    [{ /if}]
    </td>

    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
