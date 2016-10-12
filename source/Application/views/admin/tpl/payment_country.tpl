[{if $shopid != "1" || $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="payment_country">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>


<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="payment_country">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxpayments__oxid]" value="[{$oxid}]">

[{if $oxid != "-1"}]
    <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNCOUNTRIES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=payment_country&aoc=1&oxid=[{$oxid}]');">
[{/if}]

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]