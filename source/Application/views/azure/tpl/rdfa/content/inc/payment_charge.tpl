[{foreach from=$oView->getNotMappedToRDFaPayments() item=oNewPaymentMethod}]
    <div about="[{$sRDFaUrl}]#[{$oNewPaymentMethod->oxpayments__oxdesc->value|strip:''|cat:'_'|cat:$oNewPaymentMethod->oxpayments__oxid->value}]" typeof="gr:PaymentMethod">
        <div property="rdfs:label" content="[{$oNewPaymentMethod->oxpayments__oxdesc->value}]"></div>
        [{if $oNewPaymentMethod->oxpayments__oxlongdesc->value != ""}]
            <div property="rdfs:comment" content="[{$oNewPaymentMethod->oxpayments__oxlongdesc->getRawValue()|strip_tags|strip}]"></div>
        [{/if}]
    </div>
[{/foreach}]
