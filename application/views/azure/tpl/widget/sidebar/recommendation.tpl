[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{assign var="_oRecommendationList" value=$oView->getSimilarRecommLists()}]
[{assign var="oRecommList" value=$oView->getRecommList() }]

[{ if $_oRecommendationList || $oRecommList->getRecommSearch() }]
<div class="box" id="recommendationsBox">
    <h3>[{ oxmultilang ident="LISTMANIA" }]
    [{assign var='rsslinks' value=$oRecommList->getRssLinks() }]
    [{if $rsslinks.recommlists}]
        <a class="rss js-external" id="rssRecommLists" href="[{$rsslinks.recommlists.link}]" title="[{$rsslinks.recommlists.title}]">
            <img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslinks.recommlists.title}]"><span class="FXgradOrange corners glowShadow">[{$rsslinks.recommlists.title}]</span>
        </a>
    [{/if}]
    </h3>

    <div>
    [{ if $_oRecommendationList }]
        [{$_oRecommendationList->rewind()}]

        [{if $_oRecommendationList->current()}]
               [{assign var="_oFirstRecommendationList" value=$_oRecommendationList->current()}]
            [{assign var="_oBoxTopProduct" value=$_oFirstRecommendationList->getFirstArticle()}]
            [{assign var="_sTitle" value="`$_oBoxTopProduct->oxarticles__oxtitle->value` `$_oBoxTopProduct->oxarticles__oxvarselect->value`"|strip_tags}]
            <a href="[{$_oBoxTopProduct->getMainLink()}]" class="featured" title="[{$_sTitle}]">
                <img src="[{$_oBoxTopProduct->getIconUrl()}]" alt="[{$_sTitle}]">
            </a>
        [{/if}]
    [{/if}]
        <ul class="featuredList">
        [{ if $_oRecommendationList }]
            [{foreach from=$_oRecommendationList item=_oListItem name="testRecommendationsList"}]
                <li>
                    <a href="[{ $_oListItem->getLink() }]"><b>[{ $_oListItem->oxrecommlists__oxtitle->value|strip_tags }]</b></a>
                    <div class="desc">[{ oxmultilang ident="LIST_BY" suffix="COLON" }] [{ $_oListItem->oxrecommlists__oxauthor->value|strip_tags }]</div>
                </li>
            [{/foreach}]
        [{/if}]
            [{ if $_oRecommendationList || $oRecommList->getRecommSearch() }]
            <li>
                <form name="basket" class="recommendationsSearchForm" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
                    <div>
                        <input type="hidden" name="cl" value="recommlist">
                        [{ $oViewConf->getHiddenSid() }]
                    </div>
                    <label>[{ oxmultilang ident="SEARCH_FOR_LISTS" suffix="COLON" }]</label>
                    <input type="text" name="searchrecomm" id="searchRecomm" value="[{$oRecommList->getRecommSearch()}]" class="searchInput">
                    <button class="submitButton largeButton" type="submit">[{ oxmultilang ident="GO" }]</button>
                </form>
            </li>
            [{/if}]
        </ul>
    </div>
</div>
[{/if}]
[{oxscript widget=$oView->getClassName()}]