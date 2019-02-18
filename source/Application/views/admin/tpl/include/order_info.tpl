<table border="0" cellspacing="0" cellpadding="0" id="order.info">
[{block name="admin_order_overview_info_items"}]
  [{if $edit->isNettoMode()}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_INETTO"}]</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedTotalNetSum()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_DISCOUNT"}]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>- [{$edit->getFormattedDiscount()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{foreach key=iVat from=$aProductVats item=dVatPrice}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_IVAT"}] ([{$iVat}]%)</td>
    <td class="edittext" align="right"><b>[{$dVatPrice}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{/foreach}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_IBRUTTO"}]</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedTotalBrutSum()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{else}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_IBRUTTO"}]</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedTotalBrutSum()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>

  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_DISCOUNT"}]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>- [{$edit->getFormattedDiscount()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_INETTO"}]</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedTotalNetSum()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{foreach key=iVat from=$aProductVats item=dVatPrice}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_IVAT"}] ([{$iVat}]%)</td>
    <td class="edittext" align="right"><b>[{$dVatPrice}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{/foreach}]
  [{/if}]
  [{if $edit->oxorder__oxvoucherdiscount->value}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_VOUCHERS"}]</td>
    <td class="edittext" align="right"><b>- [{$edit->getFormattedTotalVouchers()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{/if}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_DELIVERYCOST"}]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedeliveryCost()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_PAYCOST"}]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedPayCost()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{if $edit->oxorder__oxwrapcost->value}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_WRAPPING"}]&nbsp;[{if $wrapping}]([{$wrapping->oxwrapping__oxname->value}])[{/if}]&nbsp;</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedWrapCost()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{/if}]
  [{if $edit->oxorder__oxgiftcardcost->value}]
  <tr>
    <td class="edittext" height="15">[{oxmultilang ident="GENERAL_CARD"}]&nbsp;[{if $giftCard}]([{$giftCard->oxwrapping__oxname->value}])[{/if}]&nbsp;</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedGiftCardCost()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>
  [{/if}]
  [{/block}]
  [{block name="admin_order_overview_info_sumtotal"}]
  <tr>
    <td class="edittext" height="25">[{oxmultilang ident="GENERAL_SUMTOTAL"}]&nbsp;&nbsp;</td>
    <td class="edittext" align="right"><b>[{$edit->getFormattedTotalOrderSum()}]</b></td>
    <td class="edittext">&nbsp;<b>[{if $edit->oxorder__oxcurrency->value}] [{$edit->oxorder__oxcurrency->value}] [{else}] [{$currency->name}] [{/if}]</b></td>
  </tr>  
  [{/block}]
</table>
