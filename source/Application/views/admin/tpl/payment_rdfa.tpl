[{if $shopid != "1" || $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{assign var="aAllRDFaPayments" value=$oView->getAllRDFaPayments()}]
<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="payment_rdfa">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="payment_rdfa">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxobject2payment__oxpaymentid]" value="[{$oxid}]">
<input type="hidden" name="editval[oxobject2payment__oxtype]" value="rdfapayment">


<strong>[{oxmultilang ident="PAYMENT_RDFA_ASIGN_PAYMENT"}]</strong><br>
    [{assign var='oxDesc' value=$edit->oxpayments__oxdesc->value}][{oxmultilang ident="PAYMENT_RDFA_ADVICE" args=$oxDesc}].
<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">
        <b>[{oxmultilang ident="PAYMENT_RDFA_GENERAL"}]</b>
        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_payment_main_form"}]
            [{foreach key=key item=oPayment from=$aAllRDFaPayments}]
                [{assign var="name" value=$oPayment->name}]
                [{assign var="ident" value=PAYMENT_RDFA_$name}]
                [{assign var="ident" value=$ident|oxupper}]
                [{if $oPayment->type == 0}]
                <tr>
                    <td class="edittext" width="70">
                    [{oxmultilang ident=$ident}]
                    </td>
                    <td class="edittext">
                    <input type="checkbox" class="edittext" name="ardfapayments[]" value="[{$oPayment->name}]" [{if $oPayment->checked}]checked[{/if}] [{$readonly}]>
                    </td>
                </tr>
                [{/if}]
            [{/foreach}]
        [{/block}]
        </table>
    </td>
    <td valign="top" class="edittext" align="left" width="50%">
        <b>[{oxmultilang ident="PAYMENT_RDFA_CREDITCARD"}]</b>
        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_payment_main_form"}]
            [{foreach key=key item=oPayment from=$aAllRDFaPayments}]
                [{assign var="name" value=$oPayment->name}]
                [{assign var="ident" value=PAYMENT_RDFA_$name}]
                [{assign var="ident" value=$ident|oxupper}]
                [{if $oPayment->type == 1}]
                <tr>
                    <td class="edittext" width="70">
                    [{oxmultilang ident=$ident}]
                    </td>
                    <td class="edittext">
                    <input type="checkbox" class="edittext" name="ardfapayments[]" value="[{$oPayment->name}]" [{if $oPayment->checked}]checked[{/if}] [{$readonly}]>
                    </td>
                </tr>
                [{/if}]
            [{/foreach}]
        [{/block}]
        </table>
    </td>

    </tr>
</table>

<input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]