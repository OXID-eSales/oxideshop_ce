[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{foreach from=$oView->getBargainArticleList() item=_product name=bargainList}]
    [{if $smarty.foreach.bargainList.first}]
        [{ oxid_include_widget cl="oxwArticleBox" cur=$oView->getActCurrency() _parent=$oView->getClassName() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() sProductId=$_product->getId() sWidgetType=product sListType=bargainitem }]
                    [{/if}]
[{/foreach}]
<div class="specBoxTitles rightShadow">
    <h3>

        <strong>[{ oxmultilang ident="WEEK_SPECIAL" }]</strong>

        [{assign var='rsslinks' value=$oView->getRssLinks() }]
        [{if $rsslinks.bargainArticles}]
            <a class="rss js-external" id="rssBargainProducts" href="[{$rsslinks.bargainArticles.link}]" title="[{$rsslinks.bargainArticles.title}]"><img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslinks.bargainArticles.title}]"><span class="FXgradOrange corners glowShadow">[{$rsslinks.bargainArticles.title}]</span></a>
        [{/if}]
    </h3>
    [{$smarty.capture.bargainTitle}]
</div>
<div class="specBoxInfo">
    [{$smarty.capture.bargainPrice}]
    [{$smarty.capture.bargainPic}]
</div>