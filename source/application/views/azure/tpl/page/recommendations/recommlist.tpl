[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{capture append="oxidBlock_content"}]
    [{if $oView->getActiveRecommList() }]
        [{assign var="_actvrecommlist" value=$oView->getActiveRecommList() }]
        [{assign var="recommendation_head" value="LIST_BY"|oxmultilangassign}]
        [{assign var="recommendation_head" value=$_actvrecommlist->oxrecommlists__oxtitle->value|cat:" <span>("|cat:$recommendation_head|cat:" "|cat:$_actvrecommlist->oxrecommlists__oxauthor->value|cat:")</span>"}]
        [{assign var="rsslinks" value=$oView->getRssLinks() }]
        [{if $oxcmp_user}]
            [{assign var="force_sid" value=$oView->getSidForWidget()}]
        [{/if}]

        <h1 class="pageHead">[{$recommendation_head}]

        [{assign var='rsslinks' value=$oView->getRssLinks() }]

        [{if $rsslinks.recommlistarts}]
            <a class="rss js-external" id="rssRecommListProducts" href="[{$rsslinks.recommlistarts.link}]" title="[{$rsslinks.recommlistarts.title}]">
                <img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslinks.recommlistarts.title}]">
                <span class="FXgradOrange corners glowShadow">[{$rsslinks.recommlistarts.title}]</span>
            </a>
        [{/if }]

        </h1>
        <div class="listRefine clear bottomRound">
            [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation()}]
        </div>

        [{block name="recommendations_desc"}]
            <div>
                <div class="clear">
                    <div>
                        [{ $_actvrecommlist->oxrecommlists__oxdesc->value }]
                    </div>
                    [{if $oView->isReviewActive()}]
                    <div class="rating clear">
                        [{oxid_include_widget cl="oxwRating" blCanRate=$oView->canRate() _parent=$oViewConf->getTopActiveClassName() nocookie=1 force_sid=$force_sid sRateUrl=$_actvrecommlist->getLink() dRatingCount=$oView->getRatingCount() dRatingValue=$oView->getRatingValue() recommid=$_actvrecommlist->getId() user=$oxcmp_user}]
                    </div>
                    [{/if}]
                </div>
            </div>
        [{/block}]
        [{* List types: grid|line *}]
        [{include file="widget/product/list.tpl" type="line" listId="productList" products=$oView->getArticleList() recommid=$_actvrecommlist->getId()}]
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
        [{if $oView->isReviewActive()}]
        <div class="widgetBox reviews">
            <h4>[{oxmultilang ident="WRITE_PRODUCT_REVIEW"}]</h4>
            [{oxid_include_widget cl="oxwReview" nocookie=1 force_sid=$force_sid _parent=$oView->getClassName() type=oxrecommlist recommid=$_actvrecommlist->getId() canrate=$oView->canRate() skipESIforUser=1}]
        </div>
        [{/if}]
    [{else}]

        [{assign var="hitsfor" value="HITS_FOR"|oxmultilangassign }]
        [{assign var="recommendation_head" value=$oView->getArticleCount()|cat:" "|cat:$hitsfor|cat:" &quot;"|cat:$oView->getSearchForHtml()|cat:"&quot;" }]

        <h1 class="pageHead">[{$recommendation_head}]</h1>
        [{ include file="page/recommendations/inc/list.tpl"}]
    [{/if}]
    [{ insert name="oxid_tracker"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
