[{* basket contents *}]
[{oxscript include="js/widgets/oxbasketchecks.js" priority=10 }]
[{oxscript add="$('#checkAll, #basketRemoveAll').oxBasketChecks();"}]
[{assign var="currency" value=$oView->getActCurrency()}]
<form name="basket[{$basketindex}]" action="[{$oViewConf->getSelfActionLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="basket">
    <input type="hidden" name="fnc" value="changebasket">
    <input type="hidden" name="CustomError" value='basket'>
    <table id="basket" class="basketitems[{if $oViewConf->getActiveClassName() == 'order'}] orderBasketItems[{/if}]">
        <colgroup>
            [{if $editable}]<col class="editCol">[{/if}]
            <col class="thumbCol">
            <col>
            [{if $oView->isWrapping()}]<col class="wrappingCol">[{/if}]
            <col class="coutCol">
            <col class="priceCol">
            <col class="vatCol">
            <col class="totalCol">
        </colgroup>
        [{* basket header *}]
        <thead>
            <tr>
                [{if $editable }]<th></th>[{/if}]
                <th></th>
                <th>[{ oxmultilang ident="PRODUCT" }]</th>
                [{if $oView->isWrapping() }]
                <th>[{ oxmultilang ident="WRAPPING" }]</th>
                [{/if}]
                <th>[{ oxmultilang ident="QUANTITY" }]</th>
                <th class="unitPrice">[{ oxmultilang ident="UNIT_PRICE" }]</th>
                <th class="vatPercent">[{ oxmultilang ident="VAT" }]</th>
                <th>[{ oxmultilang ident="TOTAL" }]</th>
            </tr>
        </thead>

        [{* basket items *}]
        <tbody>
        [{assign var="basketitemlist" value=$oView->getBasketArticles() }]
        [{foreach key=basketindex from=$oxcmp_basket->getContents() item=basketitem name=basketContents}]
            [{block name="checkout_basketcontents_basketitem"}]
                [{assign var="basketproduct" value=$basketitemlist.$basketindex }]
                [{assign var="oArticle" value=$basketitem->getArticle()}]
                [{assign var="oAttributes" value=$oArticle->getAttributesDisplayableInBasket()}]

                <tr id="cartItem_[{$smarty.foreach.basketContents.iteration}]">

                    [{block name="checkout_basketcontents_basketitem_removecheckbox"}]
                        [{if $editable }]
                            <td class="checkbox">
                                <input type="checkbox" name="aproducts[[{ $basketindex }]][remove]" value="1">
                            </td>
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_image"}]
                        [{* product image *}]
                        <td class="basketImage">
                            <a href="[{$basketitem->getLink()}]" rel="nofollow">
                                <img src="[{$basketitem->getIconUrl()}]" alt="[{$basketitem->getTitle()|strip_tags}]">
                            </a>
                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_titlenumber"}]
                        [{* product title & number *}]
                        <td>
                            <div>
                                <a rel="nofollow" href="[{$basketitem->getLink()}]"><b>[{$basketitem->getTitle()}]</b></a>[{if $basketitem->isSkipDiscount() }] <sup><a rel="nofollow" href="#SkipDiscounts_link" >**</a></sup>[{/if}]
                            </div>
                            <div class="smallFont">
                                [{ oxmultilang ident="PRODUCT_NO" suffix="COLON" }] [{ $basketproduct->oxarticles__oxartnum->value }]
                            </div>
                            <div class="smallFont">
                                [{assign var=sep value=", "}]
                                [{assign var=result value=""}]
                                [{foreach key=oArtAttributes from=$oAttributes->getArray() item=oAttr name=attributeContents}]
                                    [{assign var=temp value=$oAttr->oxattribute__oxvalue->value}]
                                    [{assign var=result value=$result$temp$sep}]
                                [{/foreach}]
                                <b>[{$result|trim:$sep}]</b>
                            </div>

                            [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]
                                [{assign var="oSelections" value=$basketproduct->getSelections(null,$basketitem->getSelList())}]
                                [{if $oSelections}]
                                    <div class="selectorsBox clear" id="cartItemSelections_[{$smarty.foreach.basketContents.iteration}]">
                                    [{foreach from=$oSelections item=oList name=selections}]
                                        [{if $oViewConf->showSelectListsInList()}]
                                            [{include file="widget/product/selectbox.tpl" oSelectionList=$oList sFieldName="aproducts[`$basketindex`][sel]" iKey=$smarty.foreach.selections.index blHideDefault=true sSelType="seldrop"}]
                                        [{else}]
                                            [{assign var="oActiveSelection" value=$oList->getActiveSelection()}]
                                            [{if $oActiveSelection}]
                                                <input type="hidden" name="aproducts[[{$basketindex}]][sel][[{$smarty.foreach.selections.index}]]" value="[{if $oActiveSelection }][{$oActiveSelection->getValue()}][{/if}]">
                                                <div>[{$oList->getLabel()}]: [{$oActiveSelection->getName()}]</div>
                                            [{/if}]
                                        [{/if}]
                                    [{/foreach}]
                                    </div>
                                [{/if}]
                            [{/if}]

                            [{if !$editable }]
                                <p class="persparamBox">
                                    [{foreach key=sVar from=$basketitem->getPersParams() item=aParam name=persparams }]
                                        [{if !$smarty.foreach.persparams.first}]<br />[{/if}]
                                        <strong>
                                            [{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
                                                [{ oxmultilang ident="LABEL" suffix="COLON" }]
                                            [{else}]
                                                [{ $sVar }] :
                                            [{/if}]
                                        </strong> [{ $aParam }]
                                    [{/foreach}]
                                </p>
                            [{else}]
                                [{if $basketproduct->oxarticles__oxisconfigurable->value}]
                                    [{if $basketitem->getPersParams()}]
                                        <br />
                                        [{foreach key=sVar from=$basketitem->getPersParams() item=aParam name=persparams }]
                                            <p>
                                                <label class="persParamLabel">
                                                    [{if $smarty.foreach.persparams.first && $smarty.foreach.persparams.last}]
                                                        [{ oxmultilang ident="LABEL" suffix="COLON" }]
                                                    [{else}]
                                                        [{ $sVar }]:
                                                    [{/if}]
                                                </label>
                                                <input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][[{ $sVar }]]" value="[{ $aParam }]">
                                            </p>
                                        [{/foreach}]
                                    [{else}]
                                         <p>[{ oxmultilang ident="LABEL" suffix="COLON" }] <input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][details]" value=""></p>
                                    [{/if}]
                                [{/if}]
                            [{/if}]

                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_wrapping"}]
                        [{* product wrapping *}]
                        [{if $oView->isWrapping() }]
                        <td>
                                [{if !$basketitem->getWrappingId() }]
                                    [{if $editable }]
                                        <a class="wrappingTrigger" rel="nofollow" href="#" title="[{oxmultilang ident="ADD"}]">[{ oxmultilang ident="ADD" }]</a>
                                    [{else}]
                                        [{ oxmultilang ident="NONE" }]
                                    [{/if}]
                                [{else}]
                                    [{assign var="oWrap" value=$basketitem->getWrapping() }]
                                    [{if $editable }]
                                        <a class="wrappingTrigger" rel="nofollow" href="#" title="[{oxmultilang ident="ADD"}]">[{$oWrap->oxwrapping__oxname->value}]</a>
                                    [{else}]
                                        [{$oWrap->oxwrapping__oxname->value}]
                                    [{/if}]
                                [{/if}]
                        </td>
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_quantity"}]
                        [{* product quantity manager *}]
                        <td class="quantity">
                            [{if $editable }]
                                <input type="hidden" name="aproducts[[{ $basketindex }]][aid]" value="[{ $basketitem->getProductId() }]">
                                <input type="hidden" name="aproducts[[{ $basketindex }]][basketitemid]" value="[{ $basketindex }]">
                                <input type="hidden" name="aproducts[[{ $basketindex }]][override]" value="1">
                                [{if $basketitem->isBundle() }]
                                    <input type="hidden" name="aproducts[[{ $basketindex }]][bundle]" value="1">
                                [{/if}]

                                [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]
                                    <p>
                                        <input id="am_[{$smarty.foreach.basketContents.iteration}]" type="text" class="textbox" name="aproducts[[{ $basketindex }]][am]" value="[{ $basketitem->getAmount() }]" size="2">
                                    </p>
                                [{/if}]
                            [{else}]
                                [{ $basketitem->getAmount() }]
                            [{/if}]
                            [{if $basketitem->getdBundledAmount() > 0 && ($basketitem->isBundle() || $basketitem->isDiscountArticle()) }]
                                +[{ $basketitem->getdBundledAmount() }]
                            [{/if}]
                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_unitprice"}]
                        [{* product price *}]
                        <td class="unitPrice">
                            [{if $basketitem->getUnitPrice() }] [{oxprice price=$basketitem->getUnitPrice() currency=$currency }] [{/if}]
                            [{if !$basketitem->isBundle() }]
                                [{assign var=dRegUnitPrice value=$basketitem->getRegularUnitPrice()}]
                                [{assign var=dUnitPrice value=$basketitem->getUnitPrice()}]
                                [{if $dRegUnitPrice->getPrice() > $dUnitPrice->getPrice() }]
                                <br><s>[{oxprice price=$basketitem->getRegularUnitPrice() currency=$currency }]</s>
                                [{/if}]
                            [{/if}]
                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_vat"}]
                        [{* product VAT percent *}]
                        <td class="vatPercent">
                            [{ $basketitem->getVatPercent() }]%
                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_totalprice"}]
                        [{* product quantity * price *}]
                        <td>
                            [{oxprice price=$basketitem->getPrice() currency=$currency}]
                        </td>
                    [{/block}]
                </tr>
            [{/block}]

            [{* packing unit *}]

            [{block name="checkout_basketcontents_itemerror"}]
                [{foreach from=$Errors.basket item=oEr key=key }]
                    [{if $oEr->getErrorClassType() == 'oxOutOfStockException'}]
                        [{* display only the exceptions for the current article *}]
                        [{if $basketindex == $oEr->getValue('basketIndex') }]
                            <tr class="basketError">
                                [{if $editable }]<td></td>[{/if}]
                                    <td colspan="5">
                                        <span class="inlineError">[{ $oEr->getOxMessage() }] <strong>[{ $oEr->getValue('remainingAmount') }]</strong></span>
                                    </td>
                                [{if $oView->isWrapping() }]<td></td>[{/if}]
                                <td></td>
                            </tr>
                        [{/if}]
                    [{/if}]
                    [{if $oEr->getErrorClassType() == 'oxArticleInputException'}]
                        [{if $basketitem->getProductId() == $oEr->getValue('productId') }]
                            <tr class="basketError">
                                [{if $editable }]<td></td>[{/if}]
                                <td colspan="5">
                                    <span class="inlineError">[{ $oEr->getOxMessage() }]</span>
                                </td>
                                [{if $oView->isWrapping() }]<td></td>[{/if}]
                                <td></td>
                            </tr>
                        [{/if}]
                    [{/if}]
                [{/foreach}]
            [{/block}]
        [{*  basket items end  *}]
        [{/foreach}]

         [{block name="checkout_basketcontents_giftwrapping"}]
             [{if $oViewConf->getShowGiftWrapping() }]
                  [{assign var="oCard" value=$oxcmp_basket->getCard() }]
                  [{if $oCard }]
                    <tr>
                      [{if $editable }]<td></td>[{/if}]
                      <td></td>
                      <td id="orderCardTitle" colspan="3">[{ oxmultilang ident="GREETING_CARD" }] "[{ $oCard->oxwrapping__oxname->value }]"
                          <br>
                          <b>[{ oxmultilang ident="YOUR_MESSAGE" suffix="COLON" }]</b>
                          <br>
                          <div id="orderCardText">[{ $oxcmp_basket->getCardMessage()|nl2br }]</div>
                      </td>
                      <td id="orderCardPrice">[{oxprice price=$oCard->getPrice() currency=$currency }]</td>
                      <td>
                         [{if $oxcmp_basket->isProportionalCalculationOn() }]
                            [{oxmultilang ident="PROPORTIONALLY_CALCULATED"}]</th>
                         [{else}]
                              [{if $oxcmp_basket->getGiftCardCostVat() }][{ $oxcmp_basket->getGiftCardCostVatPercent() }]%[{/if}]
                         [{/if}]
                      </td>
                      <td id="orderCardTotalPrice" align="right">[{oxprice price=$oCard->getPrice() currency=$currency}]</td>
                    </tr>
                  [{/if}]
              [{/if}]
          [{/block}]
        </tbody>
    </table>

    <div class="clear">

        [{block name="checkout_basketcontents_basketfunctions"}]
            [{if $editable }]
                <div id="basketFn" class="basketFunctions">
                    [{*  basket update/delete buttons  *}]
                    <input type="checkbox" name="checkAll" id="checkAll" title="[{ oxmultilang ident="ALL" }]">
                    <button id="basketRemoveAll" name="removeAllBtn"><span>[{ oxmultilang ident="ALL" }]</span></button>
                    <button id="basketRemove" type="submit" name="removeBtn"><span>[{ oxmultilang ident="REMOVE" }]</span></button>
                    <button id="basketUpdate" type="submit" name="updateBtn"><span>[{ oxmultilang ident="UPDATE" }]</span></button>
                </div>
            [{/if}]
        [{/block}]

        [{block name="checkout_basketcontents_summary"}]
            <div id="basketSummary" class="summary[{if $oViewConf->getActiveClassName() == 'order' }] orderSummary[{/if}]">
                [{*  basket summary  *}]
                <table>
                    [{if !$oxcmp_basket->getDiscounts() }]
                        [{block name="checkout_basketcontents_nodiscounttotalnet"}]
                            <tr>
                                <th>[{ oxmultilang ident="TOTAL_NET" suffix="COLON" }]</th>
                                <td id="basketTotalProductsNetto">[{oxprice price=$oxcmp_basket->getNettoSum() currency=$currency}]</td>
                            </tr>
                        [{/block}]

                        [{block name="checkout_basketcontents_nodiscountproductvats"}]
                            [{foreach from=$oxcmp_basket->getProductVats(false) item=VATitem key=key }]
                                <tr>
                                    <th>[{ oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$key }]</th>
                                    <td>[{oxprice price=$VATitem currency=$currency }]</td>
                                </tr>
                            [{/foreach}]
                        [{/block}]

                        [{block name="checkout_basketcontents_nodiscounttotalgross"}]
                            <tr>
                                <th>[{ oxmultilang ident="TOTAL_GROSS" suffix="COLON" }]</th>
                                <td id="basketTotalProductsGross">[{oxprice price=$oxcmp_basket->getBruttoSum() currency=$currency}]</td>
                            </tr>
                        [{/block}]
                    [{else}]
                        [{if $oxcmp_basket->isPriceViewModeNetto() }]
                            [{block name="checkout_basketcontents_discounttotalnet"}]
                                <tr>
                                    <th>[{ oxmultilang ident="TOTAL_NET" suffix="COLON" }]</th>
                                    <td id="basketTotalProductsNetto">[{oxprice price=$oxcmp_basket->getNettoSum() currency=$currency }]</td>
                                </tr>
                            [{/block}]
                        [{else}]
                             [{block name="checkout_basketcontents_discounttotalgross"}]
                                <tr>
                                    <th>[{ oxmultilang ident="TOTAL_GROSS" suffix="COLON" }]</th>
                                    <td id="basketTotalProductsGross">[{oxprice price=$oxcmp_basket->getBruttoSum() currency=$currency}]</td>
                                </tr>
                            [{/block}]
                        [{/if}]

                        [{block name="checkout_basketcontents_discounts"}]
                            [{foreach from=$oxcmp_basket->getDiscounts() item=oDiscount name=test_Discounts}]
                                <tr>
                                    <th>
                                        <b>[{if $oDiscount->dDiscount < 0 }][{ oxmultilang ident="SURCHARGE" }][{else}][{ oxmultilang ident="DISCOUNT" }][{/if}]&nbsp;</b>[{ $oDiscount->sDiscount }]
                                    </th>
                                    <td>[{oxprice price=$oDiscount->dDiscount*-1 currency=$currency}]</td>
                                </tr>
                            [{/foreach}]
                        [{/block}]

                        [{if !$oxcmp_basket->isPriceViewModeNetto() }]
                            [{block name="checkout_basketcontents_totalnet"}]
                                <tr>
                                    <th>[{ oxmultilang ident="TOTAL_NET" suffix="COLON" }]</th>
                                    <td id="basketTotalNetto">[{oxprice price=$oxcmp_basket->getNettoSum() currency=$currency }]</td>
                                </tr>
                            [{/block}]
                        [{/if}]

                        [{block name="checkout_basketcontents_productvats"}]
                            [{foreach from=$oxcmp_basket->getProductVats(false) item=VATitem key=key }]
                                <tr>
                                    <th>[{ oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$key }]</th>
                                    <td>[{oxprice price=$VATitem currency=$currency }]</td>
                                </tr>
                            [{/foreach}]
                        [{/block}]

                        [{if $oxcmp_basket->isPriceViewModeNetto() }]
                            [{block name="checkout_basketcontents_totalgross"}]
                                <tr>
                                    <th>[{ oxmultilang ident="TOTAL_GROSS" suffix="COLON" }]</th>
                                    <td id="basketTotalGross">[{oxprice price=$oxcmp_basket->getBruttoSum() currency=$currency }]</td>
                                </tr>
                            [{/block}]
                        [{/if}]
                    [{/if}]

                    [{block name="checkout_basketcontents_voucherdiscount"}]
                        [{if $oViewConf->getShowVouchers() && $oxcmp_basket->getVoucherDiscValue() }]
                            [{foreach from=$oxcmp_basket->getVouchers() item=sVoucher key=key name=Voucher}]
                                <tr class="couponData">
                                    <th><span><strong>[{ oxmultilang ident="COUPON" }]</strong>&nbsp;([{ oxmultilang ident="NUMBER_2" }] [{ $sVoucher->sVoucherNr }])</span>
                                    [{if $editable }]
                                        <a href="[{$oViewConf->getSelfLink() }]&amp;cl=basket&amp;fnc=removeVoucher&amp;voucherId=[{ $sVoucher->sVoucherId }]&amp;CustomError=basket" class="removeFn" rel="nofollow">[{ oxmultilang ident="REMOVE" }]</a>
                                    [{/if}]
                                    </th>
                                    <td><strong>[{oxprice price=$sVoucher->dVoucherdiscount*-1 currency=$currency}]</strong></td>
                                </tr>
                            [{/foreach}]
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_delcosts"}]
                        [{assign var="deliveryCost" value=$oxcmp_basket->getDeliveryCost()}]
                        [{if $deliveryCost && ($oxcmp_basket->getBasketUser() || $oViewConf->isFunctionalityEnabled('blCalculateDelCostIfNotLoggedIn') ) }]
                            [{if $oViewConf->isFunctionalityEnabled('blShowVATForDelivery') }]
                                <tr>
                                    <th>[{ oxmultilang ident="SHIPPING_NET" suffix="COLON" }]</th>
                                    <td id="basketDeliveryNetto">[{oxprice price=$deliveryCost->getNettoPrice() currency=$currency }]</td>
                                </tr>
                                [{if $deliveryCost->getVatValue()}]
                                    <tr>
                                        [{if $oxcmp_basket->isProportionalCalculationOn() }]
                                            <th>[{oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON"}]</th>
                                        [{else}]
                                            <th>[{oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$deliveryCost->getVat()}]
                                        [{/if}]
                                        <td id="basketDeliveryVat">[{oxprice price=$deliveryCost->getVatValue() currency=$currency}]</td>
                                    </tr>
                                [{/if}]
                            [{else}]
                                <tr>
                                    <th>[{ oxmultilang ident="SHIPPING_COST" }]</th>
                                    <td id="basketDeliveryGross">[{oxprice price=$deliveryCost->getBruttoPrice() currency=$currency}]</td>
                                </tr>
                            [{/if}]
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_paymentcosts"}]
                        [{assign var="paymentCost" value=$oxcmp_basket->getPaymentCost()}]
                        [{if $paymentCost && $paymentCost->getPrice() }]
                            [{if $oViewConf->isFunctionalityEnabled('blShowVATForPayCharge') }]
                                <tr>
                                    <th>[{if $paymentCost->getPrice() >= 0}][{ oxmultilang ident="SURCHARGE" }][{else}][{ oxmultilang ident="DEDUCTION" }][{/if}] [{ oxmultilang ident="PAYMENT_METHOD" }]</th>
                                    <td id="basketPaymentNetto">[{oxprice price=$paymentCost->getNettoPrice() currency=$currency }]</td>
                                </tr>
                                [{if $paymentCost->getVatValue()}]
                                    <tr>
                                        [{if $oxcmp_basket->isProportionalCalculationOn() }]
                                            <th>[{ oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON" }]</th>
                                        [{else}]
                                            <th>[{ oxmultilang ident="SURCHARGE_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$paymentCost->getVat() }]</th>
                                        [{/if}]
                                        <td id="basketPaymentVat">[{oxprice price=$paymentCost->getVatValue() currency=$currency }]</td>
                                    </tr>
                                [{/if}]
                            [{else}]
                                <tr>
                                    <th>[{if $paymentCost->getPrice() >= 0}][{ oxmultilang ident="SURCHARGE" }][{else}][{ oxmultilang ident="DEDUCTION" }][{/if}] [{ oxmultilang ident="PAYMENT_METHOD" }]</th>
                                    <td id="basketPaymentGross">[{oxprice price=$paymentCost->getBruttoPrice() currency=$currency }]</td>
                                </tr>
                            [{/if}]
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_ts"}]
                        [{assign var="trustedShopProtectionCost" value=$oxcmp_basket->getTrustedShopProtectionCost()}]
                        [{if $trustedShopProtectionCost && $trustedShopProtectionCost->getPrice() > 0 }]
                            [{if $oViewConf->isFunctionalityEnabled('blShowVATForPayCharge') }]
                                <tr>
                                    <th>[{ oxmultilang ident="TRUSTED_SHOP_BUYER_PROTECTION" }]</th>
                                    <td id="basketTSNetto">[{oxprice price=$trustedShopProtectionCost->getNettoPrice() currency=$currency}]</td>
                                </tr>
                                [{if $trustedShopProtectionCost->getVatValue()}]
                                    <tr>
                                        [{if $oxcmp_basket->isProportionalCalculationOn() }]
                                            <th>[{ oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON" }]</th>
                                        [{else}]
                                            <th>[{ oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$trustedShopProtectionCost->getVat() }]</th>
                                        [{/if}]
                                        <td id="basketTSVat">[{oxprice price=$trustedShopProtectionCost->getVatValue() currency=$currency}]</td>
                                    </tr>
                                [{/if}]
                            [{else}]
                                <tr>
                                    <th>[{ oxmultilang ident="TRUSTED_SHOP_BUYER_PROTECTION" }]</th>
                                    <td id="basketTSGross">[{oxprice price=$trustedShopProtectionCost->getBruttoPrice() currency=$currency}]</td>
                                </tr>
                            [{/if}]
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_wrappingcosts"}]
                        [{if $oViewConf->getShowGiftWrapping() }]

                            [{assign var="wrappingCost" value=$oxcmp_basket->getWrappingCost()}]
                            [{if $wrappingCost && $wrappingCost->getPrice() > 0 }]
                                [{if $oViewConf->isFunctionalityEnabled('blShowVATForWrapping') }]
                                    <tr>
                                        <th>[{ oxmultilang ident="BASKET_TOTAL_WRAPPING_COSTS_NET" suffix="COLON" }]</th>
                                        <td id="basketWrappingNetto">[{oxprice price=$wrappingCost->getNettoPrice() currency=$currency}]</td>
                                    </tr>
                                    [{if $oxcmp_basket->getWrappCostVat() }]
                                    <tr>
                                        <th>[{ oxmultilang ident="PLUS_VAT" suffix="COLON" }]</th>
                                        <td id="basketWrappingVat">[{oxprice price=$wrappingCost->getVatValue() currency=$currency}]</td>
                                    </tr>
                                    [{/if}]
                                [{else}]
                                    <tr>
                                        <th>[{ oxmultilang ident="GIFT_WRAPPING" suffix="COLON" }]</th>
                                        <td id="basketWrappingGross">[{oxprice price=$wrappingCost->getBruttoPrice() currency=$currency }]</td>
                                    </tr>
                                [{/if}]
                            [{/if}]

                            [{assign var="giftCardCost" value=$oxcmp_basket->getGiftCardCost()}]
                            [{if $giftCardCost && $giftCardCost->getPrice() > 0 }]
                                [{if $oViewConf->isFunctionalityEnabled('blShowVATForWrapping') }]
                                    <tr>
                                        <th>[{ oxmultilang ident="BASKET_TOTAL_GIFTCARD_COSTS_NET" suffix="COLON" }]</th>
                                        <td id="basketGiftCardNetto">[{oxprice price=$giftCardCost->getNettoPrice() currency=$currency }]</td>
                                    </tr>
                                    <tr>
                                        [{if $oxcmp_basket->isProportionalCalculationOn() }]
                                            <th>[{ oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON" }]</th>
                                        [{else}]
                                            <th>[{ oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$giftCardCost->getVat() }] </th>
                                        [{/if}]
                                        <td id="basketGiftCardVat">[{oxprice price=$giftCardCost->getVatValue() currency=$currency}]</td>
                                    </tr>
                                [{else}]
                                    <tr>
                                        <th>[{ oxmultilang ident="GREETING_CARD" suffix="COLON" }]</th>
                                        <td id="basketGiftCardGross">[{oxprice price=$giftCardCost->getBruttoPrice() currency=$currency}]</td>
                                    </tr>
                                [{/if}]
                            [{/if}]
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_grandtotal"}]
                        <tr>
                            <th><strong>[{ oxmultilang ident="GRAND_TOTAL" suffix="COLON" }]</strong></th>
                            <td id="basketGrandTotal"><strong>[{oxprice price=$oxcmp_basket->getPrice() currency=$currency }]</strong></td>
                        </tr>
                    [{/block}]

                    [{if $oxcmp_basket->hasSkipedDiscount() }]
                        <tr>
                            <th><span class="note">**</span> [{ oxmultilang ident="MESSAGE_COUPON_NOT_APPLIED_FOR_ARTICLES" }]</span></th>
                            <td></td>
                        </tr>
                    [{/if}]
                </table>
            </div>
        [{/block}]
    </div>
 </form>