[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="oConf"     value=$oViewConf->getConfig() }]
[{ assign var="currency" value=$oEmailView->getCurrency() }]
[{ assign var="user"     value=$oEmailView->getUser() }]
[{ assign var="oDelSet"   value=$order->getDelSet() }]
[{ assign var="basket"   value=$order->getBasket() }]
[{ assign var="payment"  value=$order->getPayment() }]
[{ assign var="sOrderId"   value=$order->getId() }]
[{ assign var="oOrderFileList"   value=$oEmailView->getOrderFileList($sOrderId) }]

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <title>[{ $shop->oxshops__oxordersubject->value }]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$oEmailView->getCharset()}]">
  </head>
  <body bgcolor="#FFFFFF" link="#355222" alink="#355222" vlink="#355222" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;">
      <img src="[{$oViewConf->getImageUrl('logo_white.gif', false)}]" border="0" hspace="0" vspace="0" alt="[{ $shop->oxshops__oxname->value }]" align="texttop"><br><br>
    [{if $payment->oxuserpayments__oxpaymentsid->value == "oxempty"}]
      [{oxcontent ident="oxuserordernpemail"}]
    [{else}]
      [{oxcontent ident="oxuserorderemail"}]
    [{/if}]
    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ORDERNOMBER" }] <b>[{ $order->oxorder__oxordernr->value }]</b><br><br>
    <table border="0" cellspacing="0" cellpadding="0" width="600">
      <tr>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" height="15" width="100">
          &nbsp;&nbsp;<b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PRODUCT" }]</b>
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" height="15">
          &nbsp;&nbsp;
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="70">
          <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_UNITPRICE" }]</b>
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="70">
          <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_QUANTITY" }]</b>
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="70">
          <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_VAT" }]</b>
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="70">
          <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTAL" }]</b>
        </td>
        [{if $blShowReviewLink}]
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="70">
          [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PRODUCTREVIEW" }]
        </td>
        [{/if}]
      </tr>
    [{assign var="basketitemlist" value=$basket->getBasketArticles() }]
    [{foreach key=basketindex from=$basket->getContents() item=basketitem}]
    [{assign var="basketproduct" value=$basketitemlist.$basketindex }]
      <tr>
        <td valign="top" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;">
          <img src="[{$basketproduct->getThumbnailUrl(false) }]" border="0" hspace="0" vspace="0" alt="[{$basketitem->getTitle()|strip_tags}]" align="texttop">
            [{if $oViewConf->getShowGiftWrapping() }]
                [{assign var="oWrapping" value=$basketitem->getWrapping() }]
                <br><b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_WRAPPING" }]&nbsp;</b>[{ if !$basketitem->getWrappingId() }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_NONE" }][{else}][{$oWrapping->oxwrapping__oxname->value }][{/if}]
            [{/if}]
        </td>
        <td valign="top" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;">
          <b>[{$basketitem->getTitle()}]</b>
          [{ if $basketitem->getChosenSelList() }],
            [{foreach from=$basketitem->getChosenSelList() item=oList}]
              [{ $oList->name }] [{ $oList->value }]&nbsp;
            [{/foreach}]
          [{/if}]
          [{ if $basketitem->getPersParams() }]
            [{foreach key=sVar from=$basketitem->getPersParams() item=aParam}]
              ,&nbsp;<em>[{$sVar}] : [{$aParam}]</em>
            [{/foreach}]
          [{/if}]
          <br>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ARTNOMBER" }] [{ $basketproduct->oxarticles__oxartnum->value }]
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
          <b>[{if $basketitem->getFUnitPrice() }][{ $basketitem->getFUnitPrice() }] [{ $currency->sign}][{/if}]</b>
          [{if $basketitem->aDiscounts}]<br><br>
            <em style="font-size: 7pt;font-weight: normal;">[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_DISCOUNT" }]
            [{foreach from=$basketitem->aDiscounts item=oDiscount}]
              <br>[{ $oDiscount->sDiscount }]
            [{/foreach}]
            </em>
          [{/if}]
          [{ if $basketproduct->oxarticles__oxorderinfo->value }]
            [{ $basketproduct->oxarticles__oxorderinfo->value }]
          [{/if}]
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
          [{$basketitem->getAmount()}]
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
          [{$basketitem->getVatPercent() }]%
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
          <b>[{ $basketitem->getFTotalPrice() }] [{ $currency->sign}]</b>
        </td>
        [{if $blShowReviewLink}]
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
          <a href="[{ $oConf->getShopURL() }]index.php?shp=[{$shop->oxshops__oxid->value}]&amp;anid=[{$basketitem->getProductId()}]&amp;cl=review&amp;reviewuserhash=[{$user->getReviewUserHash($user->getId())}]" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" target="_blank">[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_REVIEW" }]</a>
        </td>
        [{/if}]
      </tr>
    [{/foreach}]
    <tr>
      <td height="1" bgcolor="#BEBEBE"></td>
      <td height="1" bgcolor="#BEBEBE"></td>
      <td height="1" bgcolor="#BEBEBE"></td>
      <td height="1" bgcolor="#BEBEBE"></td>
      <td height="1" bgcolor="#BEBEBE"></td>
      <td height="1" bgcolor="#BEBEBE"></td>
      <td height="1" bgcolor="#BEBEBE"></td>
    </tr>
  </table>
  <br>

  [{if $oViewConf->getShowGiftWrapping() && $basket->getCard() }]
    [{assign var="oCard" value=$basket->getCard() }]
    <table border="0" cellspacing="0" cellpadding="2" width="600">
      <tr>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top">
          <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_YOURGREETINGCARD" }]</b><br>
          <img src="[{$oCard->getPictureUrl()}]" alt="[{$oCard->oxwrapping__oxname->value}]" hspace="0" vspace="0" border="0" align="top"><br><br>
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top">
          [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_YOURMESSAGE" }]<br><br>
          [{$basket->getCardMessage()}]
        </td>
      </tr>
    </table>
    <br>
  [{/if}]


  <table border="0" cellspacing="0" cellpadding="2" width="600">
    <tr>
      <td width="50%" valign="top">
        <table border="0" cellspacing="0" cellpadding="0">
          [{if $oViewConf->getShowVouchers() && $basket->getVoucherDiscValue() }]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top">
                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_USEDCOUPONS" }]<br>
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top">
                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_REBATE" }]
              </td>
            </tr>
          [{/if}]
          [{ foreach from=$order->getVoucherList() item=voucher}]
            [{ assign var="voucherseries" value=$voucher->getSerie() }]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top">
                [{$voucher->oxvouchers__oxvouchernr->value}]
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top">
                [{$voucherseries->oxvoucherseries__oxdiscount->value}] [{ if $voucherseries->oxvoucherseries__oxdiscounttype->value == "absolute"}][{ $currency->sign}][{else}]%[{/if}]
              </td>
            </tr>
          [{/foreach }]
        </table>
      </td>
      <td width="50%" valign="top">
        <table border="0" cellspacing="0" cellpadding="2" width="300">
        [{if !$basket->getDiscounts()}]
          [{* netto price *}]
          <tr>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
              [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALNET" }]
            </td>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right" width="60">
              [{ $basket->getProductsNetPrice() }] [{ $currency->sign}]
            </td>
          </tr>
          [{* VATs *}]
          [{foreach from=$basket->getProductVats() item=VATitem key=key}]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX1" }] [{ $key }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX2" }]
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ $VATitem }] [{ $currency->sign}]
              </td>
            </tr>
          [{/foreach}]

          <tr><td height="1"></td><td height="1" bgcolor="#BEBEBE"></td></tr>
          [{/if}]
          [{* brutto price *}]
          <tr>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
              [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALGROSS" }]
            </td>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
              [{ $basket->getFProductsPrice() }] [{ $currency->sign}]
            </td>
          </tr>
          [{* applied discounts *}]
          [{if $basket->getDiscounts()}]
            <tr><td height="1"></td><td height="1" bgcolor="#BEBEBE"></td></tr>
            [{foreach from=$basket->getDiscounts() item=oDiscount}]
              <tr>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{if $oDiscount->dDiscount < 0 }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_CHARGE" }][{else}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_DICOUNT" }][{/if}] <em>[{ $oDiscount->sDiscount }]</em> :
                </td>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{if $oDiscount->dDiscount < 0 }][{ $oDiscount->fDiscount|replace:"-":"" }][{else}]-[{ $oDiscount->fDiscount }][{/if}] [{ $currency->sign}]
                </td>
              </tr>
            [{/foreach}]
            <tr><td height="1"></td><td height="1" bgcolor="#BEBEBE"></td></tr>
            [{* netto price *}]
                <tr>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALNET" }]
                </td>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right" width="60">
                    [{ $basket->getProductsNetPrice() }] [{ $currency->sign}]
                </td>
              </tr>
            [{* VATs *}]
            [{foreach from=$basket->getProductVats() item=VATitem key=key}]
              <tr>
                  <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX1" }] [{ $key }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX2" }]
                </td>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ $VATitem }] [{ $currency->sign}]
                </td>
              </tr>
            [{/foreach}]
          [{/if}]
          <tr><td height="1"></td><td height="1" bgcolor="#BEBEBE"></td></tr>
          [{* voucher discounts *}]
          [{if $oViewConf->getShowVouchers() && $basket->getVoucherDiscValue() }]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_COUPON" }]
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ if $basket->getFVoucherDiscountValue() > 0 }]-[{/if}][{ $basket->getFVoucherDiscountValue()}]
              </td>
            </tr>

            <tr><td height="1"></td><td height="1" bgcolor="#BEBEBE"></td></tr>
          [{/if}]
          [{* delivery costs *}]
          [{* delivery VAT (if available) *}]
          [{if $basket->getDelCostVat() > 0}]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGNET" }]
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ $basket->getDelCostNet() }] [{ $currency->sign}]
              </td>
            </tr>
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TAX1" }] [{ $basket->getDelCostVatPercent() }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TAX2" }]
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ $basket->getDelCostVat() }] [{ $currency->sign}]
              </td>
            </tr>
          [{/if}]
          <tr>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
              [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGGROSS1" }] [{if $basket->getDelCostVat() > 0}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGGROSS2" }] [{/if}]:
            </td>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
              [{ $basket->getFDeliveryCosts() }] [{ $currency->sign}]
            </td>
          </tr>
          [{* payment sum *}]
          [{ if $basket->getPaymentCosts() }]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{if $basket->getPaymentCosts() >= 0}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT1" }][{else}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT2" }][{/if}] [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT3" }]
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ $basket->getPayCostNet() }] [{ $currency->sign}]
              </td>
            </tr>
            [{* payment sum VAT (if available) *}]
            [{ if $basket->getDelCostVat() }]
              <tr>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEVAT1" }] [{ $basket->getPayCostVatPercent()}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEVAT2" }]
                </td>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ $basket->getPayCostVat() }] [{ $currency->sign}]
                </td>
              </tr>
            [{/if}]
          [{/if}]

          [{ if $basket->getTsProtectionCosts() }]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TSPROTECTION" }]
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                [{ $basket->getTsProtectionNet() }] [{ $currency->sign}]
              </td>
            </tr>
            [{ if $basket->getTsProtectionVat() }]
              <tr>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TSPROTECTIONCHARGETAX1" }] [{ $basket->getTsProtectionVatPercent()}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TSPROTECTIONCHARGETAX2" }]
                </td>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ $basket->getTsProtectionVat() }]&nbsp;[{ $currency->sign}]
                </td>
              </tr>
            [{/if}]
          [{/if}]

          [{ if $oViewConf->getShowGiftWrapping() && $basket->getFWrappingCosts() }]
            [{if $basket->getWrappCostVat()}]
              <tr>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_WRAPPINGNET" }]
                </td>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ $basket->getWrappCostNet() }] [{ $currency->sign}]
                </td>
              </tr>
              <tr>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX21" }] [{ $basket->getWrappCostVatPercent() }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX22" }]
                </td>
                <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ $basket->getWrappCostVat() }] [{ $currency->sign}]
                </td>
              </tr>
            [{/if}]
            <tr>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_WRAPPINGANDGREETINGCARD1" }][{if $basket->getWrappCostVat()}] [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_WRAPPINGANDGREETINGCARD2" }][{/if}] :
              </td>
              <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
                  [{ $basket->getFWrappingCosts() }] [{ $currency->sign}]
              </td>
            </tr>
          [{/if}]

          <tr><td height="1"></td><td height="1" bgcolor="#BEBEBE"></td></tr>
          [{* grand total price *}]
          <tr>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
              <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_GRANDTOTAL" }]</b>
            </td>
            <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" valign="top" align="right">
              <b>[{ $basket->getFPrice() }] [{ $currency->sign}]</b>
            </td>
          </tr>
          [{* *}]
        </table>
      </td>
    </tr>
  </table>

  [{ if $order->oxorder__oxremark->value }]
    <br><b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_YOURMESSAGE" }] </b>[{ $order->oxorder__oxremark->value|oxescape }]<br>
  [{/if}]

    [{ if $oOrderFileList }]
        <br><b>[{ oxmultilang ident="MY_DOWNLOADS_DESC" }]</b>
        [{foreach from=$oOrderFileList item="oOrderFile"}]
          [{if $order->oxorder__oxpaid->value || !$oOrderFile->oxorderfiles__oxpurchasedonly->value}]
            <br><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=download" params="sorderfileid="|cat:$oOrderFile->getId() }]" rel="nofollow">[{$oOrderFile->oxorderfiles__oxfilename->value}]</a> [{$oOrderFile->getFileSize()|oxfilesize}]
          [{else}]
            <br>[{$oOrderFile->oxorderfiles__oxfilename->value}] <b>[{ oxmultilang ident="DOWNLOADS_PAYMENT_PENDING" }]</b>
          [{/if}]
        [{/foreach}]
    [{/if}]

  <br>
  [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTMETHOD" }] <b>[{ $payment->oxpayments__oxdesc->value }] [{ if $basket->getPaymentCosts() }]([{ $basket->getFPaymentCosts() }] [{ $currency->sign}])[{/if}]</b><br>
  [{/if}]<br>
  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_EMAILADDRESS" }] [{ $user->oxuser__oxusername->value }]<br>
  <br>
  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BILLINGADDRESS" }]  <br>
  [{ $order->oxorder__oxbillcompany->value }]<br>
  [{ $order->oxorder__oxbillsal->value|oxmultilangsal}] [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }]<br>
  [{if $order->oxorder__oxbilladdinfo->value }][{ $order->oxorder__oxbilladdinfo->value }]<br>[{/if}]
  [{ $order->oxorder__oxbillstreet->value }] [{ $order->oxorder__oxbillstreetnr->value }]<br>
  [{ $order->oxorder__oxbillstateid->value }]
  [{ $order->oxorder__oxbillzip->value }] [{ $order->oxorder__oxbillcity->value }]<br>
  [{ $order->oxorder__oxbillcountry->value }]<br>
  [{if $order->oxorder__oxbillustid->value}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_VATIDNOMBER" }] [{ $order->oxorder__oxbillustid->value }]<br>[{/if}]
  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PHONE" }] [{ $order->oxorder__oxbillfon->value }]<br><br>

  [{ if $order->oxorder__oxdellname->value }]
    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGADDRESS" }]  <br>
    [{ $order->oxorder__oxdelcompany->value }]<br>
    [{ $order->oxorder__oxdelsal->value|oxmultilangsal }] [{ $order->oxorder__oxdelfname->value }] [{ $order->oxorder__oxdellname->value }]<br>
    [{if $order->oxorder__oxdeladdinfo->value }][{ $order->oxorder__oxdeladdinfo->value }]<br>[{/if}]
    [{ $order->oxorder__oxdelstreet->value }] [{ $order->oxorder__oxdelstreetnr->value }]<br>
    [{ $order->oxorder__oxdelstateid->value }]
    [{ $order->oxorder__oxdelzip->value }] [{ $order->oxorder__oxdelcity->value }]<br>
    [{ $order->oxorder__oxdelcountry->value }]<br>
  [{/if}]

  [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER" }] <strong>[{ $oDelSet->oxdeliveryset__oxtitle->value }]</strong><br>[{/if}]

  [{if $payment->oxuserpayments__oxpaymentsid->value == "oxidpayadvance"}]
    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BANK" }] [{$shop->oxshops__oxbankname->value}]<br>
    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ROUTINGNOMBER" }] [{$shop->oxshops__oxbankcode->value}]<br>
    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ACCOUNTNOMBER" }] [{$shop->oxshops__oxbanknumber->value}]<br>
    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BIC" }] [{$shop->oxshops__oxbiccode->value}]<br>
    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_IBAN" }] [{$shop->oxshops__oxibannumber->value}]
  [{/if}]

  [{ oxcontent ident="oxuserorderemailend" }]

  [{if $oViewConf->showTs("ORDEREMAIL") && $oViewConf->getTsId() }]
  <br><br>
  [{assign var="sTSRatingImg" value="https://www.trustedshops.com/bewertung/widget/img/bewerten_"|cat:$oViewConf->getActLanguageAbbr()|cat:".gif"}]
  [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TS_RATINGS_RATEUS" }]<br><br>
  <a href="[{ $oViewConf->getTsRatingUrl() }]" target="_blank" title="[{ oxmultilang ident="TS_RATINGS_URL_TITLE" }]">
    <img src="[{$sTSRatingImg}]" border="0" alt="[{ oxmultilang ident="TS_RATINGS_BUTTON_ALT" }]" align="middle">
  </a>
  [{/if}]


    <br><br>
    [{ oxcontent ident="oxemailfooter" }]

  </body>
</html>
