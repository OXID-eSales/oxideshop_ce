[{assign var=oBanners value=$oView->getBanners() }]
[{assign var="currency" value=$oView->getActCurrency()}]
[{if $oBanners|@count}]
    [{oxstyle include="css/libs/anythingslider.css"}]
    [{oxscript include="js/libs/anythingslider.js"}]
    [{oxscript include="js/widgets/oxslider.js" priority=10 }]
    [{oxscript add="$( '#promotionSlider' ).oxSlider();"}]
    <img src="[{$oViewConf->getImageUrl('promo-shadowleft.png')}]" height="220" width="7" class="promoShadow" alt="">
    <img src="[{$oViewConf->getImageUrl('promo-shadowright.png')}]" height="220" width="7" class="promoShadow shadowRight" alt="">
    <ul id="promotionSlider">
        [{foreach from=$oBanners item=oBanner }]
        [{assign var=oArticle value=$oBanner->getBannerArticle() }]
        <li>
            [{assign var=sBannerLink value=$oBanner->getBannerLink() }]
            [{if $sBannerLink }]
            <a href="[{ $sBannerLink }]">
            [{/if}]
            [{if $oArticle }]
                [{assign var="sFrom" value=""}]
                [{assign var="oPrice" value=$oArticle->getPrice()}]
                [{if $oArticle->isParentNotBuyable() }]
                    [{assign var="oPrice" value=$oArticle->getVarMinPrice()}]
                    [{if $oArticle->isRangePrice() }]
                        [{assign var="sFrom" value="PRICE_FROM"|oxmultilangassign}]
                    [{/if}]
                [{/if}]
                <span class="promoBox [{if $sFrom }]wide[{/if}]">
                    <strong class="promoPrice [{if $sFrom }]wide[{/if}]">[{$sFrom}] [{oxprice price=$oPrice currency=$currency }]</strong>
                    <strong class="promoTitle [{if $sFrom }]wide[{/if}]">[{ $oArticle->oxarticles__oxtitle->value }]</strong>
                </span>
            [{/if}]
            [{assign var=sBannerPictureUrl value=$oBanner->getBannerPictureUrl() }]
            [{if $sBannerPictureUrl }]
            <img src="[{ $sBannerPictureUrl }]" alt="[{$oBanner->oxactions__oxtitle->value}]">
            [{/if}]
            [{if $sBannerLink }]
            </a>
            [{/if}]
        </li>
        [{/foreach}]
    </ul>
[{/if}]
