[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="user_article">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="article_main">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="fnc" value="">
<tr>
    <td class="listheader first">[{ oxmultilang ident="USER_ARTICLE_QUANTITY" }]</td>
    <td class="listheader" height="15">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_ITEMNR" }]</td>
    <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TITLE" }]</td>
    <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TYPE" }]</td>
    <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_SHORTDESC" }]</td>
</tr>
[{assign var="blWhite" value=""}]
[{foreach from=$oArticlelist item=listitem}]
<tr>
    [{assign var="listclass" value=listitem$blWhite }]
    <td valign="top" class="[{ $listclass}]">[{ $listitem->oxorderarticles__oxamount->value }]</td>
    <td valign="top" class="[{ $listclass}]" height="15">[{ $listitem->oxorderarticles__oxartnum->value }]</td>
    <td valign="top" class="[{ $listclass}]">[{ $listitem->oxorderarticles__oxtitle->value|oxtruncate:30:""|strip_tags }]</td>
    <td valign="top" class="[{ $listclass}]">[{ $listitem->oxorderarticles__oxselvariant->value }]</td>
    <td valign="top" class="[{ $listclass}]">[{ $listitem->oxorderarticles__oxshortdesc->value|oxtruncate:30:""|strip_tags }]</td>
</tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
</form>
</table>


[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
