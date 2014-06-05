[{* basket contents *}]
[{oxscript include="js/widgets/oxbasketchecks.js" priority=10 }]
[{oxscript add="$('#checkAll, #basketRemoveAll').oxBasketChecks();"}]
[{assign var="currency" value=$oView->getActCurrency()}]
<form name="basket[{ $basketindex }]" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="basket">
    <input type="hidden" name="fnc" value="changebasket">
    <input type="hidden" name="CustomError" value='basket'>
    <table id="basket" class="basketitems[{if $oViewConf->getActiveClassName() == 'order' }] orderBasketItems[{/if}]">
        <colgroup>
            [{if $editable }]<col class="editCol">[{/if}]
            <col class="thumbCol">
            <col>
            [{if $oView->isWrapping() }]<col class="wrappingCol">[{/if}]
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
                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PRODUCT" }]</th>
                [{if $oView->isWrapping() }]
                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_WRAPPING" }]</th>
                [{/if}]
                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_QUANTITY" }]</th>
                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_UNITPRICE" }]</th>
                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TAX" }]</th>
                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TOTAL" }]</th>
            </tr>
        </thead>

        [{* basket items *}]
        <tbody>
        [{assign var="basketitemlist" value=$oView->getBasketArticles() }]
        [{assign var="oMarkGenerator" value=$oView->getBasketContentMarkGenerator() }]
        [{foreach key=basketindex from=$oxcmp_basket->getContents() item=basketitem name=basketContents}]
            [{block name="checkout_basketcontents_basketitem"}]
                [{assign var="oArticle" value=$basketitem->getArticle()}]
                [{assign var="basketproduct" value=$basketitemlist.$basketindex }]
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
                                <a rel="nofollow" href="[{$basketitem->getLink()}]">[{$basketitem->getTitle()}]</a>[{if $basketitem->isSkipDiscount() }] <sup><a rel="nofollow" href="#SkipDiscounts_link" >[{$oMarkGenerator->getMark('skippedDiscount')}]</a></sup>[{/if}]
                                [{if $oViewConf->getActiveClassName() == 'order' && $oViewConf->isFunctionalityEnabled('blEnableIntangibleProdAgreement')}]
                                    [{if $oArticle->hasDownloadableAgreement() }] <sup><a rel="nofollow" href="#noteForDownloadableArticles" >[{$oMarkGenerator->getMark('downloadable')}]</a></sup>[{/if}]
                                    [{if $oArticle->hasIntangibleAgreement() }] <sup><a rel="nofollow" href="#noteForIntangibleArticles" >[{$oMarkGenerator->getMark('intangible')}]</a></sup>[{/if}]
                                [{/if}]
                            </div>
                            <div>
                                [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_ARTNOMBER" }] [{ $basketproduct->oxarticles__oxartnum->value }]
                            </div>

                            [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]
                                [{if $oViewConf->showSelectListsInList()}]
                                    [{assign var="oSelections" value=$basketproduct->getSelections(null,$basketitem->getSelList())}]
                                    [{if $oSelections}]
                                        <div class="selectorsBox clear" id="cartItemSelections_[{$smarty.foreach.basketContents.iteration}]">
                                            [{foreach from=$oSelections item=oList name=selections}]
                                                [{include file="widget/product/selectbox.tpl" oSelectionList=$oList sFieldName="aproducts[`$basketindex`][sel]" iKey=$smarty.foreach.selections.index blHideDefault=true sSelType="seldrop"}]
                                            [{/foreach}]
                                        </div>
                                    [{/if}]
                                [{/if}]
                            [{/if }]

                            [{if !$editable }]
                                [{foreach key=sVar from=$basketitem->getPersParams() item=aParam }]
                                    <p class="persparamBox"><strong>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PERSPARAM" }]</strong> [{ $aParam }]</p>
                                [{/foreach}]
                            [{/if}]
                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_wrapping"}]
                        [{* product wrapping *}]
                        [{if $oView->isWrapping() }]
                        <td>
                                [{ if !$basketitem->getWrappingId() }]
                                    [{if $editable }]
                                        <a class="wrappingTrigger" rel="nofollow" href="#" title="[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_ADDWRAPPING" }]">[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_ADDWRAPPING" }]</a>
                                    [{else}]
                                        [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_NONE" }]
                                    [{/if}]
                                [{else}]
                                    [{assign var="oWrap" value=$basketitem->getWrapping() }]
                                    [{if $editable }]
                                        <a class="wrappingTrigger" rel="nofollow" href="#" title="[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_ADDWRAPPING" }]">[{$oWrap->oxwrapping__oxname->value}]</a>
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

                                    [{if $basketproduct->oxarticles__oxisconfigurable->value}]
                                        [{if $basketitem->getPersParams()}]
                                            [{foreach key=sVar from=$basketitem->getPersParams() item=aParam }]
                                                <p><strong>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PERSPARAM" }]</strong> <input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][[{ $sVar }]]" value="[{ $aParam }]"></p>
                                            [{/foreach }]
                                        [{ else }]
                                            <p><strong>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PERSPARAM" }]</strong> <input class="textbox persParam" type="text" name="aproducts[[{ $basketindex }]][persparam][details]" value=""></p>
                                        [{/if}]
                                    [{/if}]
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
                        <td>
                            [{if $basketitem->getFUnitPrice() }][{ $basketitem->getFUnitPrice() }]&nbsp;[{ $currency->sign}][{/if}]
                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_vat"}]
                        [{* product VAT percent *}]
                        <td>
                            [{ $basketitem->getVatPercent() }]%
                        </td>
                    [{/block}]

                    [{block name="checkout_basketcontents_basketitem_totalprice"}]
                        [{* product quantity * price *}]
                        <td>
                            [{ $basketitem->getFTotalPrice() }]&nbsp;[{ $currency->sign }]
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
        [{/foreach }]

         [{block name="checkout_basketcontents_giftwrapping"}]
             [{if $oViewConf->getShowGiftWrapping() }]
                  [{assign var="oCard" value=$oxcmp_basket->getCard() }]
                  [{ if $oCard }]
                    <tr>
                      [{if $editable }]<td></td>[{/if}]
                      <td></td>
                      <td id="orderCardTitle" colspan="3">[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_GREETINGCARD" }] "[{ $oCard->oxwrapping__oxname->value }]"
                          <br>
                          <b>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_YOURMESSAGE" }]</b>
                          <br>
                          <div id="orderCardText">[{ $oxcmp_basket->getCardMessage()|nl2br }]</div>
                      </td>
                      <td id="orderCardPrice">[{ $oCard->getFPrice() }]&nbsp;[{ $currency->sign }]</td>
                      <td>[{if $oxcmp_basket->getWrappCostVat() }][{ $oxcmp_basket->getWrappCostVatPercent() }]%[{/if}]</td>
                      <td id="orderCardTotalPrice" align="right">[{ $oCard->getFPrice() }]&nbsp;[{ $currency->sign }]</td>
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
                    <input type="checkbox" name="checkAll" id="checkAll" title="[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_SELECT_ALL" }]">
                    <button id="basketRemoveAll" name="removeAllBtn"><span>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_SELECT_ALL" }]</span></button>
                    <button id="basketRemove" type="submit" name="removeBtn"><span>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_REMOVE" }]</span></button>
                    <button id="basketUpdate" type="submit" name="updateBtn"><span>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_UPDATE" }]</span></button>
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
                                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TOTALNET" }]</th>
                                <td>[{ $oxcmp_basket->getProductsNetPrice() }]&nbsp;[{ $currency->sign }]</td>
                            </tr>
                        [{/block}]

                        [{block name="checkout_basketcontents_nodiscountproductvats"}]
                            [{foreach from=$oxcmp_basket->getProductVats() item=VATitem key=key }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TAX1" }]&nbsp;[{ $key }][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TAX2" }]</th>
                                    <td>[{ $VATitem }]&nbsp;[{ $currency->sign }]</td>
                                </tr>
                            [{/foreach }]
                        [{/block}]
                    [{/if }]

                    [{block name="checkout_basketcontents_totalgross"}]
                        <tr>
                            <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TOTALGROSS" }]</th>
                            <td>[{ $oxcmp_basket->getFProductsPrice() }]&nbsp;[{ $currency->sign }]</td>
                        </tr>
                    [{/block}]

                    [{if $oxcmp_basket->getDiscounts() }]
                        [{block name="checkout_basketcontents_discounts"}]
                            [{foreach from=$oxcmp_basket->getDiscounts() item=oDiscount name=test_Discounts}]
                                <tr>
                                    <th>
                                        <b>[{if $oDiscount->dDiscount < 0 }][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_CHARGE" }][{else}][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_DISCOUNT2" }][{/if}]&nbsp;</b>
                                        [{ $oDiscount->sDiscount }]
                                    </th>
                                    <td>
                                        [{if $oDiscount->dDiscount < 0 }][{ $oDiscount->fDiscount|replace:"-":"" }][{else}]-[{ $oDiscount->fDiscount }][{/if}]&nbsp;[{ $currency->sign }]
                                    </td>
                                </tr>
                            [{/foreach }]
                        [{/block}]

                        [{block name="checkout_basketcontents_totalnet"}]
                            <tr>
                                <th>
                                    [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TOTALNET" }]
                                </th>
                                <td>
                                    [{ $oxcmp_basket->getProductsNetPrice() }]&nbsp;[{ $currency->sign }]
                                </td>
                            </tr>
                        [{/block}]

                        [{block name="checkout_basketcontents_productvats"}]
                            [{foreach from=$oxcmp_basket->getProductVats() item=VATitem key=key }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TAX1" }] [{ $key }][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TAX2" }]</th>
                                    <td>[{ $VATitem }]&nbsp;[{ $currency->sign }]</td>
                                </tr>
                            [{/foreach }]
                        [{/block}]
                    [{/if }]

                    [{block name="checkout_basketcontents_voucherdiscount"}]
                        [{if $oViewConf->getShowVouchers() && $oxcmp_basket->getVoucherDiscValue() }]
                            [{foreach from=$oxcmp_basket->getVouchers() item=sVoucher key=key name=Voucher}]
                                <tr class="couponData">
                                    <th><span><strong>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_COUPON" }]</strong>&nbsp;([{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_NOMBER" }] [{ $sVoucher->sVoucherNr }])</span>
                                    [{if $editable }]
                                        <a href="[{ $oViewConf->getSelfLink() }]&amp;cl=basket&amp;fnc=removeVoucher&amp;voucherId=[{ $sVoucher->sVoucherId }]&amp;CustomError=basket" class="removeFn" rel="nofollow">[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_REMOVE2" }]</a>
                                    [{/if}]
                                    </th>
                                    <td>-<strong>[{ $sVoucher->fVoucherdiscount }]&nbsp;[{ $currency->sign }]</strong></td>
                                </tr>
                            [{/foreach }]
                        [{/if }]
                    [{/block}]

                    [{block name="checkout_basketcontents_delcosts"}]
                        [{if $oxcmp_basket->getDelCostNet() }]
                            <tr>
                                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_SHIPPINGNET" }]</th>
                                <td>[{ $oxcmp_basket->getDelCostNet() }]&nbsp;[{ $currency->sign }]</td>
                            </tr>
                            [{if $oxcmp_basket->getDelCostVat() }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PLUSTAX1" }] [{ $oxcmp_basket->getDelCostVatPercent() }][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PLUSTAX2" }]</th>
                                    <td>[{ $oxcmp_basket->getDelCostVat() }]&nbsp;[{ $currency->sign }]</td>
                                </tr>
                            [{/if }]
                        [{elseif $oxcmp_basket->getFDeliveryCosts() }]
                            <tr>
                                <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_SHIPPING" }]</th>
                                <td>[{ $oxcmp_basket->getFDeliveryCosts() }]&nbsp;[{ $currency->sign }]</td>
                            </tr>
                        [{/if }]
                    [{/block}]

                    [{block name="checkout_basketcontents_paymentcosts"}]
                        [{if $oxcmp_basket->getPayCostNet() }]
                            <tr>
                                <th>[{if $oxcmp_basket->getPaymentCosts() >= 0}][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PAYMENT" }][{else}][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_CHARGE2" }][{/if}] [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_DISCOUNT3" }]</th>
                                <td>[{ $oxcmp_basket->getPayCostNet() }]&nbsp;[{ $currency->sign }]</td>
                            </tr>
                            [{if $oxcmp_basket->getPayCostVat() }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PAYMENTTAX1" }] [{ $oxcmp_basket->getPayCostVatPercent() }] [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PAYMENTTAX2" }]</th>
                                    <td>[{ $oxcmp_basket->getPayCostVat() }]&nbsp;[{ $currency->sign }]</td>
                                </tr>
                            [{/if }]
                        [{elseif $oxcmp_basket->getFPaymentCosts() }]
                            <tr>
                                <th>[{if $oxcmp_basket->getPaymentCosts() >= 0}][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PAYMENT" }][{else}][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_CHARGE2" }][{/if}] [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_DISCOUNT3" }]</th>
                                <td>[{ $oxcmp_basket->getFPaymentCosts() }]&nbsp;[{ $currency->sign }]</td>
                            </tr>
                        [{/if }]
                    [{/block}]

                    [{block name="checkout_basketcontents_ts"}]
                        [{if $oxcmp_basket->getTsProtectionCosts()}]
                            [{ if $oxcmp_basket->getTsProtectionNet() }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TSPROTECTION" }]</th>
                                    <td>[{ $oxcmp_basket->getTsProtectionNet() }]&nbsp;[{ $currency->sign}]</td>
                                </tr>
                                [{ if $oxcmp_basket->getTsProtectionVat() }]
                                    <tr>
                                        <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TSPROTECTIONCHARGETAX1" }] [{ $oxcmp_basket->getTsProtectionVatPercent()}][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TSPROTECTIONCHARGETAX2" }]</th>
                                        <td>[{ $oxcmp_basket->getTsProtectionVat() }]&nbsp;[{ $currency->sign}]</td>
                                    </tr>
                                [{/if}]
                            [{ elseif $oxcmp_basket->getFTsProtectionCosts() }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_TSPROTECTION" }]</th>
                                    <td>[{ $oxcmp_basket->getFTsProtectionCosts() }]&nbsp;[{ $currency->sign}]</td>
                                </tr>
                            [{/if}]
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_wrappingcosts"}]
                        [{ if $oViewConf->getShowGiftWrapping() }]
                            [{if $oxcmp_basket->getWrappCostNet() }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_WRAPPINGNET" }]</th>
                                    <td>[{ $oxcmp_basket->getWrappCostNet() }] [{ $currency->sign}]</td>
                                </tr>
                            [{elseif $oxcmp_basket->getFWrappingCosts() }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_WRAPPINGGROSS1" }]</th>
                                    <td>[{ $oxcmp_basket->getFWrappingCosts() }] [{ $currency->sign}]</td>
                                </tr>
                            [{/if}]
                            [{if $oxcmp_basket->getWrappCostVat() }]
                                <tr>
                                    <th>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_WRAPPINGTAX1" }] [{ $oxcmp_basket->getWrappCostVatPercent() }][{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_WRAPPINGTAX2" }]</th>
                                    <td>[{ $oxcmp_basket->getWrappCostVat() }] [{ $currency->sign}]</td>
                                </tr>
                            [{/if}]
                        [{/if}]
                    [{/block}]

                    [{block name="checkout_basketcontents_grandtotal"}]
                        <tr>
                            <th><strong>[{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_GRANDTOTAL" }]</strong></th>
                            <td><strong>[{ $oxcmp_basket->getFPrice() }]&nbsp;[{ $currency->sign }]</strong></td>
                        </tr>
                    [{/block}]

                    [{if $oxcmp_basket->hasSkipedDiscount() }]
                        <tr>
                            <th><span class="note">[{$oMarkGenerator->getMark('skippedDiscount')}]</span> [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_DISCOUNTS_NOT_APPLIED_FOR_ARTICLES" }]</span></th>
                            <td></td>
                        </tr>
                    [{/if}]
                </table>
            </div>
        [{/block}]
    </div>
 </form>