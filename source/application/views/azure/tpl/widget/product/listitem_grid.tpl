[{block name="widget_product_listitem_grid"}]
    [{assign var="product"              value=$oView->getProduct()       }]
    [{assign var="blDisableToCart"      value=$oView->getDisableToCart()    }]
    [{assign var="iIndex"               value=$oView->getIndex()           }]
    [{assign var="showMainLink"         value=$oView->getShowMainLink()     }]

    [{if $showMainLink}]
        [{assign var='_productLink' value=$product->getMainLink()}]
    [{else}]
        [{assign var='_productLink' value=$product->getLink()}]
    [{/if}]
    [{assign var="blShowToBasket" value=true}] [{* tobasket or more info ? *}]
    [{if $blDisableToCart || $product->isNotBuyable()||$product->hasMdVariants()||($oViewConf->showSelectListsInList() && $product->getSelections(1))||$product->getVariants()}]
        [{assign var="blShowToBasket" value=false}]
    [{/if}]
    [{capture name=product_price}]
        [{block name="widget_product_listitem_grid_price"}]
            [{oxhasrights ident="SHOWARTICLEPRICE"}]
                [{if $product->getTPrice()}]
                    <span class="priceOld">
                        [{ oxmultilang ident="REDUCED_FROM_2" }] <del>[{oxprice price=$product->getTPrice() currency=$oView->getActCurrency()}]</del>
                    </span>
                [{/if}]
                [{block name="widget_product_listitem_grid_price_value"}]
                    [{if $product->getPrice()}]
                        <strong><span>
                        [{if $product->isRangePrice()}]
                                                [{ oxmultilang ident="PRICE_FROM" }]
                                                [{if !$product->isParentNotBuyable() }]
                                                    [{assign var="oPrice" value=$product->getMinPrice() }]
                                                [{else}]
                                                    [{assign var="oPrice" value=$product->getVarMinPrice() }]
                                                [{/if}]
                                        [{else}]
                                                [{if !$product->isParentNotBuyable() }]
                                                    [{assign var="oPrice" value=$product->getPrice() }]
                                                [{else}]
                                                    [{assign var="oPrice" value=$product->getVarMinPrice() }]
                                                [{/if}]
                                        [{/if}]
                        </span> [{oxprice price=$oPrice currency=$oView->getActCurrency()}]
                        [{if $oView->isVatIncluded() }]
                            [{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}] *[{/if}]</strong>
                        [{/if}]
                    [{/if}]
                [{/block}]
                [{if $product->getUnitPrice()}]
                    <span id="productPricePerUnit_[{$iIndex}]" class="pricePerUnit">
                        [{$product->getUnitQuantity()}] [{$product->getUnitName()}] | [{oxprice price=$product->getUnitPrice() currency=$oView->getActCurrency() }] /[{$product->getUnitName()}]
                    </span>
                [{elseif $product->oxarticles__oxweight->value  }]
                    <span id="productPricePerUnit_[{$iIndex}]" class="pricePerUnit">
                        <span title="weight">[{ oxmultilang ident="WEIGHT" suffix="COLON" }]</span>
                        <span class="value">[{ $product->oxarticles__oxweight->value }] [{ oxmultilang ident="KG" }]</span>
                    </span>
                [{/if}]
            [{/oxhasrights}]
        [{/block}]
    [{/capture}]
    <a id="[{$iIndex}]" href="[{$_productLink}]" class="titleBlock title fn" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
        <span>[{ $product->oxarticles__oxtitle->value }] [{$product->oxarticles__oxvarselect->value}]</span>
        <div class="gridPicture">
            <img src="[{$product->getThumbnailUrl()}]" alt="[{ $product->oxarticles__oxtitle->value }] [{$product->oxarticles__oxvarselect->value}]">
        </div>
    </a>

    [{block name="widget_product_listitem_grid_tobasket"}]
        <div class="priceBlock">
            [{oxhasrights ident="TOBASKET"}]
                [{$smarty.capture.product_price}]
                [{if !$blShowToBasket }]
                    <a href="[{ $_productLink }]" class="toCart button">[{ oxmultilang ident="MORE_INFO" }]</a>
                [{else}]
                    [{assign var="listType" value=$oView->getListType()}]

                    <a href="[{$oView->getLink()|oxaddparams:"listtype=`$listType`&amp;fnc=tobasket&amp;aid=`$product->oxarticles__oxid->value`&amp;am=1" }]" class="toCart button" title="[{oxmultilang ident="TO_CART" }]">[{oxmultilang ident="TO_CART" }]</a>
                [{/if}]
            [{/oxhasrights}]
        </div>
   [{/block}]
[{/block}]

[{oxscript widget=$oView->getClassName()}]
