[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="user_remark">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxuser__oxid]" value="[{ $oxid }]">
<input type="hidden" name="rem_oxid" value="[{ $rem_oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <select name="rem_oxid" size="17" class="editinput" style="width:180px;" onChange="Javascript:document.myedit.submit();" [{ $readonly }]>
        [{foreach from=$allremark item=allitem}]
        <option value="[{ $allitem->oxremark__oxid->value }]" [{ if $allitem->selected}]SELECTED[{/if}]>[{ $allitem->oxremark__oxheader|oxformdate:"datetime" }]
        [{ if $allitem->oxremark__oxtype->value == "r" }][{ oxmultilang ident="ORDER_REMARK_REMARK" }][{elseif $allitem->oxremark__oxtype->value == "o" }][{ oxmultilang ident="ORDER_REMARK_ORDER" }][{elseif $allitem->oxremark__oxtype->value == "c" }][{ oxmultilang ident="ORDER_REMARK_USER" }][{else}][{ oxmultilang ident="ORDER_REMARK_NEWS" }][{/if}]
        </option>
        [{/foreach}]
        </select><br><br>
        <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]>
        <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_DELETE" }]" onClick="Javascript:document.myedit.fnc.value='delete'"" [{ $readonly }]><br>

    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left">
    <input type="text" class="editinput" size="100" maxlength="128" value="[{if $remarkheader}][{$remarkheader|oxformdate:"datetime":true}][{/if}]" readonly disabled><br>
    <input type="hidden" name="remarkheader" value="[{$remarkheader}]">
    <textarea class="editinput" cols="100" rows="17" wrap="VIRTUAL" name="remarktext" [{ $readonly }]>[{$remarktext}]</textarea><br>
    </td>
    <!-- Ende rechte Seite -->

</tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
