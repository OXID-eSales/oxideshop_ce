[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="newsletter_plain">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="newsletter_plain">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxnewsletter__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">
        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_newsleter_plain_form"}]
            <tr>
                <td class="edittext" width="100">
                [{ oxmultilang ident="NEWSLETTER_PLAIN_TEXT" }]
                </td>
                <td class="edittext">
                <textarea class="editinput" cols="150" rows="15" wrap="VIRTUAL" name="editval[oxnewsletter__oxplaintemplate]">[{$edit->oxnewsletter__oxplaintemplate->value}]</textarea><br>
                [{ oxinputhelp ident="HELP_NEWSLETTER_PLAIN_TEXT" }]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"">
            </td>
        </tr>
        </table>
    </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
