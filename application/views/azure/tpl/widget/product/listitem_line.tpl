[{block name="widget_product_listitem_line"}]
    [{assign var="product"          value=$oView->getProduct()      }]
    [{assign var="owishid"          value=$oView->getWishId()          }]
    [{assign var="removeFunction"   value=$oView->getRemoveFunction()  }]
    [{assign var="recommid"         value=$oView->getRecommId()        }]
    [{assign var="iIndex"           value=$oView->getIndex()          }]
    [{assign var="showMainLink"     value=$oView->getShowMainLink()    }]
    [{assign var="blDisableToCart"  value=$oView->getDisableToCart()   }]
    [{assign var="toBasketFunction" value=$oView->getToBasketFunction()}]
    [{assign var="altproduct"       value=$oView->getAltProduct()      }]

    [{oxscript include="js/widgets/oxlistremovebutton.js" priority=10 }]
    [{oxscript add="$('button.removeButton').oxListRemoveButton();"}]

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

<form name="tobasket.[{$iIndex}]" [{if $blShowToBasket}]action="[{ $oViewConf->getSelfActionLink() }]" method="post"[{else}]action="[{$_productLink}]" method="get"[{/if}]  class="js-oxProductForm">
    [{ $oViewConf->getNavFormParams() }]
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="pgNr" value="[{ $oView->getActPage() }]">
    [{if $recommid}]
        <input type="hidden" name="recommid" value="[{ $recommid }]">
    [{/if}]
    [{if $blShowToBasket}]
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
            <input id="am_[{$iIndex}]" type="hidden" name="am" value="1">
        [{/oxhasrights}]
    [{else}]
        <input type="hidden" name="cl" value="details">
        <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
    [{/if}]

    [{block name="widget_product_listitem_line_picturebox"}]
        <div class="pictureBox">
            <a class="sliderHover" href="[{ $_productLink }]" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"></a>
            <a href="[{$_productLink}]" class="viewAllHover glowShadow corners" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]"><span>[{oxmultilang ident="PRODUCT_DETAILS"}]</span></a>
            <img src="[{$product->getThumbnailUrl()}]" alt="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
        </div>
    [{/block}]

    <div class="infoBox">
        [{block name="widget_product_listitem_line_selections"}]
            <div class="info">
                <a id="[{$iIndex}]" href="[{$_productLink}]" class="title" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
                    <span>[{ $product->oxarticles__oxtitle->value }] [{$product->oxarticles__oxvarselect->value}]</span>
                </a>
                <div class="variants">
                    [{if $aVariantSelections && $aVariantSelections.selections }]
                        <div id="variantselector_[{$iIndex}]" class="selectorsBox js-fnSubmit clear">
                            [{foreach from=$aVariantSelections.selections item=oSelectionList key=iKey}]
                                [{include file="widget/product/selectbox.tpl" oSelectionList=$oSelectionList sJsAction="js-fnSubmit"}]
                            [{/foreach}]
                        </div>
                    [{elseif $oViewConf->showSelectListsInList()}]
                        [{assign var="oSelections" value=$product->getSelections(1)}]
                        [{if $oSelections}]
                            <div id="selectlistsselector_[{$iIndex}]" class="selectorsBox js-fnSubmit clear">
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
                [{oxid_include_dynamic file="widget/product/compare_links.tpl" testid="_`$iIndex`" type="compare" aid=$product->oxarticles__oxid->value anid=$altproduct in_list=$product->isOnComparisonList() page=$oView->getActPage()}]
            [{/if}]
            [{block name="widget_product_listitem_line_price"}]
                [{oxhasrights ident="SHOWARTICLEPRICE"}]
                    [{if $product->getTPrice()}]
                        <span class="oldPrice">
                            [{oxmultilang ident="REDUCED_FROM_2"}] <del>[{oxprice price=$product->getTPrice() currency=$oView->getActCurrency()}]</del>
                        </span>
                    [{/if}]
                    [{block name="widget_product_listitem_line_price_value"}]
                        [{if $product->getPrice()}]
                            <label id="productPrice_[{$iIndex}]" class="price">
                                <span>
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
                                    [{if !($product->hasMdVariants() || ($oViewConf->showSelectListsInList() && $product->getSelections(1)) || $product->getVariants())}]*[{/if}]
                                [{/if}]
                            </label>
                        [{/if}]
                    [{/block}]

                    [{if $product->loadAmountPriceInfo()}]
                        [{oxscript include="js/widgets/oxamountpriceselect.js" priority=10 }]
                        [{include file="page/details/inc/priceinfo.tpl" oDetailsProduct=$product}]
                    [{/if}]

                    [{if $product->getUnitPrice()}]
                        <span id="productPricePerUnit_[{$iIndex}]" class="pricePerUnit">
                            [{$product->getUnitQuantity()}] [{$product->getUnitName()}] | [{oxprice price=$product->getUnitPrice() currency=$oView->getActCurrency() }]/[{$product->getUnitName()}]
                        </span>
                    [{elseif $product->oxarticles__oxweight->value  }]
                        <span id="productPricePerUnit_[{$iIndex}]" class="pricePerUnit">
                            <span title="weight">[{ oxmultilang ident="WEIGHT" suffix="COLON" }]</span>
                            <span class="value">[{ $product->oxarticles__oxweight->value }] [{ oxmultilang ident="KG" }]</span>
                        </span>
                    [{/if}]
                [{/oxhasrights}]
            [{/block}]
            [{block name="widget_product_listitem_line_tobasket"}]
                <div class="tobasketFunction clear">
                    [{if $blShowToBasket }]
                        [{oxhasrights ident="TOBASKET"}]
                            <input id="amountToBasket_[{$iIndex}]" type="text" name="am" value="1" size="3" autocomplete="off" class="textbox">
                            <button id="toBasket_[{$iIndex}]" type="submit" class="submitButton largeButton">[{oxmultilang ident="TO_CART"}]</button>
                        [{/oxhasrights}]
                    [{else}]
                        <a class="submitButton largeButton" href="[{ $_productLink }]" >[{ oxmultilang ident="MORE_INFO" }]</a>
                    [{/if}]
                    [{if $removeFunction && (($owishid && ($owishid==$oxcmp_user->oxuser__oxid->value)) || (($wishid==$oxcmp_user->oxuser__oxid->value)) || $recommid) }]
                        <button triggerForm="remove_[{$removeFunction}][{$iIndex}]" type="submit" class="submitButton largeButton removeButton"><span>[{ oxmultilang ident="REMOVE" }]</span></button>
                    [{/if}]
                </div>
            [{/block}]
        </div>
    </form>
    [{if $removeFunction && (($owishid && ($owishid==$oxcmp_user->oxuser__oxid->value)) || (($wishid==$oxcmp_user->oxuser__oxid->value)) || $recommid) }]
        <form action="[{ $oViewConf->getSelfActionLink() }]" method="post" id="remove_[{$removeFunction}][{$iIndex}]">
            <div>
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cl" value="[{ $oViewConf->getTopActiveClassName() }]">
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
[{oxscript widget=$oView->getClassName()}]
