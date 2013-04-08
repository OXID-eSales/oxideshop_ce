[{oxscript include="js/widgets/oxcenterelementonhover.js" priority=10 }]
[{oxscript add="$( '#specCatBox' ).oxCenterElementOnHover();" }]
[{capture append="oxidBlock_content"}]
    [{assign var="oFirstArticle" value=$oView->getFirstArticle()}]
    [{if $oView->getCatOfferArticleList()|@count > 0}]
        [{foreach from=$oView->getCatOfferArticleList() item=actionproduct name=CatArt}]
        [{if $smarty.foreach.CatArt.first}]
        [{assign var="oCategory" value=$actionproduct->getCategory()}]
            [{if $oCategory }]
                [{assign var="promoCatTitle" value=$oCategory->oxcategories__oxtitle->value}]
                [{assign var="promoCatImg" value=$oCategory->getPromotionIconUrl()}]
                [{assign var="promoCatLink" value=$oCategory->getLink()}]
            [{/if}]
        [{/if}]
        [{/foreach}]
    [{/if}]
    [{if $oView->getBargainArticleList()|@count > 0 || ($promoCatTitle && $promoCatImg)}]
        <div class="promoBoxes clear">
            [{if count($oView->getBargainArticleList()) > 0 }]
                <div id="specBox" class="specBox">
                    [{include file="widget/product/bargainitems.tpl"}]
                </div>
            [{/if}]
            [{if $promoCatTitle && $promoCatImg}]
                <div id="specCatBox" class="specCatBox">
                    <h2 class="sectionHead">[{$promoCatTitle}]</h2>
                    <a href="[{$promoCatLink}]" class="viewAllHover glowShadow corners"><span>[{ oxmultilang ident="VIEW_ALL_PRODUCTS" }]</span></a>
                    <img src="[{$promoCatImg}]" alt="[{$promoCatTitle}]">
                </div>
            [{/if}]
        </div>
    [{/if}]
    [{include file="widget/manufacturersslider.tpl" }]
    [{if $oView->getNewestArticles() }]
        [{assign var='rsslinks' value=$oView->getRssLinks() }]
        [{include file="widget/product/list.tpl" type=$oViewConf->getViewThemeParam('sStartPageListDisplayType') head="JUST_ARRIVED"|oxmultilangassign listId="newItems" products=$oView->getNewestArticles() rsslink=$rsslinks.newestArticles rssId="rssNewestProducts" showMainLink=true}]
    [{/if}]
    [{ insert name="oxid_tracker"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Right"}]