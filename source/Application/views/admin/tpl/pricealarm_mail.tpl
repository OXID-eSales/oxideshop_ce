[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="payment_main">
</form>


<b>[{oxmultilang ident="PRICEALARM_MAIL_OXIDPRICEALARM"}]</b>
<br><br>
[{oxmultilang ident="PRICEALARM_MAIL_OPENEMAILS1"}] [{$iAllCnt}] [{oxmultilang ident="PRICEALARM_MAIL_OPENEMAILS2"}]
<br><br>
[{if $iAllCnt && !$readonly}]
[{oxmultilang ident="PRICEALARM_MAIL_SENDPRICEALARMEMAIL1"}] <a href="[{$oViewConf->getSelfLink()}]&cl=pricealarm_send" class="edittext" target="list" [{if $readonly}]onclick="JavaScript:return false;"[{/if}]><b>[{oxmultilang ident="PRICEALARM_MAIL_SENDPRICEALARMEMAIL2"}]</b></a>
[{/if}]

[{include file="bottomnaviitem.tpl"}]
</table>
[{include file="bottomitem.tpl"}]