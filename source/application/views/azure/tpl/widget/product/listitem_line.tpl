[{block name="widget_product_listitem_line"}]
    [{oxscript include="js/widgets/oxlistremovebutton.js" priority=10 }]
    [{oxscript add="$('button.removeButton').oxListRemoveButton();"}]
    [{assign var="currency" value=$oView->getActCurrency()}]
    [{if $showMainLink}]
        [{assign var='_productLink' value=$product->getMainLink()}]
    [{else}]
        [{assign var='_productLink' value=$product->getLink()}]
    [{/if}]
    [{assign var="aVariantSelections" value=$product->getVariantSelections(null,null,1)}]
    [{assign var="blShowToBasket" value=true}] [{* tobasket or more info ? *}]
    [{if $blDisableToCart || $product->isNotBuyable()||($aVariantSelections&&$aVariantSelections.selections)||$product->getVariants()||($oViewConf->showSelectListsInList()&&$product->getSelections(1))}]
        [{assign var="blShowToBasket" value=false}]
    [{/if}]

<form name="tobasket.[{$testid}]" [{if $blShowToBasket}]action="[{ $oViewConf->getSelfActionLink() }]" method="post"[{else}]action="[{$_productLink}]" method="get"[{/if}]  class="js-oxProductForm">
    [{ $oViewConf->getNavFormParams() }]
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="pgNr" value="[{ $oView->getActPage() }]">
    [{if $recommid}]
        <input type="hidden" name="recommid" value="[{ $recommid }]">
    [{/if}]
    [{ if $blShowToBasket}]
        [{oxhasrights ident="TOBASKET"}]
            <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
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
            <input id="am_[{$testid}]" type="hidden" name="am" value="1">
        [{/oxhasrights}]
    [{else}]
        <input type="hidden" name="cl" value="details">
        <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
    [{/if}]

    [{block name="widget_product_listitem_line_picturebox"}]
        <div class="pictureBox">
            <a class="sliderHover" href="[{ $_productLink }]" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"></a>
            <a href="[{$_productLink}]" class="viewAllHover glowShadow corners" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"><span>[{oxmultilang ident="WIDGET_PRODUCT_PRODUCT_DETAILS"}]</span></a>
            <img src="[{$product->getThumbnailUrl()}]" alt="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
        </div>
    [{/block}]

    <div class="infoBox">
        [{block name="widget_product_listitem_line_selections"}]
            <div class="info">
                <a id="[{$testid}]" href="[{$_productLink}]" class="title" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
                    <span>[{ $product->oxarticles__oxtitle->value }] [{$product->oxarticles__oxvarselect->value}]</span>
                </a>
                <div class="variants">
                    [{if $aVariantSelections && $aVariantSelections.selections }]
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
                    [{/if}]
                </div>
            </div>
        [{/block}]
        [{block name="widget_product_listitem_line_description"}]
            <div class="description">
                [{if $recommid }]
                    <div>[{ $product->text|truncate:160:"..." }]</div>
                [{else}]
                    [{oxhasrights ident="SHOWSHORTDESCRIPTION"}]
                        [{$product->oxarticles__oxshortdesc->value|truncate:160:"..."}]
                    [{/oxhasrights}]
                [{/if}]
            </div>
        [{/block}]
    </div>
    <div class="functions">
            [{if $oViewConf->getShowCompareList()}]
                [{oxid_include_dynamic file="widget/product/compare_links.tpl" testid="_`$testid`" type="compare" aid=$product->oxarticles__oxid->value anid=$altproduct in_list=$product->isOnComparisonList() page=$oView->getActPage()}]
            [{/if}]
            [{block name="widget_product_listitem_line_price"}]
                [{oxhasrights ident="SHOWARTICLEPRICE"}]
                    [{assign var=tprice value=$product->getTPrice()}]
                    [{assign var=price  value=$product->getPrice()}]
                    [{if $tprice && $tprice->getBruttoPrice() > $price->getBruttoPrice()}]
                        <span class="oldPrice">
                            [{oxmultilang ident="WIDGET_PRODUCT_PRODUCT_REDUCEDFROM"}] <del>[{$product->getFTPrice()}] [{$currency->sign}]</del>
                        </span>
                    [{/if}]
                    [{block name="widget_product_listitem_line_price_value"}]
                        <label id="productPrice_[{$testid}]" class="price">
                            <span>
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
                                        [{/if}]
                            </span> [{ $currency->sign}]
                            [{if $oView->isVatIncluded() }]
                                [{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}]*[{/if}]
                            [{/if}]
                        </label>
                    [{/block}]

                    [{if $product->loadAmountPriceInfo()}]
                        [{oxscript include="js/widgets/oxamountpriceselect.js" priority=10 }]
                        [{include file="page/details/inc/priceinfo.tpl" oDetailsProduct=$product}]
                    [{/if}]

                    [{if $product->getPricePerUnit()}]
                        <span id="productPricePerUnit_[{$testid}]" class="pricePerUnit">
                            [{$product->oxarticles__oxunitquantity->value}] [{$product->getUnitName()}] | [{$product->getPricePerUnit()}] [{ $currency->sign}]/[{$product->getUnitName()}]
                        </span>
                    [{elseif $product->oxarticles__oxweight->value  }]
                        <span id="productPricePerUnit_[{$testid}]" class="pricePerUnit">
                            <span title="weight">[{ oxmultilang ident="WIDGET_PRODUCT_PRODUCT_ARTWEIGHT" }]</span>
                            <span class="value">[{ $product->oxarticles__oxweight->value }] [{ oxmultilang ident="WIDGET_PRODUCT_PRODUCT_ARTWEIGHT2" }]</span>
                        </span>
                    [{/if}]
                [{/oxhasrights}]
            [{/block}]
            [{block name="widget_product_listitem_line_tobasket"}]
                <div class="tobasketFunction clear">
                    [{if $blShowToBasket }]
                        [{oxhasrights ident="TOBASKET"}]
                            <input id="amountToBasket_[{$testid}]" type="text" name="am" value="1" size="3" autocomplete="off" class="textbox">
                            <button id="toBasket_[{$testid}]" type="submit" class="submitButton largeButton">[{oxmultilang ident="DETAILS_ADDTOCART"}]</button>
                        [{/oxhasrights}]
                    [{else}]
                        <a class="submitButton largeButton" href="[{ $_productLink }]" >[{ oxmultilang ident="WIDGET_PRODUCT_PRODUCT_MOREINFO" }]</a>
                    [{/if}]
                    [{if $removeFunction && (($owishid && ($owishid==$oxcmp_user->oxuser__oxid->value)) || (($wishid==$oxcmp_user->oxuser__oxid->value)) || $recommid) }]
                        <button triggerForm="remove_[{$removeFunction}][{$testid}]" type="submit" class="submitButton largeButton removeButton"><span>[{ oxmultilang ident="WIDGET_PRODUCT_PRODUCT_REMOVE" }]</span></button>
                    [{/if}]
                </div>
            [{/block}]
        </div>
    </form>
    [{if $removeFunction && (($owishid && ($owishid==$oxcmp_user->oxuser__oxid->value)) || (($wishid==$oxcmp_user->oxuser__oxid->value)) || $recommid) }]
        <form action="[{ $oViewConf->getSelfActionLink() }]" method="post" id="remove_[{$removeFunction}][{$testid}]">
            <div>
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
                <input type="hidden" name="fnc" value="[{$removeFunction}]">
                <input type="hidden" name="aid" value="[{$product->oxarticles__oxid->value}]">
                <input type="hidden" name="am" value="0">
                <input type="hidden" name="itmid" value="[{$product->getItemKey()}]">
                [{if $recommid}]
                    <input type="hidden" name="recommid" value="[{$recommid}]">
                [{/if}]
            </div>
        </form>
    [{/if}]
[{/block}]
