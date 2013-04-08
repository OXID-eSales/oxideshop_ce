[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="oConf"     value=$oViewConf->getConfig() }]
[{ assign var="currency"  value=$oEmailView->getCurrency() }]
[{ assign var="user"      value=$oEmailView->getUser() }]
[{ assign var="basket"    value=$order->getBasket() }]
[{ assign var="oDelSet"   value=$order->getDelSet() }]
[{ assign var="payment"   value=$order->getPayment() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxordersubject->value}]

        [{block name="email_html_order_owner_orderemail"}]
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                [{if $payment->oxuserpayments__oxpaymentsid->value == "oxempty"}]
                    [{oxcontent ident="oxadminordernpemail"}]
                [{else}]
                    [{oxcontent ident="oxadminorderemail"}]
                [{/if}]

                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ORDERNOMBER" }] <b>[{ $order->oxorder__oxordernr->value }]</b>
            </p>
        [{/block}]

            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td height="15" width="100" style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PRODUCT" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        &nbsp;
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_UNITPRICE" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_QUANTITY" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_VAT" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTAL" }]</b></p>
                    </td>
                </tr>
                [{assign var="basketitemlist" value=$basket->getBasketArticles() }]
                [{foreach key=basketindex from=$basket->getContents() item=basketitem}]
                    [{block name="email_html_order_owner_basketitem"}]
                        [{assign var="basketproduct" value=$basketitemlist.$basketindex }]
                        <tr valign="top">
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <img src="[{$basketproduct->getThumbnailUrl(false) }]" border="0" hspace="0" vspace="0" alt="[{$basketitem->getTitle()|strip_tags}]" align="texttop">
                                [{if $oViewConf->getShowGiftWrapping() }]
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                                    [{assign var="oWrapping" value=$basketitem->getWrapping() }]
                                    <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_WRAPPING" }]&nbsp;</b>[{ if !$basketitem->getWrappingId() }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_NONE" }][{else}][{$oWrapping->oxwrapping__oxname->value}][{/if}]
                                </p>
                                [{/if}]
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
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
                                </p>
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                                    <b>[{if $basketitem->getFUnitPrice() }][{ $basketitem->getFUnitPrice() }] [{ $currency->sign}][{/if}]</b>
                                    [{if !$basketitem->isBundle() }]
                                        [{assign var=dRegUnitPrice value=$basketitem->getRegularUnitPrice()}]
                                        [{assign var=dUnitPrice value=$basketitem->getUnitPrice()}]
                                        [{if $dRegUnitPrice->getPrice() > $dUnitPrice->getPrice() }]
                                        <br><s>[{ $basketitem->getFRegularUnitPrice() }]&nbsp;[{ $currency->sign}]</s>
                                        [{/if}]
                                    [{/if}]
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
                                </p>
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                                    [{$basketitem->getAmount()}]
                                </p>
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                                    [{$basketitem->getVatPercent() }]%
                                </p>
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                                    <b>[{ $basketitem->getFTotalPrice() }] [{ $currency->sign}]</b>
                                </p>
                            </td>
                        </tr>
                    [{/block}]
                [{/foreach}]
            </table>
            <br>

            [{block name="email_html_order_owner_giftwrapping"}]
                [{if $basket->oCard }]
                    <table border="0" cellspacing="0" cellpadding="2" width="100%">
                        <tr valign="top">
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                    <b>[{ oxmultilang ident="EMAIL_ORDER_OWNER_HTML_ATENTIONGREETINGCARD" }]</b><br>
                                    <img src="[{$basket->oCard->getPictureUrl()}]" alt="[{$basket->oCard->oxwrapping__oxname->value}]" hspace="0" vspace="0" border="0" align="top">
                                </p>
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_YOURMESSAGE" }]
                                    <br><br>
                                    [{$basket->getCardMessage()}]
                                </p>
                            </td>
                        </tr>
                    </table>
                    <br>
                [{/if}]
            [{/block}]

            <table border="0" cellspacing="0" cellpadding="2" width="100%">
                <tr>
                    <td width="50%" valign="top">
                        [{block name="email_html_order_owner_voucherdiscount"}]
                            [{if $oViewConf->getShowVouchers() }]
                                <table border="0" cellspacing="0" cellpadding="0">
                                    [{if $basket->getVoucherDiscValue() }]
                                    <tr valign="top">
                                        <td style="padding: 5px 20px 5px 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;  color: #555;">
                                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_USEDCOUPONS" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px 20px 5px 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;  color: #555;">
                                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_REBATE" }]
                                            </p>
                                        </td>
                                    </tr>
                                    [{/if}]
                                    [{ foreach from=$order->getVoucherList() item=voucher}]
                                    [{ assign var="voucherseries" value=$voucher->getSerie() }]
                                    <tr valign="top">
                                        <td style="padding: 5px 20px 5px 5px;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{$voucher->oxvouchers__oxvouchernr->value}]
                                            </p>
                                        </td>
                                        <td style="padding: 5px 20px 5px 5px;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{$voucherseries->oxvoucherseries__oxdiscount->value}] [{ if $voucherseries->oxvoucherseries__oxdiscounttype->value == "absolute"}][{ $currency->sign}][{else}]%[{/if}]
                                            </p>
                                        </td>
                                    </tr>
                                    [{/foreach }]
                                </table>
                            [{/if}]
                        [{/block}]
                    </td>
                    <td width="50%" valign="top">
                        <table border="0" cellspacing="0" cellpadding="2" width="300">
                            [{if !$basket->getDiscounts() }]
                                [{block name="email_html_order_owner_nodiscounttotalnet"}]
                                    <!-- netto price -->
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALNET" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right" width="60">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getProductsNetPrice() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/block}]
                                [{block name="email_html_order_owner_nodiscountproductvats"}]
                                    <!-- VATs -->
                                    [{foreach from=$basket->getProductVats() item=VATitem key=key}]
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX1" }] [{ $key }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX2" }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ $VATitem }] [{ $currency->sign}]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/foreach}]
                                [{/block}]

                                [{block name="email_html_order_owner_nodiscounttotalgross"}]
                                    <!-- brutto price -->
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALGROSS" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getFProductsPrice() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/block}]

                            [{/if}]

                            <!-- applied discounts -->
                            [{ if $basket->getDiscounts()}]

                                [{if $order->isNettoMode() }]
                                    [{block name="email_html_order_cust_discounttotalnet"}]
                                    <!-- netto price -->
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALNET" }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right" width="60">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ $basket->getProductsNetPrice() }] [{ $currency->sign}]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/block}]
                                [{else}]
                                    [{block name="email_html_order_cust_discounttotalgross"}]
                                        <!-- brutto price -->
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALGROSS" }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ $basket->getFProductsPrice() }] [{ $currency->sign}]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/block}]
                                [{/if}]



                                [{block name="email_html_order_owner_discounts"}]
                                    <!-- discounts -->
                                    [{foreach from=$basket->getDiscounts() item=oDiscount}]
                                        <tr valign="top">
                                             <td style="padding: 5px; border-bottom: 1px solid #ddd;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{if $oDiscount->dDiscount < 0 }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_CHARGE" }][{else}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_DICOUNT" }][{/if}] <em>[{ $oDiscount->sDiscount }]</em> :
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 1px solid #ddd;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{if $oDiscount->dDiscount < 0 }][{ $oDiscount->fDiscount|replace:"-":"" }][{else}]-[{ $oDiscount->fDiscount }][{/if}] [{ $currency->sign}]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/foreach}]
                                [{/block}]

                                [{ if !$order->isNettoMode() }]
                                [{block name="email_html_order_cust_totalnet"}]
                                    <!-- netto price -->
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 1px solid #ddd;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALNET" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 1px solid #ddd;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getProductsNetPrice() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/block}]
                                [{/if}]

                                [{block name="email_html_order_owner_productvats"}]
                                    <!-- VATs -->
                                    [{foreach from=$basket->getProductVats() item=VATitem key=key}]
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX1" }] [{ $key }][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PLUSTAX2" }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ $VATitem }] [{ $currency->sign}]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/foreach}]
                                [{/block}]

                                [{ if $order->isNettoMode() }]
                                [{block name="email_html_order_cust_totalbrut"}]
                                    <!-- brutto price -->
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TOTALGROSS" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getFProductsPrice() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/block}]
                                [{/if}]

                            [{/if}]

                            [{block name="email_html_order_owner_voucherdiscount"}]
                                <!-- voucher discounts -->
                                [{if $oViewConf->getShowVouchers() && $basket->getVoucherDiscValue() }]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_COUPON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ if $basket->getFVoucherDiscountValue() > 0 }]-[{/if}][{ $basket->getFVoucherDiscountValue()|replace:"-":"" }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                            [{/block}]

                            [{block name="email_html_order_owner_delcosts"}]
                            <!-- delivery costs -->
                            [{if $basket->getDelCostNet() }]
                                <tr valign="top">
                                    <td style="padding: 5px; border-bottom: 1px solid #ccc;">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGNET" }]
                                        </p>
                                    </td>
                                    <td style="padding: 5px; border-bottom: 1px solid #ccc;" align="right">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{ $basket->getDelCostNet() }] [{ $currency->sign}]
                                        </p>
                                    </td>
                                </tr>
                                [{if $basket->getDelCostVat() }]
                                    <tr valign="top">
                                        [{if $basket->isProportionalCalculationOn() }]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" }]:
                                                </p>
                                            </td>
                                        [{else}]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="SHIPPING_VAT1" }] [{ $basket->getDelCostVatPercent() }][{ oxmultilang ident="SHIPPING_VAT2" }]
                                                </p>
                                            </td>
                                        [{/if}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ddd;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getDelCostVat() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if }]
                            [{elseif $basket->getFDeliveryCosts() }]
                                <tr valign="top">
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{ oxmultilang ident="SHIPPING_COST" }]:
                                        </p>
                                    </td>
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{ $basket->getFDeliveryCosts() }] [{ $currency->sign}]
                                        </p>
                                    </td>
                                </tr>
                            [{/if }]
                        [{/block}]

                        [{block name="email_html_order_owner_paymentcosts"}]
                            <!-- payment sum -->
                        [{ if $basket->getPayCostNet() }]
                            <tr valign="top">
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;[{ if $basket->getDelCostVat() }]border-bottom: 1px solid #ddd;[{/if}]">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{if $basket->getPaymentCosts() >= 0}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT1" }][{else}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT2" }][{/if}] [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT3" }]
                                    </p>
                                </td>
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;[{ if $basket->getDelCostVat() }]border-bottom: 1px solid #ddd;[{/if}]" align="right">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{ $basket->getPayCostNet() }] [{ $currency->sign}]
                                    </p>
                                </td>
                            </tr>
                            <!-- payment sum VAT (if available) -->
                            [{ if $basket->getPayCostVat() }]
                                <tr valign="top">
                                    [{if $basket->isProportionalCalculationOn() }]
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" }]:
                                            </p>
                                        </td>
                                    [{else}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="SHIPPING_VAT1" }] [{ $basket->getPayCostVatPercent() }][{ oxmultilang ident="SHIPPING_VAT2" }]
                                            </p>
                                        </td>
                                    [{/if}]
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{ $basket->getPayCostVat() }] [{ $currency->sign}]
                                        </p>
                                    </td>
                                </tr>
                            [{/if}]
                        [{elseif $basket->getFPaymentCosts() }]
                            <tr valign="top">
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{ oxmultilang ident="SURCHARGE" }]:
                                    </p>
                                </td>
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{ $basket->getFPaymentCosts() }] [{ $currency->sign}]
                                    </p>
                                </td>
                            </tr>
                        [{/if}]
                        [{/block}]

                        [{block name="email_html_order_owner_ts"}]
                            [{ if $basket->getTsProtectionCosts() }]
                                <!-- Trusted Shops -->
                                <tr valign="top">
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;[{ if $basket->getTsProtectionVat() }]border-bottom: 1px solid #ddd;[{/if}]">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_TSPROTECTION" }]:
                                        </p>
                                    </td>
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;[{ if $basket->getTsProtectionVat() }]border-bottom: 1px solid #ddd;[{/if}]" align="right">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{ $basket->getTsProtectionNet() }] [{ $currency->sign}]
                                        </p>
                                    </td>
                                </tr>
                                [{ if $basket->getTsProtectionVat() }]
                                    <tr valign="top">
                                        [{if $basket->isProportionalCalculationOn() }]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" }]:
                                                </p>
                                            </td>
                                        [{else}]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="SHIPPING_VAT1" }] [{ $basket->getTsProtectionVatPercent() }][{ oxmultilang ident="SHIPPING_VAT2" }]
                                                </p>
                                            </td>
                                        [{/if}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getTsProtectionVat() }]&nbsp;[{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                            [{/if}]
                        [{/block}]

                        [{ if $oViewConf->getShowGiftWrapping() }]
                            [{block name="email_html_order_owner_wrappingcosts"}]
                                <!-- Gift wrapping -->
                                [{if $basket->getWrappCostNet() }]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="BASKET_TOTAL_WRAPPING_COSTS_NET" }]:
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getWrappCostNet() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                    [{if $basket->getWrappCostVat() }]
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="BASKET_TOTAL_PLUS_VAT" }]:
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ $basket->getWrappCostVat() }] [{ $currency->sign}]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/if}]
                                [{elseif $basket->getFWrappingCosts() }]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="BASKET_TOTAL_WRAPPING_COSTS" }]:
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getFWrappingCosts() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                            [{/block}]

                            [{block name="email_html_order_owner_giftwrapping"}]
                                <!-- Greeting card -->
                                [{if $basket->getGiftCardCostNet() }]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="BASKET_TOTAL_GIFTCARD_COSTS_NET" }]:
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getGiftCardCostNet() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                    [{if $basket->getGiftCardCostVat() }]
                                    <tr>
                                        [{if $basket->isProportionalCalculationOn() }]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" }]:
                                                </p>
                                            </td>
                                        [{else}]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{ oxmultilang ident="SHIPPING_VAT1" }] [{ $basket->getGiftCardCostVatPercent() }][{ oxmultilang ident="SHIPPING_VAT2" }]
                                                </p>
                                            </td>
                                        [{/if}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getGiftCardCostVat() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                    [{/if}]
                                [{elseif $basket->getFGiftCardCosts() }]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ oxmultilang ident="BASKET_TOTAL_GIFTCARD_COSTS" }]:
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{ $basket->getFGiftCardCosts() }] [{ $currency->sign}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                            [{/block}]
                        [{/if}]

                        [{block name="email_html_order_owner_grandtotal"}]
                            <!-- grand total price -->
                            <tr valign="top">
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_GRANDTOTAL" }]</b>
                                    </p>
                                </td>
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        <b>[{ $basket->getFPrice() }] [{ $currency->sign}]</b>
                                    </p>
                                </td>
                            </tr>
                        [{/block}]
                        </table>
                    </td>
                </tr>
            </table>

            [{block name="email_html_order_owner_userremark"}]
                [{ if $order->oxorder__oxremark->value }]
                    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                        [{ oxmultilang ident="EMAIL_ORDER_OWNER_HTML_MESSAGE" }]
                    </h3>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                        [{ $order->oxorder__oxremark->value|oxescape }]
                    </p>
                [{/if}]
            [{/block}]

            [{block name="email_html_order_owner_paymentinfo"}]
                [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}]
                    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                        [{ oxmultilang ident="EMAIL_ORDER_OWNER_HTML_PAYMENTINFO" }]
                    </h3>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                        <b>[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTMETHOD" }] [{ $payment->oxpayments__oxdesc->value }] [{ if $basket->getPaymentCosts() }]([{ $basket->getFPaymentCosts() }] [{ $currency->sign}])[{/if}]</b>
                        <br><br>
                        [{ oxmultilang ident="EMAIL_ORDER_OWNER_HTML_PAYMENTINFOOFF" }]
                    </p>
                [{/if}]
            [{/block}]

            [{block name="email_html_order_owner_username"}]
                <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_EMAILADDRESS" }]
                </h3>
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                    [{ $user->oxuser__oxusername->value }]
                </p>
            [{/block}]

            [{block name="email_html_order_owner_address"}]
                <!-- Address info -->
                <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ADDRESS" }]
                </h3>

                <table colspan="0" rowspan="0" border="0">
                    <tr valign="top">
                        <td style="padding-right: 30xp">
                            <h4 style="font-weight: bold; margin: 0; padding: 0 0 15px; line-height: 20px; font-size: 11px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase;">
                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BILLINGADDRESS" }]
                            </h4>
                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                [{ $order->oxorder__oxbillcompany->value }]<br>
                                [{ $order->oxorder__oxbillsal->value|oxmultilangsal}] [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }]<br>
                                [{if $order->oxorder__oxbilladdinfo->value }][{ $order->oxorder__oxbilladdinfo->value }]<br>[{/if}]
                                [{ $order->oxorder__oxbillstreet->value }] [{ $order->oxorder__oxbillstreetnr->value }]<br>
                                [{ $order->oxorder__oxbillstateid->value }]
                                [{ $order->oxorder__oxbillzip->value }] [{ $order->oxorder__oxbillcity->value }]<br>
                                [{ $order->oxorder__oxbillcountry->value }]<br>
                                [{if $order->oxorder__oxbillustid->value}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_VATIDNOMBER" }] [{ $order->oxorder__oxbillustid->value }]<br>[{/if}]
                                [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PHONE" }] [{ $order->oxorder__oxbillfon->value }]<br><br>
                            </p>
                        </td>
                        [{ if $order->oxorder__oxdellname->value }]
                            <td>
                                <h4 style="font-weight: bold; margin: 0; padding: 0 0 15px; line-height: 20px; font-size: 11px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase;">
                                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGADDRESS" }]
                                </h4>
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                    [{ $order->oxorder__oxdelcompany->value }]<br>
                                    [{ $order->oxorder__oxdelsal->value|oxmultilangsal }] [{ $order->oxorder__oxdelfname->value }] [{ $order->oxorder__oxdellname->value }]<br>
                                    [{if $order->oxorder__oxdeladdinfo->value }][{ $order->oxorder__oxdeladdinfo->value }]<br>[{/if}]
                                    [{ $order->oxorder__oxdelstreet->value }] [{ $order->oxorder__oxdelstreetnr->value }]<br>
                                    [{ $order->oxorder__oxdelstateid->value }]
                                    [{ $order->oxorder__oxdelzip->value }] [{ $order->oxorder__oxdelcity->value }]<br>
                                    [{ $order->oxorder__oxdelcountry->value }]
                                </p>
                            </td>
                        [{/if}]
                    </tr>
                </table>
            [{/block}]

            [{block name="email_html_order_owner_deliveryinfo"}]
                [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}]
                    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                    [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER" }]
                    </h3>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                        [{ $oDelSet->oxdeliveryset__oxtitle->value }]
                    </p>
                [{/if}]
            [{/block}]

[{include file="email/html/footer.tpl"}]
