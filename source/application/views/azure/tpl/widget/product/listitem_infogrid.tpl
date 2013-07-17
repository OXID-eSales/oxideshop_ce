[{block name="widget_product_listitem_infogrid"}]
    [{assign var="product" value=$oView->getProduct()}]

    [{assign var="currency" value=$oView->getActCurrency()}]
    [{if $showMainLink}]
        [{assign var='_productLink' value=$product->getMainLink()}]
    [{else}]
        [{assign var='_productLink' value=$product->getLink()}]
    [{/if}]
    [{assign var="aVariantSelections" value=$product->getVariantSelections(null,null,1)}]
    [{assign var="blShowToBasket" value=true}] [{* tobasket or more info ? *}]
    [{if $blDisableToCart || $product->isNotBuyable()||($aVariantSelections&&$aVariantSelections.selections)||$product->hasMdVariants()||($oViewConf->showSelectListsInList() && $product->getSelections(1))||$product->getVariants()}]
        [{assign var="blShowToBasket" value=false}]
    [{/if}]

    <form name="tobasket[{$testid}]" [{if $blShowToBasket}]action="[{ $oViewConf->getSelfActionLink() }]" method="post"[{else}]action="[{$_productLink}]" method="get"[{/if}]>
        [{ $oViewConf->getNavFormParams() }]
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="pgNr" value="[{ $oView->getActPage() }]">
        [{if $recommid}]
            <input type="hidden" name="recommid" value="[{ $recommid }]">
        [{/if}]
        [{ if $blShowToBasket}]
            [{oxhasrights ident="TOBASKET"}]
                <input type="hidden" name="cl" value="[{ $oViewConf->getTopActiveClassName() }]">
                [{if $owishid}]
                    <input type="hidden" name="owishid" value="[{$owishid}]">
                [{/if}]
                [{if $toBasketFunction}]
                    <input type="hidden" name="fnc" value="[{$toBasketFunction}]">
                [{else}]
                  <input type="hidden" name="fnc" value="tobasket">
                [{/if}]
                <input type="hidden" name="aid" value="[{ $product->oxarticles__oxid->value }]">
                [{if $altproduct}]
                    <input type="hidden" name="anid" value="[{ $altproduct }]">
                [{else}]
                    <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
                [{/if}]
                <input type="hidden" name="am" value="1">
            [{/oxhasrights}]
        [{else}]
            <input type="hidden" name="cl" value="details">
            <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
        [{/if}]

    [{block name="widget_product_listitem_infogrid_gridpicture"}]
        <div class="pictureBox gridPicture">
            <a class="sliderHover" href="[{ $_productLink }]" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"></a>
            <a href="[{$_productLink}]" class="viewAllHover glowShadow corners" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"><span>[{oxmultilang ident="PRODUCT_DETAILS"}]</span></a>
            <img src="[{$product->getThumbnailUrl()}]" alt="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
        </div>
    [{/block}]

    <div class="listDetails">
        [{block name="widget_product_listitem_infogrid_titlebox"}]
            <div class="titleBox">
                <a id="[{$testid}]" href="[{$_productLink}]" class="title" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
                    <span>[{ $product->oxarticles__oxtitle->value }] [{$product->oxarticles__oxvarselect->value}]</span>
                </a>
            </div>
        [{/block}]

        [{block name="widget_product_listitem_infogrid_selections"}]
                <div class="selectorsBox">
                    [{ if $aVariantSelections && $aVariantSelections.selections }]
                        <div id="variantselector_[{$testid}]" class="selectorsBox js-fnSubmit clear">
                            [{foreach from=$aVariantSelections.selections item=oSelectionList key=iKey}]
                                [{include file="widget/product/selectbox.tpl" oSelectionList=$oSelectionList sJsAction="js-fnSubmit"}]
                            [{/foreach}]
                        </div>
                    [{elseif $oViewConf->showSelectListsInList()}]
                        [{assign var="oSelections" value=$product->getSelections(1)}]
                        [{if $oSelections}]
                            <div id="selectlistsselector_[{$testid}]" class="selectorsBox js-fnSubmit clear">
                                [{foreach from=$oSelections item=oList name=selections}]
                                    [{include file="widget/product/selectbox.tpl" oSelectionList=$oList sFieldName="sel" iKey=$smarty.foreach.selections.index blHideDefault=true sSelType="seldrop" sJsAction="js-fnSubmit"}]
                                [{/foreach}]
                            </div>
                        [{/if}]
                    [{/if }]
                </div>
        [{/block}]

           <div class="priceBox">
                <div class="content">
                    [{if $oViewConf->getShowCompareList()}]
                        [{oxid_include_dynamic file="widget/product/compare_links.tpl" testid="_`$testid`" type="compare" aid=$product->oxarticles__oxid->value anid=$altproduct in_list=$product->isOnComparisonList() page=$oView->getActPage()}]
                    [{/if}]
                    [{block name="widget_product_listitem_infogrid_price"}]
                        [{oxhasrights ident="SHOWARTICLEPRICE"}]
                            [{if $product->getTPrice()}]
                                <span class="oldPrice">
                                  [{ oxmultilang ident="REDUCED_FROM_2" }] <del>[{ $product->getFTPrice()}] [{ $currency->sign}]</del>
                                </span>
                            [{/if}]
                            [{block name="widget_product_listitem_infogrid_price_value"}]
                                [{if $product->getFPrice()}]
                                    <span class="price"><span>
                                        [{if $product->isRangePrice()}]
                                                [{ oxmultilang ident="PRICE_FROM" }]
                                                [{if !$product->isParentNotBuyable() }]
                                                    [{ $product->getFMinPrice() }]
                                                [{else}]
                                                    [{ $product->getFVarMinPrice() }]
                                                [{/if}]
                                        [{else}]
                                                [{if !$product->isParentNotBuyable() }]
                                                    [{ $product->getFPrice() }]
                                                [{else}]
                                                    [{ $product->getFVarMinPrice() }]
                                                [{/if}]
                                        [{/if}]</span>
                                    [{ $currency->sign}]
                                    [{if $oView->isVatIncluded() }]
                                         [{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}]*[{/if}]</span>
                                    [{/if}]
                                [{/if}]
                            [{/block}]
                            [{ if $product->getPricePerUnit()}]
                                <span id="productPricePerUnit_[{$testid}]" class="pricePerUnit">
                                    [{$product->oxarticles__oxunitquantity->value}] [{$product->getUnitName()}] | [{$product->getPricePerUnit()}] [{ $currency->sign}]/[{$product->getUnitName()}]
                                </span>
                            [{elseif $product->oxarticles__oxweight->value  }]
                                <span id="productPricePerUnit_[{$testid}]" class="pricePerUnit">
                                    <span title="weight">[{ oxmultilang ident="WEIGHT" suffix="COLON" }]</span>
                                    <span class="value">[{ $product->oxarticles__oxweight->value }] [{ oxmultilang ident="KG" }]</span>
                                </span>
                            [{/if }]
                        [{/oxhasrights}]
                    [{/block}]
                </div>
            </div>
            [{block name="widget_product_listitem_infogrid_tobasket"}]
                <div class="buttonBox">
                    [{ if $blShowToBasket }]
                        [{oxhasrights ident="TOBASKET"}]
                            <button type="submit" class="submitButton largeButton">[{oxmultilang ident="TO_CART" }]</button>
                        [{/oxhasrights}]
                    [{else}]
                        <a class="submitButton largeButton" href="[{ $_productLink }]" >[{ oxmultilang ident="MORE_INFO" }]</a>
                    [{/if}]
                </div>
            [{/block}]
        </div>
    </form>
[{/block}]
