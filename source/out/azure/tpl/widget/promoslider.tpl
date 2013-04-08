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
            <span class="promoBox">
                <strong class="promoPrice">[{ $oArticle->getFPrice() }] [{ $currency->sign}]</strong>
                <strong class="promoTitle">[{ $oArticle->oxarticles__oxtitle->value }]</strong>
            </span>
            [{/if }]
            [{assign var=sBannerPictureUrl value=$oBanner->getBannerPictureUrl() }]
            [{if $sBannerPictureUrl }]
            <img src="[{ $sBannerPictureUrl }]" height="220" width="940" alt="[{$oBanner->oxactions__oxtitle->value}]">
            [{/if }]
            [{if $sBannerLink }]
            </a>
            [{/if}]
        </li>
        [{/foreach }]
    </ul>
[{/if }]
