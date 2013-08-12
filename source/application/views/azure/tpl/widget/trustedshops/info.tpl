[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
<!-- Trusted Shops Siegel -->
[{if $oView->getTrustedShopId() }]
    [{assign var="tsId" value=$oView->getTrustedShopId() }]
[{/if}]

[{if $oView->getTSExcellenceId() }]
    [{assign var="tsId" value=$oView->getTSExcellenceId() }]
[{/if}]

[{if $tsId }]
    [{oxscript include='js/widgets/oxtsbadge.js'}]
    [{oxscript add="$( 'body' ).oxTsBadge({trustedShopId:'`$tsId`'});"}]
    <noscript>
        <a href="https://www.trustedshops.co.uk/shop/certificate.php?shop_id=[{$tsId}]">
            <img title="Trusted Shops Seal of Approval - click to verify." src="//widgets.trustedshops.com/images/badge.png" style="position:fixed;bottom:100;right:100;" />
        </a>
    </noscript>
[{else}]
    <a id="tsMembership" class="js-external" href="[{ oxmultilang ident="TRUSTED_SHOPS_LINK" }]">
        [{assign var="sTrustShopImg" value="trustedshops_"|cat:$oViewConf->getActLanguageAbbr()|cat:".gif" }]
        <img src="[{$oViewConf->getImageUrl($sTrustShopImg)}]" alt="[{ oxmultilang ident="MORE" }]">
    </a>
[{/if}]
<!-- / Trusted Shops Siegel -->