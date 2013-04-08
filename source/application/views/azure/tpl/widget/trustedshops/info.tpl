[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
<!-- Trusted Shops Siegel -->
[{if $oView->getTrustedShopId() }]
    [{assign var="tsId" value=$oView->getTrustedShopId() }]
[{/if}]

[{if $oView->getTSExcellenceId() }]
    [{assign var="tsId" value=$oView->getTSExcellenceId() }]
[{/if}]

[{if $tsId }]
    <div id="tsSeal">
        <a id="tsCertificate" class="js-external" href="https://www.trustedshops.com/shop/certificate.php?shop_id=[{$tsId}]">
            <img src="[{$oViewConf->getImageUrl('trustedshops_m.gif')}]" title="[{ oxmultilang ident="WIDGET_TRUSTEDSHOPS_ITEM_IMGTITLE" }]">
        </a>
    </div>
    <div id="tsText">
        <a id="tsProfile" class="js-external" title="[{ oxmultilang ident="WIDGET_TRUSTEDSHOPS_ITEM_ALTTEXT" }]" href="[{ oxmultilang ident="WIDGET_TRUSTEDSHOPS_ITEM_PROFILELINK" }][{$tsId}].html">
            [{$oxcmp_shop->oxshops__oxname->value}] [{ oxmultilang ident="WIDGET_TRUSTEDSHOPS_ITEM_SEALOFAPPROVAL" }]
        </a>
    </div>
[{else}]
    <a id="tsMembership" class="js-external" href="[{ oxmultilang ident="WIDGET_TRUSTEDSHOPS_ITEM_LINK" }]">
        [{assign var="sTrustShopImg" value="trustedshops_"|cat:$oViewConf->getActLanguageAbbr()|cat:".gif" }]
        <img src="[{$oViewConf->getImageUrl($sTrustShopImg)}]" alt="[{ oxmultilang ident="WIDGET_TRUSTEDSHOPS_ITEM_ALTTEXT" }]">
    </a>
[{/if}]
<!-- / Trusted Shops Siegel -->