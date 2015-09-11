[{assign var=aPaymentMethods value=$oView->getRDFaPaymentMethods()}]
[{foreach from=$aPaymentMethods item=oPaymentMethod}]
    [{if $oPaymentMethod->oxpayments__oxobjectid->value}]
        <div rel="gr:acceptedPaymentMethods" resource="http://purl.org/goodrelations/v1#[{$oPaymentMethod->oxpayments__oxobjectid->value}]"></div>
    [{else}]
        [{assign var=sContentName value=$oView->getRDFaPaymentChargeSpecLoc()}]
        [{oxifcontent ident=$sContentName object="oCont"}]
        <div rel="gr:acceptedPaymentMethods" resource="[{$oCont->getLink()}]#[{$oPaymentMethod->oxpayments__oxdesc->value|strip:''|cat:'_'|cat:$oPaymentMethod->oxpayments__oxid->value}]">
        </div>
        [{/oxifcontent}]
    [{/if}]
[{/foreach}]