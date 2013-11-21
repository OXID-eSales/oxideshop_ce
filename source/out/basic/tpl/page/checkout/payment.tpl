[{assign var="template_title" value="PAYMENT_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

<!-- ordering steps -->
[{include file="inc/steps_item.tpl" highlight=3}]
[{assign var="currency" value=$oView->getActCurrency() }]

  [{ if $oView->getAllSets() }]

    <strong id="test_DeliveryHeader" class="boxhead paymentboxhead">[{ if $oView->getAllSetsCnt() > 1 }][{ oxmultilang ident="PAYMENT_SELECTSHIPPING" }][{else}][{ oxmultilang ident="PAYMENT_SELECTEDSHIPPING" }][{/if}]</strong>
    <div class="box info">
        <form action="[{ $oViewConf->getSslSelfLink() }]" name="shipping" id="shipping" method="post">
          <div>
              [{ $oViewConf->getHiddenSid() }]
              [{ $oViewConf->getNavFormParams() }]
              <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
              <input type="hidden" name="fnc" value="changeshipping">

              <div class="left">
                <select name="sShipSet" onChange="JavaScript:document.forms.shipping.submit();">
                  [{foreach key=sShipID from=$oView->getAllSets() item=oShippingSet name=ShipSetSelect}]
                    <option value="[{$sShipID}]" [{if $oShippingSet->blSelected}]SELECTED[{/if}]>[{ $oShippingSet->oxdeliveryset__oxtitle->value }]</option>
                  [{/foreach}]
                </select>
                <noscript>
                  <div>
                    <span class="btn"><input class="btn" type="submit" value="[{ oxmultilang ident="PAYMENT_UPDATESHIPPING" }]" ></span>
                  </div>
                </noscript>
              </div>
              <div id="test_shipSetCost" class="right fs10">
                [{ if $oxcmp_basket->getDeliveryCosts() }]
                  [{ oxmultilang ident="PAYMENT_CHARGE" }] [{ $oxcmp_basket->getFDeliveryCosts() }] [{ $currency->sign}]
                [{ /if}]
              </div>
          </div>
        </form>
    </div>

  [{/if}]

  [{assign var="iPayError" value=$oView->getPaymentError() }]

  [{ if $iPayError == 1}]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_COMLETEALLFIELDS" }]</div>
  [{ elseif $iPayError == 2}]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_AUTHORIZATIONFAILED" }]</div>
  [{ elseif $iPayError == 4 }]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_UNAVAILABLESHIPPING" }]</div>
  [{ elseif $iPayError == 5 }]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_UNAVAILABLEPAYMENT" }]</div>
  [{ elseif $iPayError == 6 }]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_UNAVAILABLETSPROTECTION" }]</div>
  [{ elseif $iPayError > 6 }]
    <!--Add custom error message here-->
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_UNAVAILABLEPAYMENT" }]</div>
  [{ elseif $iPayError == -1}]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_ERRUNAVAILABLEPAYMENT" }] "[{ $oView->getPaymentErrorText() }]").</div>
  [{ elseif $iPayError == -2}]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_NOSHIPPINGFOUND" }]</div>
  [{ elseif $iPayError == -3}]
    <br><div class="errorbox">[{ oxmultilang ident="PAYMENT_SELECTANOTHERPAYMENT" }]</div>
  [{ elseif $iPayError == -4}]
    <br><div class="errorbox">[{ oxmultilang ident="MESSAGE_PAYMENT_BANK_CODE_INVALID" }]</div>
  [{ elseif $iPayError == -5}]
    <br><div class="errorbox">[{ oxmultilang ident="MESSAGE_PAYMENT_ACCOUNT_NUMBER_INVALID" }]</div>
  [{/if}]



        <form action="[{ $oViewConf->getSslSelfLink() }]" name="order" method="post">
          <div>
              [{ $oViewConf->getHiddenSid() }]
              [{ $oViewConf->getNavFormParams() }]
              <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
              <input type="hidden" name="fnc" value="validatepayment">
          </div>

            [{if $oView->getPaymentList()}]
              <strong id="test_PaymentHeader" class="boxhead">[{ oxmultilang ident="PAYMENT_PAYMENT" }]</strong>
              <div class="box info">

              <table class="form" style="width:92%">
                [{ assign var="inptcounter" value="-1"}]
                [{foreach key=sPaymentID from=$oView->getPaymentList() item=paymentmethod name=PaymentSelect}]
                  [{ assign var="inptcounter" value="`$inptcounter+1`"}]
                  [{if $sPaymentID == "oxidcashondel"}]
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td><input id="test_Payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]></td>
                      <td id="test_PaymentDesc_[{$smarty.foreach.PaymentSelect.iteration}]" colspan="2"><label><b>[{ $paymentmethod->oxpayments__oxdesc->value}]</b></label></td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td colspan="2">[{ oxmultilang ident="PAYMENT_PLUSCODCHARGE1" }] [{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}] [{ oxmultilang ident="PAYMENT_PLUSCODCHARGE2" }]</td>
                    </tr>
                  [{elseif $sPaymentID == "oxidcreditcard"}]
                    [{ assign var="dynvalue" value=$oView->getDynValue()}]
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td><input id="test_Payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]></td>
                      <td id="test_PaymentDesc_[{$smarty.foreach.PaymentSelect.iteration}]" colspan="2"><label><b>[{ $paymentmethod->oxpayments__oxdesc->value}]</b></label></td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td valign="top"><label>[{ oxmultilang ident="PAYMENT_CREDITCARD" }]</label></td>
                      <td>
                        <select name="dynvalue[kktype]">
                          <option value="mcd" [{ if ($dynvalue.kktype == "mcd" || !$dynvalue.kktype)}]selected[{/if}]>[{ oxmultilang ident="PAYMENT_MASTERCARD" }]</option>
                          <option value="vis" [{ if $dynvalue.kktype == "vis"}]selected[{/if}]>[{ oxmultilang ident="PAYMENT_VISA" }]</option>
                          <!--
                          <option value="amx" [{ if $dynvalue.kktype == "amx"}]selected[{/if}]>American Express</option>
                          <option value="dsc" [{ if $dynvalue.kktype == "dsc"}]selected[{/if}]>Discover</option>
                          <option value="dnc" [{ if $dynvalue.kktype == "dnc"}]selected[{/if}]>Diners Club</option>
                          <option value="jcb" [{ if $dynvalue.kktype == "jcb"}]selected[{/if}]>JCB</option>
                          <option value="swi" [{ if $dynvalue.kktype == "swi"}]selected[{/if}]>Switch</option>
                          <option value="dlt" [{ if $dynvalue.kktype == "dlt"}]selected[{/if}]>Delta</option>
                          <option value="enr" [{ if $dynvalue.kktype == "enr"}]selected[{/if}]>EnRoute</option>
                          -->
                        </select>
                      </td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td><label>[{ oxmultilang ident="PAYMENT_NUMBER" }]</label></td>
                      <td>
                        <input type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[kknumber]" value="[{ $dynvalue.kknumber }]">
                      </td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td valign="top"><label>[{ oxmultilang ident="PAYMENT_ACCOUNTHOLDER" }]</label></td>
                      <td>
                        <input type="text" size="20" maxlength="64" name="dynvalue[kkname]" value="[{ if $dynvalue.kkname }][{ $dynvalue.kkname }][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]"><br>
                        <div class="fs10 def_color_1">[{ oxmultilang ident="PAYMENT_DIFFERENTBILLINGADDRESS" }]</div>
                      </td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td><label>[{ oxmultilang ident="PAYMENT_VALIDUNTIL" }]</label></td>
                      <td>
                        <select name="dynvalue[kkmonth]">
                          <option [{ if $dynvalue.kkmonth == "01"}]selected[{/if}]>01</option>
                          <option [{ if $dynvalue.kkmonth == "02"}]selected[{/if}]>02</option>
                          <option [{ if $dynvalue.kkmonth == "03"}]selected[{/if}]>03</option>
                          <option [{ if $dynvalue.kkmonth == "04"}]selected[{/if}]>04</option>
                          <option [{ if $dynvalue.kkmonth == "05"}]selected[{/if}]>05</option>
                          <option [{ if $dynvalue.kkmonth == "06"}]selected[{/if}]>06</option>
                          <option [{ if $dynvalue.kkmonth == "07"}]selected[{/if}]>07</option>
                          <option [{ if $dynvalue.kkmonth == "08"}]selected[{/if}]>08</option>
                          <option [{ if $dynvalue.kkmonth == "09"}]selected[{/if}]>09</option>
                          <option [{ if $dynvalue.kkmonth == "10"}]selected[{/if}]>10</option>
                          <option [{ if $dynvalue.kkmonth == "11"}]selected[{/if}]>11</option>
                          <option [{ if $dynvalue.kkmonth == "12"}]selected[{/if}]>12</option>
                        </select>&nbsp;/&nbsp;

                        <select name="dynvalue[kkyear]">
                        [{foreach from=$oView->getCreditYears() item=year}]
                            <option [{ if $dynvalue.kkyear == $year}]selected[{/if}]>[{$year}]</option>
                        [{/foreach}]
                        </select>
                      </td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td valign="top"><label>[{ oxmultilang ident="PAYMENT_SECURITYCODE" }]</label></td>
                      <td>
                        <input type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[kkpruef]" value="[{ $dynvalue.kkpruef }]"><br>
                        <div class="fs10 def_color_1">[{ oxmultilang ident="PAYMENT_SECURITYCODEDESCRIPTION" }]</div>
                      </td>
                    </tr>
                  [{elseif $sPaymentID == "oxiddebitnote"}]
                    [{ assign var="dynvalue" value=$oView->getDynValue()}]
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td><input id="test_Payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]></td>
                      <td id="test_PaymentDesc_[{$smarty.foreach.PaymentSelect.iteration}]" colspan="2"><label><b>[{ $paymentmethod->oxpayments__oxdesc->value}]</b></label></td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td><label>[{ oxmultilang ident="PAYMENT_BANK" }]</label></td>
                      <td><input id="test_Payment_[{$sPaymentID}]_1" type="text" size="20" maxlength="64" name="dynvalue[lsbankname]" value="[{ $dynvalue.lsbankname }]"></td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td><label>[{ oxmultilang ident="PAYMENT_ROUTINGNUMBER" }]</label></td>
                      <td><input type="text" size="20" maxlength="64" name="dynvalue[lsblz]" value="[{ $dynvalue.lsblz }]"></td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td><label>[{ oxmultilang ident="PAYMENT_ACCOUNTNUMBER" }]</label></td>
                      <td><input type="text" size="20" maxlength="64" name="dynvalue[lsktonr]" value="[{ $dynvalue.lsktonr }]"></td>
                    </tr>
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td></td>
                      <td><label>[{ oxmultilang ident="PAYMENT_ACCOUNTHOLDER2" }]</label></td>
                      <td><input type="text" size="20" maxlength="64" name="dynvalue[lsktoinhaber]" value="[{ if $dynvalue.lsktoinhaber }][{ $dynvalue.lsktoinhaber }][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]"></td>
                    </tr>
                  [{else}]
                    <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                      <td><input id="test_Payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]></td>
                      <td id="test_PaymentDesc_[{$smarty.foreach.PaymentSelect.iteration}]" colspan="2"><label><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->dAddPaymentSum }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label></td>
                    </tr>
                    [{foreach from=$paymentmethod->getDynValues() item=value name=PaymentDynValues}]
                      <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                        <td></td>
                        <td><label>[{ $value->name}]</label></td>
                        <td>
                          <input id="test_Payment_[{$sPaymentID}]_[{$smarty.foreach.PaymentDynValues.iteration}]" type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[[{$value->name}]]" value="[{ $value->value}]">
                        </td>
                      </tr>
                    [{/foreach}]
                  [{/if}]
                  <tr onclick="oxid.form.select('paymentid',[{$inptcounter}]);">
                    <td></td>
                    <td id="test_PaymentLongDesc_[{$sPaymentID}]" colspan="2">[{ $paymentmethod->oxpayments__oxlongdesc->getRawValue()}]</td>
                  </tr>
                    [{if $inptcounter > -1 && $inptcounter < ($oView->getPaymentCnt()-1) }]
                    <tr class="tr_sep">
                      <td colspan="3"><div class="dot_sep"></div></td>
                    </tr>
                    [{/if}]
                [{/foreach}]
                </table>

            </div>
            [{if $oView->getTSExcellenceId()}]
            <strong id="test_TsProtectionHeader" class="boxhead paymentboxhead">[{ oxmultilang ident="PAYMENT_TSPROTECTION" }]</strong>
            <div class="box info">
              <div class="etrustlogocol">
                <a href="https://www.trustedshops.com/shop/certificate.php?shop_id=[{$oView->getTSExcellenceId()}]" target="_blank">
                  <img style="border:0px none;" src="[{$oViewConf->getImageUrl()}]/trustedshops_m.gif" title="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_IMGTITLE" }]">
                </a>
              </div>
              <div class="etrustdescocol">
                <input type="checkbox" name="bltsprotection" value="1" [{if $oView->getCheckedTsProductId()}]checked[{/if}]>
                [{assign var="aTsProtections" value=$oView->getTsProtections() }]
                [{if count($aTsProtections) > 1 }]
                <select name="stsprotection">
                  [{foreach from=$aTsProtections item=oTsProduct}]
                    <option value="[{$oTsProduct->getTsId()}]" [{if $oView->getCheckedTsProductId() == $oTsProduct->getTsId()}]SELECTED[{/if}]>[{ oxmultilang ident="PAYMENT_TSPROTECTIONFOR" }] [{ $oTsProduct->getAmount() }] [{ $currency->sign}] ([{ $oTsProduct->getFPrice() }] [{ $currency->sign}] [{ oxmultilang ident="PAYMENT_INCLUDEVAT" }]) </option>
                  [{/foreach}]
                </select>
                [{else}]
                    [{assign var="oTsProduct" value=$aTsProtections[0] }]
                    <input type="hidden" name="stsprotection" value="[{$oTsProduct->getTsId()}]">
                    [{ oxmultilang ident="PAYMENT_TSPROTECTIONFOR" }] [{ $oTsProduct->getAmount() }] [{ $currency->sign}] ([{ $oTsProduct->getFPrice() }] [{ $currency->sign}] [{ oxmultilang ident="PAYMENT_INCLUDEVAT" }])
                [{/if}]
              <br>
              <br>
                [{ oxmultilang ident="PAYMENT_TSPROTECTIONTEXT" }] <a href="http://www.trustedshops.com/shop/data_privacy.php?shop_id=[{$oView->getTSExcellenceId()}]" target="_blank">[{ oxmultilang ident="PAYMENT_TSPROTECTIONTEXT2" }]</a>
                [{ oxmultilang ident="PAYMENT_TSPROTECTIONTEXT3" }] <a href="http://www.trustedshops.com/shop/protection_conditions.php?shop_id=[{$oView->getTSExcellenceId()}]" target="_blank">[{ oxmultilang ident="PAYMENT_TSPROTECTIONTEXT4" }]</a> [{ oxmultilang ident="PAYMENT_TSPROTECTIONTEXT5" }]
              </div>
            </div>
            [{/if}]
                [{if $oView->isLowOrderPrice()}]
                  <div class="bar prevnext order">
                    <div class="minorderprice">[{ oxmultilang ident="BASKET_MINORDERPRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</div>
                  </div>
                [{else}]
                  <div class="bar prevnext">
                    <div class="right arrowright">
                        <input id="test_PaymentNextStepBottom" name="userform" type="submit" value="[{ oxmultilang ident="PAYMENT_NEXTSTEP" }]">
                    </div>
                  </div>
                [{/if}]


              [{elseif $oView->getEmptyPayment()}]
                <strong id="test_PaymentHeader" class="boxhead">[{ oxmultilang ident="PAYMENT_INFO" }]</strong>
                <div class="box info">

                    [{ oxmultilang ident="PAYMENT_EMPTY_TEXT" }]
                </div>

                <div class="bar prevnext">
                  <div class="right arrowright">
                      <input type="hidden" name="paymentid" value="oxempty">
                      <input id="test_PaymentNextStepBottom" name="userform" type="submit" value="[{ oxmultilang ident="PAYMENT_NEXTSTEP" }]">
                  </div>
                </div>
              [{/if}]

        </form>

        &nbsp;


[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]