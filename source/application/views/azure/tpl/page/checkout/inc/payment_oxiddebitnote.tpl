[{assign var="dynvalue" value=$oView->getDynValue()}]
[{assign var="iPayError" value=$oView->getPaymentError() }]
<dl>
    <dt>
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
        <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}]</b></label>
    </dt>
    <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
        <ul class="form">
            <li>
                <label>[{ oxmultilang ident="BANK" }]</label>
                <input id="payment_[{$sPaymentID}]_1" class="js-oxValidate js-oxValidate_notEmpty" type="text" size="20" maxlength="64" name="dynvalue[lsbankname]" autocomplete="off" value="[{ $dynvalue.lsbankname }]">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li [{if $iPayError == -4}]class="oxInValid"[{/if}]>
                <label>
                [{if $oView->isOldDebitValidationEnabled()}]
                    [{ oxmultilang ident="BANK_CODE" suffix="COLON" }]
                [{else}]
                    [{ oxmultilang ident="BIC" suffix="COLON" }]
                [{/if}]
                </label>
                <input type="text" class="js-oxValidate" size="20" maxlength="64" name="dynvalue[lsblz]" autocomplete="off" value="[{ $dynvalue.lsblz }]">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li [{if $iPayError == -5}]class="oxInValid"[{/if}]>
                <label>
                [{if $oView->isOldDebitValidationEnabled()}]
                    [{ oxmultilang ident="BANK_ACCOUNT_NUMBER" suffix="COLON" }]
                [{else}]
                    [{ oxmultilang ident="IBAN" suffix="COLON" }]
                [{/if}]
                </label>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" maxlength="64" name="dynvalue[lsktonr]" autocomplete="off" value="[{ $dynvalue.lsktonr }]">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="BANK_ACCOUNT_HOLDER" suffix="COLON" }]</label>
                <input type="text" class="js-oxValidate js-oxValidate_notEmpty" size="20" maxlength="64" name="dynvalue[lsktoinhaber]" value="[{if $dynvalue.lsktoinhaber}][{$dynvalue.lsktoinhaber}][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
        </ul>

        [{block name="checkout_payment_longdesc"}]
            [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                <div class="desc">
                    [{ $paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                </div>
            [{/if}]
        [{/block}]
    </dd>
</dl>