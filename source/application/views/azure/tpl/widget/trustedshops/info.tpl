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
            <img src="[{$oViewConf->getImageUrl('trustedshops_m.gif')}]" title="[{ oxmultilang ident="TRUSTED_SHOPS_IMGTITLE" }]">
        </a>
    </div>
    <div id="tsText">
        <a id="tsProfile" class="js-external" title="[{ oxmultilang ident="MORE" }]" href="[{ oxmultilang ident="TRUSTED_SHOPS_PROFILE_LINK" }][{$tsId}].html">
            [{$oxcmp_shop->oxshops__oxname->value}] [{ oxmultilang ident="MESSAGE_TRUSTED_SHOPS_SEAL_OF_APPROVAL" }]
        </a>
    </div>
[{else}]
    <a id="tsMembership" class="js-external" href="[{ oxmultilang ident="TRUSTED_SHOPS_LINK" }]">
        [{assign var="sTrustShopImg" value="trustedshops_"|cat:$oViewConf->getActLanguageAbbr()|cat:".gif" }]
        <img src="[{$oViewConf->getImageUrl($sTrustShopImg)}]" alt="[{ oxmultilang ident="MORE" }]">
    </a>
[{/if}]
[{assign var="tsRatings" value=$oViewConf->getTsRatings()}]
[{if !$tsRatings.empty}]
<span class='hidden' xmlns:v="http://rdf.data-vocabulary.org/#" typeof="v:Review-aggregate">
    <span rel="v:rating">
            <span property="v:value">[{$tsRatings.result}] </span>
        </span> /
    <span property="v:best">[{$tsRatings.max}] </span> von <span property="v:count">[{$tsRatings.count}]</span>
        <a href="https://www.trustedshops.de/bewertung/info_[{$tsId}].html" title="[{$tsRatings.shopName}] Bewertungen">[{$tsRatings.shopName}] Bewertungen</a>
</span>
[{/if}]
<!-- / Trusted Shops Siegel -->