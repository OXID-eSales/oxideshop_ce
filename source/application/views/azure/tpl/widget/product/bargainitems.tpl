[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{assign var="currency" value=$oView->getActCurrency()}]

[{foreach from=$oView->getBargainArticleList() item=_product name=bargainList}]

    [{if $smarty.foreach.bargainList.first}]
        [{assign var="sBargainArtTitle" value="`$_product->oxarticles__oxtitle->value` `$_product->oxarticles__oxvarselect->value`"}]
        [{assign var="iIteration" value=$smarty.foreach.bargainList.iteration}]

        [{capture name="bargainTitle"}]
        <a id="titleBargain_[{$iIteration}]" href="[{$_product->getMainLink()}]" class="title">[{ $sBargainArtTitle|strip_tags }]</a>
        [{/capture}]

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

        [{oxid_include_widget cl="oxwArticleBox" currencySign=$currency->sign _parent=$oView->getClassName() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() anid=$_product->getId() iIteration=$iIteration sWidgetType=product sListType=bargainitem}]
    [{/if}]

[{/foreach}]
