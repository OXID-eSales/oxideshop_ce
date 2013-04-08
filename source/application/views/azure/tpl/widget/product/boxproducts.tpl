[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{oxscript include="js/widgets/oxarticlebox.js" priority=10 }]
[{oxscript add="$( 'ul.js-articleBox' ).oxArticleBox();" }]
<div class="box" [{if $_boxId}]id="[{$_boxId}]"[{/if}]>
    [{if $_sHeaderIdent}]
        <h3 class="clear [{if $_sHeaderCssClass}] [{$_sHeaderCssClass}][{/if}]">
            [{ oxmultilang ident=$_sHeaderIdent }]
            [{assign var='rsslinks' value=$oView->getRssLinks() }]
            [{if $rsslinks.topArticles}]
                <a class="rss js-external" id="rssTopProducts" href="[{$rsslinks.topArticles.link}]" title="[{$rsslinks.topArticles.title}]"><img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslinks.topArticles.title}]"><span class="FXgradOrange corners glowShadow">[{$rsslinks.topArticles.title}]</span></a>
            [{/if }]
        </h3>
    [{/if}]
    <ul class="js-articleBox featuredList">
    [{foreach from=$_oBoxProducts item=_oBoxProduct name=_sProdList}]
            [{ assign var="_sTitle" value="`$_oBoxProduct->oxarticles__oxtitle->value` `$_oBoxProduct->oxarticles__oxvarselect->value`"|strip_tags}]
            [{block name="widget_product_boxproduct_image"}]
                <li class="articleImage" [{if !$smarty.foreach._sProdList.first}] style="display:none;" [{/if}]>
                    <a class="articleBoxImage" href="[{ $_oBoxProduct->getMainLink() }]">
                        <img src="[{$_oBoxProduct->getIconUrl()}]" alt="[{$_sTitle}]">
                    </a>
                </li>
            [{/block}]

            [{block name="widget_product_boxproduct_price"}]
                [{ assign var="currency" value=$oView->getActCurrency()}]
                <li class="articleTitle">
                    <a href="[{ $_oBoxProduct->getMainLink() }]">
                        [{ $_sTitle }]<br>
                        [{oxhasrights ident="SHOWARTICLEPRICE"}]
                            [{if $_oBoxProduct->getFPrice()}]
                                <strong> [{if $_oBoxProduct->isRangePrice()}]
                                                [{ oxmultilang ident="PRICE_FROM" }]
                                                [{if !$_oBoxProduct->isParentNotBuyable() }]
                                                    [{ $_oBoxProduct->getFMinPrice() }]
                                                [{else}]
                                                    [{ $_oBoxProduct->getFVarMinPrice() }]
                                                [{/if}]
                                        [{else}]
                                                [{if !$_oBoxProduct->isParentNotBuyable() }]
                                                    [{ $_oBoxProduct->getFPrice() }]
                                                [{else}]
                                                    [{ $_oBoxProduct->getFVarMinPrice() }]
                                                [{/if}]
                                        [{/if}]
                                [{ $currency->sign}]</strong>
                            [{/if}]
                        [{/oxhasrights}]
                    </a>
                </li>
            [{/block}]
    [{/foreach}]
    </ul>
</div>