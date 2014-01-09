[{assign var="shop"      value=$oEmailView->getShop() }]
[{assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{assign var="currency"  value=$oEmailView->getCurrency() }]
[{assign var="user"      value=$oEmailView->getUser() }]
[{assign var="basket"    value=$order->getBasket() }]
[{assign var="oDelSet"   value=$order->getDelSet() }]
[{assign var="payment"   value=$order->getPayment() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxordersubject->value}]

        [{block name="email_html_order_owner_orderemail"}]
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                [{if $payment->oxuserpayments__oxpaymentsid->value == "oxempty"}]
                    [{oxcontent ident="oxadminordernpemail"}]
                [{else}]
                    [{oxcontent ident="oxadminorderemail"}]
                [{/if}]

                [{oxmultilang ident="ORDER_NUMBER" }] <b>[{ $order->oxorder__oxordernr->value }]</b>
            </p>
        [{/block}]

            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td height="15" width="100" style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{oxmultilang ident="PRODUCT" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        &nbsp;
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{oxmultilang ident="UNIT_PRICE" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{oxmultilang ident="QUANTITY" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{oxmultilang ident="VAT" }]</b></p>
                    </td>
                    <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><b>[{oxmultilang ident="TOTAL" }]</b></p>
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
                                    <b>[{oxmultilang ident="GIFT_WRAPPING" suffix="COLON" }]&nbsp;</b>[{if !$basketitem->getWrappingId() }][{oxmultilang ident="NONE" }][{else}][{$oWrapping->oxwrapping__oxname->value}][{/if}]
                                </p>
                                [{/if}]
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                                    <b>[{$basketitem->getTitle()}]</b>
                                    [{if $basketitem->getChosenSelList() }],
                                    [{foreach from=$basketitem->getChosenSelList() item=oList}]
                                    [{ $oList->name }] [{ $oList->value }]&nbsp;
                                    [{/foreach}]
                                    [{/if}]
                                    [{if $basketitem->getPersParams() }]
                                    [{foreach key=sVar from=$basketitem->getPersParams() item=aParam}]
                                    ,&nbsp;<em>[{$sVar}] : [{$aParam}]</em>
                                    [{/foreach}]
                                    [{/if}]
                                    <br>[{oxmultilang ident="PRODUCT_NO" suffix="COLON" }] [{ $basketproduct->oxarticles__oxartnum->value }]
                                </p>
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 10px 0;">
                                    <b>[{if $basketitem->getUnitPrice() }][{oxprice price=$basketitem->getUnitPrice() currency=$currency }][{/if}]</b>
                                    [{if !$basketitem->isBundle() }]
                                        [{assign var=dRegUnitPrice value=$basketitem->getRegularUnitPrice()}]
                                        [{assign var=dUnitPrice value=$basketitem->getUnitPrice()}]
                                        [{if $dRegUnitPrice->getPrice() > $dUnitPrice->getPrice() }]
                                        <br><s>[{oxprice price=$basketitem->getRegularUnitPrice() currency=$currency }]</s>
                                        [{/if}]
                                    [{/if}]
                                    [{if $basketitem->aDiscounts}]<br><br>
                                    <em style="font-size: 7pt;font-weight: normal;">[{oxmultilang ident="DISCOUNT" suffix="COLON" }]
                                        [{foreach from=$basketitem->aDiscounts item=oDiscount}]
                                        <br>[{ $oDiscount->sDiscount }]
                                        [{/foreach}]
                                    </em>
                                    [{/if}]
                                    [{if $basketproduct->oxarticles__oxorderinfo->value }]
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
                                    <b>[{oxprice price=$basketitem->getPrice() currency=$currency}]</b>
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
                                    <b>[{oxmultilang ident="ATENTION_GREETING_CARD" suffix='COLON' }]</b><br>
                                    <img src="[{$basket->oCard->getPictureUrl()}]" alt="[{$basket->oCard->oxwrapping__oxname->value}]" hspace="0" vspace="0" border="0" align="top">
                                </p>
                            </td>
                            <td style="padding: 5px; border-bottom: 4px solid #ddd;">
                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                    [{oxmultilang ident="WHAT_I_WANTED_TO_SAY" }]
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
                        [{block name="email_html_order_owner_voucherdiscount_top"}]
                                <table border="0" cellspacing="0" cellpadding="0">
                        [{if $oViewConf->getShowVouchers() && $basket->getVoucherDiscValue() }]
                                    <tr valign="top">
                                        <td style="padding: 5px 20px 5px 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;  color: #555;">
                                        <b>[{oxmultilang ident="USED_COUPONS_2" }]</b>
                                            </p>
                                        </td>
                                        <td style="padding: 5px 20px 5px 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;  color: #555;">
                                        <b>[{oxmultilang ident="REBATE" }]</b>
                                            </p>
                                        </td>
                                    </tr>
                                    [{foreach from=$order->getVoucherList() item=voucher}]
                                    [{assign var="voucherseries" value=$voucher->getSerie() }]
                                    <tr valign="top">
                                        <td style="padding: 5px 20px 5px 5px;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{$voucher->oxvouchers__oxvouchernr->value}]
                                            </p>
                                        </td>
                                        <td style="padding: 5px 20px 5px 5px;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{$voucherseries->oxvoucherseries__oxdiscount->value}] [{if $voucherseries->oxvoucherseries__oxdiscounttype->value == "absolute"}][{ $currency->sign}][{else}]%[{/if}]
                                            </p>
                                        </td>
                                    </tr>
                                    [{/foreach}]
                        [{/if}]
                                </table>
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
                                                [{oxmultilang ident="TOTAL_NET" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right" width="60">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$basket->getNettoSum() currency=$currency }]
                                            </p>
                                        </td>
                                    </tr>
                                [{/block}]
                                [{block name="email_html_order_owner_nodiscountproductvats"}]
                                    <!-- VATs -->
                            [{foreach from=$basket->getProductVats(false) item=VATitem key=key}]
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$key}]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$VATitem currency=$currency }]
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
                                                [{oxmultilang ident="TOTAL_GROSS" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$basket->getBruttoSum() currency=$currency }]
                                            </p>
                                        </td>
                                    </tr>
                                [{/block}]
                            [{/if}]
                            <!-- applied discounts -->
                            [{if $basket->getDiscounts()}]

                                [{if $order->isNettoMode() }]
                                    [{block name="email_html_order_cust_discounttotalnet"}]
                                    <!-- netto price -->
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="TOTAL_NET" suffix="COLON" }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right" width="60">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$basket->getNettoSum() currency=$currency }]
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
                                                    [{oxmultilang ident="TOTAL_GROSS" suffix="COLON" }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$basket->getBruttoSum() currency=$currency }]
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
                                                    [{if $oDiscount->dDiscount < 0 }][{oxmultilang ident="SURCHARGE" }][{else}][{oxmultilang ident="DISCOUNT" }][{/if}] <em>[{ $oDiscount->sDiscount }]</em> :
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 1px solid #ddd;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$oDiscount->dDiscount*-1 currency=$currency}]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/foreach}]
                                [{/block}]

                                [{if !$order->isNettoMode() }]
                                [{block name="email_html_order_cust_totalnet"}]
                                    <!-- netto price -->
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 1px solid #ddd;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="TOTAL_NET" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 1px solid #ddd;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$basket->getNettoSum() currency=$currency }]
                                            </p>
                                        </td>
                                    </tr>
                                [{/block}]
                                [{/if}]

                                [{block name="email_html_order_owner_productvats"}]
                                    <!-- VATs -->
                            [{foreach from=$basket->getProductVats(false) item=VATitem key=key}]
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$key }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$VATitem currency=$currency }]
                                                </p>
                                            </td>
                                        </tr>
                                    [{/foreach}]
                                [{/block}]

                                [{if $order->isNettoMode() }]
                                [{block name="email_html_order_cust_totalbrut"}]
                                    <!-- brutto price -->
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="TOTAL_GROSS" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$basket->getBruttoSum() currency=$currency }]
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
                                                [{oxmultilang ident="COUPON" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{assign var="oVoucherDiscount" value=$basket->getVoucherDiscount()}]
                                        [{oxprice price=$oVoucherDiscount->getBruttoPrice()*-1 currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                            [{/block}]

                            [{block name="email_html_order_owner_delcosts"}]
                            <!-- delivery costs -->
                    [{assign var="oDeliveryCost" value=$basket->getDeliveryCost()}]
                    [{if $oDeliveryCost && $oDeliveryCost->getPrice() > 0 }]
                        [{if $oViewConf->isFunctionalityEnabled('blShowVATForDelivery') }]
                                <tr valign="top">
                                    <td style="padding: 5px; border-bottom: 1px solid #ccc;">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxmultilang ident="SHIPPING_NET" suffix="COLON" }]
                                        </p>
                                    </td>
                                    <td style="padding: 5px; border-bottom: 1px solid #ccc;" align="right">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$oDeliveryCost->getNettoPrice() currency=$currency}]
                                        </p>
                                    </td>
                                </tr>
                                [{if $oDeliveryCost->getVatValue()}]
                                    <tr valign="top">
                                        [{if $basket->isProportionalCalculationOn() }]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON" }]
                                                </p>
                                            </td>
                                        [{else}]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$oDeliveryCost->getVat() }]
                                                </p>
                                            </td>
                                        [{/if}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ddd;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$oDeliveryCost->getVatValue() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                        [{else}]
                                <tr valign="top">
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxmultilang ident="SHIPPING_COST" suffix="COLON" }]
                                        </p>
                                    </td>
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$oDeliveryCost->getBruttoPrice() currency=$currency}]
                                        </p>
                                    </td>
                                </tr>
                            [{/if}]
                    [{/if}]
                        [{/block}]

                        [{block name="email_html_order_owner_paymentcosts"}]
                            <!-- payment sum -->
                    [{assign var="oPaymentCost" value=$basket->getPaymentCost()}]
                    [{if $oPaymentCost && $oPaymentCost->getPrice() }]
                        [{if $oViewConf->isFunctionalityEnabled('blShowVATForPayCharge') }]
                            <tr valign="top">
                                <td style="padding: 5px; border-bottom: 2px solid #ccc; border-bottom: 1px solid #ddd;">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{if $oPaymentCost->getPrice() >= 0}][{oxmultilang ident="SURCHARGE" }][{else}][{oxmultilang ident="DEDUCTION" }][{/if}] [{oxmultilang ident="PAYMENT_METHOD" suffix="COLON" }]
                                    </p>
                                </td>
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;[{if $basket->getDelCostVat() }]border-bottom: 1px solid #ddd;[{/if}]" align="right">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$oPaymentCost->getNettoPrice() currency=$currency}]
                                    </p>
                                </td>
                            </tr>
                                [{if $oPaymentCost->getVatValue()}]
                                    <tr valign="top">
                                        [{if $basket->isProportionalCalculationOn() }]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON" }]
                                                </p>
                                            </td>
                                        [{else}]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$oPaymentCost->getVat()}]
                                                </p>
                                            </td>
                                        [{/if}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$oPaymentCost->getVatValue() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                        [{else}]
                            <tr valign="top">
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxmultilang ident="SURCHARGE" suffix="COLON" }]
                                    </p>
                                </td>
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$oPaymentCost->getBruttoPrice() currency=$currency}]
                                    </p>
                                </td>
                            </tr>
                        [{/if}]
                    [{/if}]
                        [{/block}]

                        [{block name="email_html_order_owner_ts"}]
                        [{assign var="trustedShopProtectionCost" value=$basket->getTrustedShopProtectionCost()}]
                        [{if $trustedShopProtectionCost && $trustedShopProtectionCost->getPrice() > 0  }]
                                <!-- Trusted Shops -->
                                <tr valign="top">
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;[{if $basket->getTsProtectionVat() }]border-bottom: 1px solid #ddd;[{/if}]">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxmultilang ident="TRUSTED_SHOP_BUYER_PROTECTION" suffix="COLON" }]
                                        </p>
                                    </td>
                                    <td style="padding: 5px; border-bottom: 2px solid #ccc;[{if $basket->getTsProtectionVat() }]border-bottom: 1px solid #ddd;[{/if}]" align="right">
                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$trustedShopProtectionCost->getNettoPrice() currency=$currency}]
                                        </p>
                                    </td>
                                </tr>
                                [{if $trustedShopProtectionCost->getVatValue()}]
                                    <tr valign="top">
                                        [{if $basket->isProportionalCalculationOn() }]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON" }]
                                                </p>
                                            </td>
                                        [{else}]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" args=$trustedShopProtectionCost->getVat()}]
                                                </p>
                                            </td>
                                        [{/if}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        [{oxprice price=$trustedShopProtectionCost->getVatValue() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                        [{/if}]
                        [{/block}]

                        [{if $oViewConf->getShowGiftWrapping() }]
                            [{block name="email_html_order_owner_wrappingcosts"}]
                                <!-- Gift wrapping -->
                        [{assign var="wrappingCost" value=$basket->getWrappingCost()}]
                        [{if $wrappingCost && $wrappingCost->getPrice() > 0 }]
                            [{if $oViewConf->isFunctionalityEnabled('blShowVATForWrapping') }]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="BASKET_TOTAL_WRAPPING_COSTS_NET" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$wrappingCost->getNettoPrice() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                        <tr valign="top">
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="PLUS_VAT" suffix="COLON" }]
                                                </p>
                                            </td>
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$wrappingCost->getVatValue() currency=$currency}]
                                                </p>
                                            </td>
                                        </tr>
                            [{else}]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="GIFT_WRAPPING" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$wrappingCost->getBruttoPrice() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                        [{/if}]
                            [{/block}]
                            [{block name="email_html_order_owner_giftwrapping"}]
                                <!-- Greeting card -->
                        [{assign var="giftCardCost" value=$basket->getGiftCardCost()}]
                        [{if $giftCardCost && $giftCardCost->getPrice() > 0 }]
                            [{if $oViewConf->isFunctionalityEnabled('blShowVATForWrapping') }]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="BASKET_TOTAL_GIFTCARD_COSTS_NET" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 1px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$giftCardCost->getNettoPrice() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        [{if $basket->isProportionalCalculationOn() }]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                    [{oxmultilang ident="BASKET_TOTAL_PLUS_PROPORTIONAL_VAT" suffix="COLON" }]
                                                </p>
                                            </td>
                                        [{else}]
                                            <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="VAT_PLUS_PERCENT_AMOUNT" suffix="COLON" suffix="COLON" args=$giftCardCost->getVat()}]
                                                </p>
                                            </td>
                                        [{/if}]
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$giftCardCost->getVatValue() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                            [{else}]
                                    <tr valign="top">
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                                [{oxmultilang ident="GREETING_CARD" suffix="COLON" }]
                                            </p>
                                        </td>
                                        <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                            [{oxprice price=$giftCardCost->getBruttoPrice() currency=$currency}]
                                            </p>
                                        </td>
                                    </tr>
                                [{/if}]
                        [{/if}]
                            [{/block}]
                        [{/if}]

                        [{block name="email_html_order_owner_grandtotal"}]
                            <!-- grand total price -->
                            <tr valign="top">
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        <b>[{oxmultilang ident="GRAND_TOTAL" suffix="COLON" }]</b>
                                    </p>
                                </td>
                                <td style="padding: 5px; border-bottom: 2px solid #ccc;" align="right">
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                        <b>[{oxprice price=$basket->getPrice() currency=$currency}]</b>
                                    </p>
                                </td>
                            </tr>
                        [{/block}]
                        </table>
                    </td>
                </tr>
            </table>

            [{block name="email_html_order_owner_userremark"}]
                [{if $order->oxorder__oxremark->value }]
                    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                        [{oxmultilang ident="MESSAGE" suffix="COLON" }]
                    </h3>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                        [{ $order->oxorder__oxremark->value|oxescape }]
                    </p>
                [{/if}]
            [{/block}]

            [{block name="email_html_order_owner_paymentinfo"}]
                [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}]
                    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                        [{oxmultilang ident="PAYMENT_INFORMATION" suffix="COLON" }]
                    </h3>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                        <b>[{oxmultilang ident="PAYMENT_METHOD" suffix="COLON" }] [{ $payment->oxpayments__oxdesc->value }]
                        [{assign var="oPaymentCostPrice" value=$basket->getPaymentCost()}]
                        [{if $oPaymentCostPrice }]([{oxprice price=$oPaymentCostPrice->getBruttoPrice() currency=$currency}])[{/if}]</b>

                        <br><br>
                        [{oxmultilang ident="PAYMENT_INFO_OFF" }]
                    </p>
                [{/if}]
            [{/block}]

            [{block name="email_html_order_owner_username"}]
                <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                    [{oxmultilang ident="EMAIL_ADDRESS" suffix="COLON" }]
                </h3>
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                    [{ $user->oxuser__oxusername->value }]
                </p>
            [{/block}]

            [{block name="email_html_order_owner_address"}]
                <!-- Address info -->
                <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                    [{oxmultilang ident="ADDRESS" suffix="COLON" }]
                </h3>

                <table colspan="0" rowspan="0" border="0">
                    <tr valign="top">
                        <td style="padding-right: 30px">
                            <h4 style="font-weight: bold; margin: 0; padding: 0 0 15px; line-height: 20px; font-size: 11px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase;">
                                [{oxmultilang ident="BILLING_ADDRESS" suffix="COLON" }]
                            </h4>
                            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                                [{ $order->oxorder__oxbillcompany->value }]<br>
                                [{ $order->oxorder__oxbillsal->value|oxmultilangsal}] [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }]<br>
                                [{if $order->oxorder__oxbilladdinfo->value }][{ $order->oxorder__oxbilladdinfo->value }]<br>[{/if}]
                                [{ $order->oxorder__oxbillstreet->value }] [{ $order->oxorder__oxbillstreetnr->value }]<br>
                                [{ $order->oxorder__oxbillstateid->value }]
                                [{ $order->oxorder__oxbillzip->value }] [{ $order->oxorder__oxbillcity->value }]<br>
                                [{ $order->oxorder__oxbillcountry->value }]<br>
                                [{if $order->oxorder__oxbillustid->value}][{oxmultilang ident="VAT_ID_NUMBER" suffix="COLON" }] [{ $order->oxorder__oxbillustid->value }]<br>[{/if}]
                                [{oxmultilang ident="PHONE" suffix="COLON" }] [{ $order->oxorder__oxbillfon->value }]<br><br>
                            </p>
                        </td>
                        [{if $order->oxorder__oxdellname->value }]
                            <td>
                                <h4 style="font-weight: bold; margin: 0; padding: 0 0 15px; line-height: 20px; font-size: 11px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase;">
                                    [{oxmultilang ident="SHIPPING_ADDRESS" suffix="COLON" }]
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
                    [{oxmultilang ident="SELECTED_SHIPPING_CARRIER" suffix="COLON" }]
                    </h3>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                        [{ $oDelSet->oxdeliveryset__oxtitle->value }]
                    </p>
                [{/if}]
            [{/block}]

[{include file="email/html/footer.tpl"}]
