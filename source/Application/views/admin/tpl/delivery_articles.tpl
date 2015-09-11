[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="delivery_articles">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>


<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="delivery_articles">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxdelivery__oxid]" value="[{$oxid}]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left">
        <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNCATEGORIES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=delivery_articles&aoc=2&oxid=[{$oxid}]');">
    </td>
    <!-- Ende rechte Seite -->
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
        <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNARTICLES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=delivery_articles&aoc=1&oxid=[{$oxid}]');">
    </td>
    <!-- Ende rechte Seite -->
    </tr>
</table>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
