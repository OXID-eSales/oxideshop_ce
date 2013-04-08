[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{assign var="currency" value=$oView->getActCurrency()}]
[{foreach from=$oView->getBargainArticleList() item=_product name=bargainList}]
[{if $smarty.foreach.bargainList.first}]
    [{ assign var="sBargainArtTitle" value="`$_product->oxarticles__oxtitle->value` `$_product->oxarticles__oxvarselect->value`" }]
    [{capture name="bargainTitle"}]
        <a id="titleBargain_[{$smarty.foreach.bargainList.iteration}]" href="[{$_product->getMainLink()}]" class="title">[{ $sBargainArtTitle|strip_tags }]</a>
    [{/capture}]
    [{capture name="bargainPic"}]
        <a href="[{$_product->getMainLink()}]"><img src="[{$_product->getThumbnailUrl()}]" alt="[{ $sBargainArtTitle|strip_tags }]" class="picture"></a>
    [{/capture}]
    [{capture name="bargainPrice"}]
        [{block name="widget_product_bargainitem_price"}]
            <div class="price [{if $_product->getPricePerUnit()}]tight[{/if}]" id="priceBargain_[{$smarty.foreach.bargainList.iteration}]">
                <div>
                [{oxhasrights ident="SHOWARTICLEPRICE"}]
                    [{if $_product->getTPrice()}]
                        <span class="priceOld">
                            [{ oxmultilang ident="REDUCED_FROM_2" }] <del>[{ $_product->getFTPrice()}] [{ $currency->sign}]</del>
                        </span>
                    [{/if}]
                    [{if $_product->getFPrice()}]
                        [{assign var="currency" value=$oView->getActCurrency()}]
                        [{assign var="sFrom" value=""}]
                        [{assign var="fPrice" value=$_product->getFPrice()}]
                        [{if $_product->isParentNotBuyable() }]
                            [{assign var="fPrice" value=$_product->getFVarMinPrice()}]
                            [{if $_product->isRangePrice() }]
                                [{assign var="sFrom" value="PRICE_FROM"|oxmultilangassign}]
                            [{/if}]
                        [{/if}]
                        <span class="priceValue">[{$sFrom}] [{$fPrice}] [{$currency->sign}]
                         [{if $oView->isVatIncluded() }]
                             [{if !( $_product->hasMdVariants() || ($oViewConf->showSelectListsInList()&&$_product->getSelections(1)) || $_product->getVariants() )}]*[{/if}]
                         [{/if}]
                         </span>
                    [{/if}]
                    [{if $_product->getPricePerUnit()}]
                    <span class="pricePerUnit">
                        [{$_product->oxarticles__oxunitquantity->value}] [{$_product->getUnitName()}] | [{$_product->getPricePerUnit()}] [{ $currency->sign}]/[{$_product->getUnitName()}]
                    </span>
                    [{/if}]
                    [{block name="widget_product_bargainitem_tobasket"}]
                        [{ if !( $_product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $_product->getSelections(1)) || $_product->getVariants() )}]
                            <a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=start" params="fnc=tobasket&amp;aid=`$_product->oxarticles__oxid->value`&amp;am=1"}]" class="toCart button" title="[{oxmultilang ident="ADD_TO_CART" }]">[{oxmultilang ident="ADD_TO_CART" }]</a>
                        [{else}]
                            <a href="[{$_product->getMainLink()}]" class="toCart button">[{ oxmultilang ident="MORE_INFO" }]</a>
                        [{/if}]
                    [{/block}]
                [{/oxhasrights}]
                </div>
            </div>
        [{/block}]
    [{/capture}]
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