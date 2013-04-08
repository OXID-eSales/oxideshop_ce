[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<input type="hidden" name="clr" value="1">

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="category_order">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="category_order">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[category__oxid]" value="[{$oxid}]">
    [{oxhasrights object=$edit readonly=$readonly right=$smarty.const.RIGHT_VIEW}]
        [{if $oxid != "-1" && !$edit->isDerived()}]
            <input type="button" value="[{oxmultilang ident="CATEGORY_ORDER_SORTCATEGORIES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=category_order&aoc=1&oxid=[{$oxid}]');">
        [{/if}]
    [{/oxhasrights}]
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]