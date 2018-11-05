[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $error}]<div class="errorbox">[{$error}]</div>[{/if}]
[{if $message}]<div class="messagebox">[{$message}]</div>[{/if}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="shop_license">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

[{include file="include/update_views_notice.tpl"}]
<table id="tShopLicense" border="0" width="45%">
    <tr>
        <td class="edittext">
            <br><strong>[{oxmultilang ident="SHOP_LICENSE_VERSION"}]</strong>
        </td>
        <td class="edittext">
            <b>[{oxmultilang ident="GENERAL_OXIDESHOP"}]
               [{$oView->getShopEdition()}] [{$oView->getShopVersion()}]_[{$oView->getRevision()}]
               [{if $oView->isDemoVersion()}]
                   [{oxmultilang ident="SHOP_LICENSE_DEMO"}]
               [{/if}]
            </b>
        </td>
    </tr>
</table>
<table id="tVersionInfo" border="0">
    <tr>
        <td>
            <span>[{$aCurVersionInfo}]</span>
        </td>
    </tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
