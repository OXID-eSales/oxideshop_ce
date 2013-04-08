<dl>
    <dt>
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
        <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}]</b></label>
    </dt>
    <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
        [{ if $paymentmethod->getPrice() }]
            [{if $oxcmp_basket->getPayCostNet() }]
                [{ $paymentmethod->getFNettoPrice() }] [{ $currency->sign}] [{ oxmultilang ident="PLUS_VAT" }] [{ $paymentmethod->getFPriceVat() }]
            [{else}]
                [{ $paymentmethod->getFBruttoPrice() }] [{ $currency->sign}]
            [{/if}]
        [{/if}]
        [{ oxmultilang ident="COD_CHARGE" }]

        [{block name="checkout_payment_longdesc"}]
            [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                <div class="desc">
                    [{ $paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                </div>
            [{/if}]
        [{/block}]
    </dd>
</dl>