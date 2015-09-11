[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="article_userdef">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="99%" height="100%">
<tr>
<td valign="top" background="[{$oViewConf->getImageUrl()}]/edit_back.gif" width="100%">

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="article_userdef">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[article__oxid]" value="[{$oxid}]">

<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
<tr height="10">
    <td></td>
    <td></td>
</tr>
<tr>
    <td width="15"></td>
    <td valign="top" class="edittext">

    [{oxmultilang ident="ARTICLE_USERDEF_USERDEFRANGE"}]

    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
    </td>
    <!-- Ende rechte Seite -->

    </tr>
</table>
</form>
</td>
</tr>
[{include file="bottomnaviitem.tpl"}]
</table>
[{include file="bottomitem.tpl"}]
