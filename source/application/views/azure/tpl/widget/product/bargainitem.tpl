[{assign var="_product" value=$oView->getBoxProduct()}]
[{assign var="iIteration" value=$oView->getIteration()}]

[{capture name="bargainPic"}]
    <a href="[{$_product->getMainLink()}]"><img src="[{$_product->getThumbnailUrl()}]" alt="[{ $sBargainArtTitle|strip_tags }]" class="picture"></a>
[{/capture}]
[{capture name="bargainPrice"}]
    [{block name="widget_product_bargainitem_price"}]
        <div class="price [{if $_product->getPricePerUnit()}]tight[{/if}]" id="priceBargain_[{$iIteration}]">
            <div>
                [{oxhasrights ident="SHOWARTICLEPRICE"}]
                    [{if $_product->getTPrice()}]
                        <span class="priceOld">
                            [{ oxmultilang ident="REDUCED_FROM_2" }] <del>[{ $_product->getFTPrice()}] [{ $oView->getActCurrencySign()}]</del>
                        </span>
                    [{/if}]
                    [{if $_product->getFPrice()}]
                        [{assign var="sFrom" value=""}]
                        [{assign var="fPrice" value=$_product->getFPrice()}]
                        [{if $_product->isParentNotBuyable() }]
                            [{assign var="fPrice" value=$_product->getFVarMinPrice()}]
                            [{if $_product->isRangePrice() }]
                                [{assign var="sFrom" value="PRICE_FROM"|oxmultilangassign}]
                            [{/if}]
                        [{/if}]
                        <span class="priceValue">[{$sFrom}] [{$fPrice}] [{$oView->getActCurrencySign()}]
                        [{if $oView->isVatIncluded() }]
                            [{if !( $_product->hasMdVariants() || ($oViewConf->showSelectListsInList()&&$_product->getSelections(1)) || $_product->getVariants() )}]*[{/if}]
                        [{/if}]
                        </span>
                    [{/if}]
                    [{if $_product->getPricePerUnit()}]
                        <span class="pricePerUnit">
                            [{$_product->oxarticles__oxunitquantity->value}] [{$_product->getUnitName()}] | [{$_product->getPricePerUnit()}] [{ $oView->getActCurrencySign()}]/[{$_product->getUnitName()}]
                        </span>
                    [{/if}]
                    [{block name="widget_product_bargainitem_tobasket"}]
                        [{if !( $_product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $_product->getSelections(1)) || $_product->getVariants() )}]
                            <a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=start" params="fnc=tobasket&amp;aid=`$_product->oxarticles__oxid->value`&amp;am=1"}]" class="toCart button" title="[{oxmultilang ident="TO_CART" }]">[{oxmultilang ident="TO_CART" }]</a>
                        [{else}]
                            <a href="[{$_product->getMainLink()}]" class="toCart button">[{ oxmultilang ident="MORE_INFO" }]</a>
                        [{/if}]
                    [{/block}]
                [{/oxhasrights}]
            </div>
        </div>
    [{/block}]
[{/capture}]

<div class="specBoxInfo">
    [{$smarty.capture.bargainPrice}]
    [{$smarty.capture.bargainPic}]
</div>

[{oxscript widget=$oView->getClassName()}]