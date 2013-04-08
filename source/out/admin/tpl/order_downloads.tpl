[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="order_downloads">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="order_downloads">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="fnc" value="resetDownloadLink">
    <input type="hidden" name="oxorderfileid" value="[{ $oxid }]">
<tr>
    [{block name="admin_order_downloads_header"}]
        <td class="listheader" height="15">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_ITEMNR" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TITLE" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="ORDER_DOWNLOADS_FILE" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="ORDER_DOWNLOADS_FIRSTDOWNLOAD" }]</td>
        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="ORDER_DOWNLOADS_LASTDOWNLOAD" }]</td>
        <td class="listheader">[{ oxmultilang ident="ORDER_DOWNLOADS_COUNTOFDOWNLOADS" }]</td>
        <td class="listheader">[{ oxmultilang ident="ORDER_DOWNLOADS_MAXCOUNT" }]</td>
        <td class="listheader">[{ oxmultilang ident="ORDER_DOWNLOADS_EXPIRATIONTIME" }]</td>
        <td class="listheader" colspan="2">[{ oxmultilang ident="ORDER_DOWNLOADS_COUNTOFRESETS" }]</td>
    [{/block}]
</tr>
[{assign var="blWhite" value=""}]
[{foreach from=$edit item=listfile name=orderFiles}]
<tr id="file.[{$smarty.foreach.orderFiles.iteration}]">
    [{block name="admin_order_downloads_filelist"}]
        [{assign var="listclass" value=listitem$blWhite }]
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxarticleartnum->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxarticletitle->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxfilename->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxfirstdownload->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxlastdownload->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxdownloadcount->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxmaxdownloadcount->value}]</td>
        <td valign="top" class="[{ $listclass}]">[{ $listfile->oxorderfiles__oxvaliduntil->value }]</td>
        <td valign="top" class="[{ $listclass}]">&nbsp;&nbsp;[{ $listfile->oxorderfiles__oxresetcount->value}] </td>
        <td align="right" class="[{ $listclass}]">
            &nbsp;&nbsp;&nbsp;
            <input class="edittext" type="submit" onClick="document.forms['search'].oxorderfileid.value = '[{ $listfile->oxorderfiles__oxid->value }]';" value="[{ oxmultilang ident="ORDER_DOWNLOADS_RESET" }]" [{$readonly}]>
        </td>
    [{/block}]
</tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
</table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
