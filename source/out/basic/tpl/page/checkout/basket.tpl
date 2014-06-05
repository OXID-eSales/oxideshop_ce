[{assign var="template_title" value="BASKET_BASKET"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location=$template_title}]
<!-- ordering steps -->
[{include file="inc/steps_item.tpl" highlight=1 }]
[{assign var="currency" value=$oView->getActCurrency() }]
[{if !$oxcmp_basket->getProductsCount()  }]
  <div class="msg">[{ oxmultilang ident="BASKET_EMPTYSHIPPINGCART" }]</div>
[{else }]
  <div class="bar prevnext order">
    [{if $oView->showBackToShop()}]
    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="basket">
          <input type="hidden" name="fnc" value="backtoshop">
          <div class="left arrowdown">
              <input type="submit" value="[{ oxmultilang ident="BASKET_CONTINUESHOPPING" }]">
          </div>
      </div>
    </form>
    [{/if}]

    [{if $oView->isLowOrderPrice() }]
      <div class="minorderprice">[{ oxmultilang ident="BASKET_MINORDERPRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
    [{else}]
    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="user">
          <div class="right arrowright">
              <input id="test_BasketNextStepTop" type="submit" value="[{ oxmultilang ident="BASKET_NEXTSTEP" }]">
          </div>
      </div>
    </form>
    [{/if}]
  </div>

<!-- basket contents -->

 <form name="basket[{ $basketindex }]" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
    <div>
         [{ $oViewConf->getHiddenSid() }]
         <input type="hidden" name="cl" value="basket">
         <input type="hidden" name="fnc" value="changebasket">
         <input type="hidden" name="CustomError" value='basket'>
    </div>

  <table class="basket">
    <colgroup>
        <col width="19">
        <col width="75">
        <col width="166">
        <col width="60">
        <col width="94">
        <col width="61">
        <col width="78">
        <col width="7">
    </colgroup>

    <!-- basket header -->
    <thead>
      <tr>
          <th class="brd"><div class="brd_line">&nbsp;</div></th>
          <th>[{ oxmultilang ident="BASKET_ARTICEL" }]</th>
          <th></th>
          <th>[{ oxmultilang ident="BASKET_QUANTITY" }]</th>
          <th class="ta_right">[{ oxmultilang ident="BASKET_UNITPRICE" }]</th>
          <th class="ta_right">[{ oxmultilang ident="BASKET_TAX" }]&nbsp;&nbsp;</th>
          <th class="ta_right">[{ oxmultilang ident="BASKET_TOTAL" }]</th>
          <th class="lastcol"></th>
      </tr>
    </thead>

    <!-- basket items -->
    <tbody>
    [{assign var="basketitemlist" value=$oView->getBasketArticles() }]
    [{foreach key=basketindex from=$oxcmp_basket->getContents() item=basketitem name=test_Contents}]
    [{assign var="basketproduct" value=$basketitemlist.$basketindex }]
    <tr valign="top">
      <!-- product image -->
      <td class="brd">
          <input id="test_removeCheck_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" type="checkbox" name="aproducts[[{ $basketindex }]][remove]" value="1">
      </td>
      <td>
          <a class="picture" id="test_basketPic_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" href="[{$basketitem->getLink()}]" rel="nofollow">
             <img src="[{$basketitem->getIconUrl()}]" alt="[{$basketitem->getTitle()|strip_tags}]">
          </a>
      </td>

      <!-- product title & number -->
      <td>
        <div class="art_title">
            <a id="test_basketTitle_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" rel="nofollow" href="[{$basketitem->getLink()}]">[{$basketitem->getTitle()}]</a>
            [{if $basketitem->isSkipDiscount() }]
                <sup><a rel="nofollow" href="#SkipDiscounts_link" class="note">**</a></sup>
            [{/if}]
        </div>
        <div class="art_num" id="test_basketNo_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]">[{ oxmultilang ident="BASKET_ARTNOMBER" }] [{ $basketproduct->oxarticles__oxartnum->value }]</div>
        [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]

           [{if $basketproduct->getDispSelList() }]
             <div class="variants">
             [{foreach key=iSel from=$basketproduct->getDispSelList() item=oList }]
               <select id="test_basketSelect_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]_[{ $iSel }]" name="aproducts[[{ $basketindex }]][sel][[{ $iSel }]]">
                 [{foreach key=iSelIdx from=$oList item=oSelItem }]
                 [{if $oSelItem->name }]
                   <option value="[{ $iSelIdx }]"[{if $oSelItem->selected }]SELECTED[{/if }]>[{ $oSelItem->name }]</option>
                 [{/if }]
                 [{/foreach }]
               </select>
             [{/foreach }]
             </div>
           [{/if}]

          [{/if }]
      </td>

      <!-- product quantity manager -->
      <td align="right">

         <input type="hidden" name="aproducts[[{ $basketindex }]][aid]" value="[{ $basketitem->getProductId() }]">
         <input type="hidden" name="aproducts[[{ $basketindex }]][basketitemid]" value="[{ $basketindex }]">
         <input type="hidden" name="aproducts[[{ $basketindex }]][override]" value="1">
         [{if $basketitem->isBundle() }]
             <input type="hidden" name="aproducts[[{ $basketindex }]][bundle]" value="1">
         [{/if}]
         [{if $basketitem->getdBundledAmount() > 0 && ($basketitem->isBundle() || $basketitem->isDiscountArticle()) }]
             <div id="test_basketAmount_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" align="center">+[{ $basketitem->getdBundledAmount() }]</div>
         [{/if}]

         [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]
             [{foreach key=sVar from=$basketitem->getPersParams() item=aParam }]
               <b>[{ oxmultilang ident="BASKET_DETAILS" }]:</b>&nbsp;<input id="persparamInput_[{$basketitem->getProductId()}]_[{$sVar}]" type="text" name="aproducts[[{ $basketindex }]][persparam][[{ $sVar }]]" value="[{ $aParam }]"><br>
             [{/foreach }]
             <input id="test_basketAm_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" type="text" name="aproducts[[{ $basketindex }]][am]" value="[{ $basketitem->getAmount() }]" size="2">
        [{/if}]
      </td>

      <!-- product price -->
      <td id="test_basket_Price_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" class="price">
        [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]
          [{if $basketitem->getFUnitPrice() }][{ $basketitem->getFUnitPrice() }]&nbsp;[{ $currency->sign}][{/if}]
        [{/if}]
      </td>

      <!-- product VAT percent -->
      <td id="test_basket_Vat_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" class="vat">
        [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]
          [{ $basketitem->getVatPercent() }]%
        [{/if}]
      </td>

      <!-- product quantity * price -->
      <td id="test_basket_TotalPrice_[{$basketitem->getProductId()}]_[{$smarty.foreach.test_Contents.iteration}]" class="totalprice">
         [{if !$basketitem->isBundle() || !$basketitem->isDiscountArticle()}]
           [{ $basketitem->getFTotalPrice() }]&nbsp;[{ $currency->sign }]
         [{/if}]
      </td>
      <td></td>
    </tr>


        [{foreach from=$Errors.basket item=oEr key=key }]
        [{if $oEr->getErrorClassType() == 'oxOutOfStockException'}]
        <!-- display only the exceptions for the current article-->
           [{if $basketindex == $oEr->getValue('basketIndex') }]
               <tr>
                 <td class="brd"></td>
                 <td id="test_basket_StockError_[{$basketitem->getProductId()}]_[{$key}]" colspan="6">
                     <span class="err">[{ $oEr->getOxMessage() }] [{ $oEr->getValue('remainingAmount') }]</span>
                 </td>
                 <td></td>
               </tr>
            [{/if}]
         [{/if}]
         [{if $oEr->getErrorClassType() == 'oxArticleInputException'}]
            [{if $basketitem->getProductId() == $oEr->getValue('productId') }]
                <tr class="notice">
                 <td></td>
                 <td colspan="6">
                     [{ $oEr->getOxMessage() }]
                 </td>
                 <td></td>
               </tr>
            [{/if}]
         [{/if}]
         [{/foreach}]

    <tr class="bsk_sep">
      <td class="brd"></td>
      <td colspan="6" class="line"></td>
      <td></td>
    </tr>
    <!--  basket items end  -->
    [{/foreach }]


    <!--  basket update/delete buttons  -->
     <tr class="sumrow">
       <td class="brd" valign="top">
          <input type="checkbox" name="checkAll" onClick="oxid.checkAll(this, 'aproducts')" title="[{ oxmultilang ident="BASKET_SELECT_ALL" }]">
       </td>
       <td colspan="6">

         <div class="frombasket">
            <input id="test_basket_Remove" class="btn" type="submit" name="removeBtn" value="[{ oxmultilang ident="BASKET_REMOVE" }]">
         </div>
         &nbsp;&nbsp;&nbsp;
         <span class="btn">
             <input id="test_basketUpdate" class="upd" type="submit" name="updateBtn" value="[{ oxmultilang ident="BASKET_UPDATE" }]">
         </span>
       </td>
       <td></td>
     </tr>

    <!--  basket summary  -->
    [{if !$oxcmp_basket->getDiscounts() }]
     <tr class="sumrow">
       <td class="brd"></td>
       <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TOTALNET" }]</td>
       <td id="test_basketNet" align="right">[{ $oxcmp_basket->getProductsNetPrice() }]&nbsp;[{ $currency->sign }]</td>
       <td></td>
     </tr>
     [{foreach from=$oxcmp_basket->getProductVats() item=VATitem key=key }]
     <tr class="sumrow">
       <td class="brd"></td>
       <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TAX1" }]&nbsp;[{ $key }][{ oxmultilang ident="BASKET_TAX2" }]</td>
       <td id="test_basketVAT_[{$key}]" align="right">[{ $VATitem }]&nbsp;[{ $currency->sign }]</td>
       <td></td>
     </tr>
     [{/foreach }]
    [{/if }]

    <tr class="sumrow">
      <td class="brd"></td>
      <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TOTALGROSS" }]</td>
      <td id="test_basketGross" align="right">[{ $oxcmp_basket->getFProductsPrice() }]&nbsp;[{ $currency->sign }]</td>
      <td></td>
    </tr>

    [{if $oxcmp_basket->getDiscounts() }]
      <tr class="bsk_sep"><td class="brd"></td><td colspan="6" class="line"></td><td></td></tr>
      [{foreach from=$oxcmp_basket->getDiscounts() item=oDiscount name=test_Discounts}]
       <tr class="sumrow">
         <td class="brd"></td>
         <td colspan="5" class="sumdesc discount">
           <b class="fs11">[{if $oDiscount->dDiscount < 0 }][{ oxmultilang ident="BASKET_CHARGE" }][{else}][{ oxmultilang ident="BASKET_DISCOUNT2" }][{/if}]&nbsp;</b>
           [{ $oDiscount->sDiscount }]:
         </td>
         <td id="test_basketDiscount_[{$smarty.foreach.test_Discounts.iteration}]" align="right">
           [{if $oDiscount->dDiscount < 0 }][{ $oDiscount->fDiscount|replace:"-":"" }][{else}]-[{ $oDiscount->fDiscount }][{/if}]&nbsp;[{ $currency->sign }]
         </td>
         <td></td>
       </tr>
      [{/foreach }]
      <tr class="sumrow">
        <td class="brd"></td>
        <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TOTALNET" }]</td>
        <td id="test_basketNet" align="right">[{ $oxcmp_basket->getProductsNetPrice() }]&nbsp;[{ $currency->sign }]</td>
        <td></td>
      </tr>
      [{foreach from=$oxcmp_basket->getProductVats() item=VATitem key=key }]
       <tr class="sumrow">
         <td class="brd"></td>
         <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TAX1" }] [{ $key }][{ oxmultilang ident="BASKET_TAX2" }]</td>
         <td id="test_basketVAT_[{$key}]" align="right">[{ $VATitem }]&nbsp;[{ $currency->sign }]</td>
         <td></td>
       </tr>
      [{/foreach }]
    [{/if }]

    [{if $oViewConf->getShowVouchers() && $oxcmp_basket->getVoucherDiscValue() }]
      <tr class="bsk_sep"><td class="brd"></td><td colspan="6" class="line"></td><td></td></tr>
      [{foreach from=$oxcmp_basket->getVouchers() item=oVoucher key=key name=Voucher}]
        <tr class="sumrow">
          <td class="brd"></td>
          <td colspan="2" class="coupon">
            <b class="fs11">&nbsp;&nbsp;[{ oxmultilang ident="BASKET_COUPON" }]</b>&nbsp;
            <span id="test_basketVoucher_[{$smarty.foreach.Voucher.iteration}]">([{ oxmultilang ident="BASKET_NOMBER" }] [{ $oVoucher->sVoucherNr }])</span>:
          </td>
          <td colspan="3">
            <div class="frombasket">
            <a id="test_basketVoucherRemove_[{$smarty.foreach.Voucher.iteration}]" href="[{ $oViewConf->getSelfLink() }]&amp;cl=basket&amp;fnc=removeVoucher&amp;voucherId=[{ $oVoucher->sVoucherId }]&amp;CustomError=basket" class="" rel="nofollow">[{ oxmultilang ident="BASKET_REMOVE2" }]</a>
            </div>
          </td>
          <td align="right">
            <span id="test_basketVoucherDiscount_[{$smarty.foreach.Voucher.iteration}]">-
            [{ $oVoucher->fVoucherdiscount }]&nbsp;
            [{ $currency->sign }]</span>
          </td>
          <td></td>
        </tr>
      [{/foreach }]
    [{/if }]

    [{if $oxcmp_basket->getDelCostNet() || $oxcmp_basket->getDelCostVat()}]
    <tr class="bsk_sep"><td class="brd"></td><td colspan="6" class="line"></td><td></td></tr>
    [{/if}]

    [{if $oxcmp_basket->getDelCostNet() }]
      <tr class="sumrow">
        <td class="brd"></td>
        <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_SHIPPINGNET" }]</td>
        <td id="test_basketDeliveryNet" align="right">[{ $oxcmp_basket->getDelCostNet() }]&nbsp;[{ $currency->sign }]</td>
        <td></td>
      </tr>
      [{if $oxcmp_basket->getDelCostVat() }]
        <tr class="sumrow">
          <td class="brd"></td>
          <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_PLUSTAX1" }] [{ $oxcmp_basket->getDelCostVatPercent() }][{ oxmultilang ident="BASKET_PLUSTAX2" }]</td>
          <td id="test_basketDeliveryVAT" align="right">[{ $oxcmp_basket->getDelCostVat() }]&nbsp;[{ $currency->sign }]</td>
          <td></td>
        </tr>
      [{/if }]
    [{elseif $oxcmp_basket->getFDeliveryCosts() }]
      <tr class="sumrow">
        <td class="brd"></td>
        <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_SHIPPING" }]</td>
        <td id="test_basketDeliveryNet" align="right">[{ $oxcmp_basket->getFDeliveryCosts() }]&nbsp;[{ $currency->sign }]</td>
        <td></td>
      </tr>
    [{/if }]

    [{if $oxcmp_basket->getPayCostNet() }]
      <tr class="sumrow">
        <td class="brd"></td>
        <td colspan="5" class="sumdesc">[{if $oxcmp_basket->getPaymentCosts() >= 0}][{ oxmultilang ident="BASKET_PAYMENT" }][{else}][{ oxmultilang ident="BASKET_CHARGE2" }][{/if}] [{ oxmultilang ident="BASKET_DISCOUNT3" }]</td>
        <td id="test_basketPaymentNet" align="right">[{ $oxcmp_basket->getPayCostNet() }]&nbsp;[{ $currency->sign }]</td>
        <td></td>
      </tr>
      [{if $oxcmp_basket->getPayCostVat() }]
        <tr class="sumrow">
          <td class="brd"></td>
          <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_PAYMENTTAX1" }] [{ $oxcmp_basket->getPayCostVatPercent() }] [{ oxmultilang ident="BASKET_PAYMENTTAX2" }]</td>
          <td id="test_basketPaymentVAT" align="right">[{ $oxcmp_basket->getPayCostVat() }]&nbsp;[{ $currency->sign }]</td>
          <td></td>
        </tr>
      [{/if }]
    [{elseif $oxcmp_basket->getFPaymentCosts() }]
        <tr class="sumrow">
          <td class="brd"></td>
          <td colspan="5" class="sumdesc">[{if $oxcmp_basket->getPaymentCosts() >= 0}][{ oxmultilang ident="BASKET_PAYMENT" }][{else}][{ oxmultilang ident="BASKET_CHARGE2" }][{/if}] [{ oxmultilang ident="BASKET_DISCOUNT3" }]</td>
          <td id="test_basketPaymentNet" align="right">[{ $oxcmp_basket->getFPaymentCosts() }]&nbsp;[{ $currency->sign }]</td>
          <td></td>
        </tr>
    [{/if }]

    [{ if $oxcmp_basket->getTsProtectionCosts() }]
      [{ if $oxcmp_basket->getTsProtectionNet() }]
          <tr class="sumrow">
            <td class="brd"></td>
            <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TSPROTECTION" }]</td>
            <td id="test_basketTsProtectionNet" align="right">[{ $oxcmp_basket->getTsProtectionNet() }]&nbsp;[{ $currency->sign}]</td>
            <td></td>
          </tr>
          [{ if $oxcmp_basket->getTsProtectionVat() }]
            <tr class="sumrow">
              <td class="brd"></td>
              <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TSPROTECTIONCHARGETAX1" }] [{ $oxcmp_basket->getTsProtectionVatPercent()}][{ oxmultilang ident="BASKET_TSPROTECTIONCHARGETAX2" }]</td>
              <td id="test_basketTsProtectionVat" align="right">[{ $oxcmp_basket->getTsProtectionVat() }]&nbsp;[{ $currency->sign}]</td>
              <td></td>
            </tr>
          [{/if}]
      [{elseif $oxcmp_basket->getFTsProtectionCosts()}]
            <tr class="sumrow">
            <td class="brd"></td>
            <td colspan="5" class="sumdesc">[{ oxmultilang ident="BASKET_TSPROTECTION" }]</td>
            <td id="test_basketTsProtectionNet" align="right">[{ $oxcmp_basket->getFTsProtectionCosts() }]&nbsp;[{ $currency->sign}]</td>
            <td></td>
          </tr>
      [{/if}]
    [{/if}]

    [{ if $oViewConf->getShowGiftWrapping() }]
      [{if $oxcmp_basket->getWrappCostNet() }]
        <tr class="sumrow">
          <td class="brd"></td>
          <td colspan="5" class="sumdesc">[{ oxmultilang ident="ORDER_WRAPPINGNET" }]</td>
          <td id="test_basketWrappNet" align="right">[{ $oxcmp_basket->getWrappCostNet() }] [{ $currency->sign}]</td>
          <td></td>
        </tr>
      [{elseif $oxcmp_basket->getFWrappingCosts()}]
        <tr class="sumrow">
          <td class="brd"></td>
          <td colspan="5" class="sumdesc">[{ oxmultilang ident="ORDER_WRAPPINGGROSS1" }]</td>
          <td id="test_basketWrappNet" align="right">[{ $oxcmp_basket->getFWrappingCosts() }] [{ $currency->sign}]</td>
          <td></td>
        </tr>
      [{/if}]
      [{if $oxcmp_basket->getWrappCostVat() }]
        <tr class="sumrow">
          <td class="brd"></td>
          <td colspan="5" class="sumdesc">[{ oxmultilang ident="ORDER_WRAPPINGTAX1" }] [{ $oxcmp_basket->getWrappCostVatPercent() }][{ oxmultilang ident="ORDER_WRAPPINGTAX2" }]</td>
          <td id="test_basketWrappVat" align="right">[{ $oxcmp_basket->getWrappCostVat() }] [{ $currency->sign}]</td>
          <td></td>
        </tr>
      [{/if}]
    [{/if}]

    <tr class="bsk_sep"><td class="brd"></td><td colspan="6" class="line"></td><td></td></tr>

    <tr class="sumrow total">
      <td class="brd"></td>
      <td colspan="5" class="sumdesc"><b>[{ oxmultilang ident="BASKET_GRANDTOTAL" }]</b></td>
      <td id="test_basketGrandTotal" align="right"><b>[{ $oxcmp_basket->getFPrice() }]&nbsp;[{ $currency->sign }]</b></td>
      <td></td>
    </tr>
    <tr class="bsk_sep">
      <td class="brd"></td>
      <td colspan="6" class="bigline"></td>
      <td></td>
    </tr>

    [{if $oxcmp_basket->hasSkipedDiscount() }]
      <tr class="sumrow">
        <td class="brd"></td>
        <td colspan="6"><span id="SkipDiscounts_link"><span class="note">**</span> [{ oxmultilang ident="BASKET_DISCOUNTS_NOT_APPLIED_FOR_ARTICLES" }]</span></td>
        <td></td>
      </tr>
    [{/if}]

    <tr>
      <td colspan="8" class="brd bottrow"></td>
    </tr>
    </tbody>
  </table>
 </form>

  [{if $oViewConf->getShowVouchers()}]
      <strong id="test_VoucherHeader" class="boxhead">[{ oxmultilang ident="BASKET_REDEEMCOUPON" }]</strong>
      <div class="box">
         [{foreach from=$Errors.basket item=oEr key=key }]
         [{if $oEr->getErrorClassType() == 'oxVoucherException'}]
             <span class="err">[{ oxmultilang ident="BASKET_COUPONNOTACCEPTED1" }] [{ $oEr->getValue('voucherNr') }] [{ oxmultilang ident="BASKET_COUPONNOTACCEPTED2" }]</span><br>
             <span class="err">[{ oxmultilang ident="BASKET_REASON" }]</span>
             <span class="err">[{ $oEr->getOxMessage() }]</span><br>
          [{/if}]
          [{/foreach}]

          <form name="voucher" action="[{ $oViewConf->getSelfActionLink() }]" method="post" class="left">
              <div>
                  <label>[{ oxmultilang ident="BASKET_ENTERCOUPONNUMBER" }]</label>
                  [{ $oViewConf->getHiddenSid() }]
                  <input type="hidden" name="cl" value="basket">
                  <input type="hidden" name="fnc" value="addVoucher">
                  <input type="text" size="20" name="voucherNr">
                  <span class="btn"><input id="test_basketVoucherAdd" class="btn" type="submit" value="[{ oxmultilang ident="BASKET_SUBMITCOUPON" }]"></span>
                  <input type="hidden" name="CustomError" value='basket'>
              </div>
          </form>
      </div>
  [{/if}]

  <div class="bar prevnext bottom">
    [{if $oView->showBackToShop()}]
    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="basket">
          <input type="hidden" name="fnc" value="backtoshop">
          <div class="left arrowdown">
              <input type="submit" value="[{ oxmultilang ident="BASKET_CONTINUESHOPPING" }]">
          </div>
      </div>
    </form>
    [{/if}]

    [{if $oView->isLowOrderPrice() }]
      <div class="minorderprice">[{ oxmultilang ident="BASKET_MINORDERPRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
    [{else}]
    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="user">
          <div class="right arrowright">
              <input id="test_BasketNextStepBottom" type="submit" value="[{ oxmultilang ident="BASKET_NEXTSTEP" }]">
          </div>
      </div>
    </form>
    [{/if}]

  </div>

  [{if $oView->getBasketSimilarList() }]
    <strong id="test_similarlist" class="head2">[{ oxmultilang ident="ORDER_OTHERINTRESTINGARTICLES"}]</strong>
    [{foreach from=$oView->getBasketSimilarList() item=simproduct }]
        [{include file="inc/product.tpl" size="small" product=$simproduct testid="similar_"|cat:$simproduct->oxarticles__oxid->value }]
    [{/foreach }]
  [{/if }]

  &nbsp;

[{/if }]


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
