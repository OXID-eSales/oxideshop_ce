<div id="wait" class="popup">
    <strong>[{ oxmultilang ident="BASKET_POPUP_ADDEDARTICLETOBASKET" }]</strong>
    <form action="[{ $oViewConf->getCurrentHomeDir() }]index.php" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="basket">
        <input type="hidden" name="redirected" value="1">
        [{oxhasrights ident="TOBASKET"}]
        <input id="test_popupCart" type="submit" class="bl" value="[{ oxmultilang ident="BASKET_POPUP_FULL_DISPLAYCART" }]" onclick="if (oxid.popup) {oxid.popup.hide();}">
        [{/oxhasrights}]
        <input id="test_popupContinue" type="button" class="br disabled" value="[{ oxmultilang ident="BASKET_POPUP_FULL_CONTINUESHOPPING" }]" onclick="oxid.popup.hide();return false;" disabled="disabled">
    </div>
    </form>
</div>

[{ if $oxcmp_basket->getProductsCount() && ( $_newitem || $Errors.popup )}]
[{assign var="currency" value=$oView->getActCurrency() }]
<div id="popup" class="popup">
    [{if $Errors.popup}]
        [{foreach from=$Errors.popup item=oEr }]
            <strong class="err">[{ $oEr->getOxMessage() }]</strong>
        [{/foreach}]
    [{else}]
        <strong>[{ oxmultilang ident="BASKET_POPUP_FULL_ADDEDARTICLETOBASKET" }]</strong>
    [{/if}]
    [{oxhasrights ident="TOBASKET"}]
    [{if $oxcmp_basket->getContents()}]
     <table summary="[{ oxmultilang ident="INC_HEADER_CART" }]" cellpadding="2" cellspacing="2">
      <tr>
         <th>[{ oxmultilang ident="INC_CMP_BASKET_PRODUCT" }]</th>
         <td>[{ $oxcmp_basket->getProductsCount()}]</td>
      </tr>
      <tr [{if !$oxcmp_basket->getDeliveryCosts()}]class="sep"[{/if}]>
         <th>[{ oxmultilang ident="INC_CMP_BASKET_QUANTITY" }]</th>
         <td>[{ $oxcmp_basket->getItemsCount()}]</td>
      </tr>
      [{if $oxcmp_basket->getDeliveryCosts()}]
      <tr>
         <th>[{ oxmultilang ident="INC_CMP_BASKET_SHIPPING" }]</th>
         <td>[{ $oxcmp_basket->getFDeliveryCosts() }] [{ $currency->sign}]</td>
      </tr>
      [{/if}]
      <tr>
         <th>[{ oxmultilang ident="INC_CMP_BASKET_TOTALPRODUCTS" }]</th>
         <td>[{ $oxcmp_basket->getFProductsPrice()}] [{ $currency->sign}]</td>
      </tr>
     </table>
    [{/if}]
    [{/oxhasrights}]
    <form action="[{ $oViewConf->getCurrentHomeDir() }]index.php" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="basket">
        <input type="hidden" name="redirected" value="1">
        [{oxhasrights ident="TOBASKET"}]
        <input type="submit" class="bl" value="[{ oxmultilang ident="BASKET_POPUP_FULL_DISPLAYCART" }]" onclick="oxid.popup.hide();">
        [{/oxhasrights}]
        <input type="button" class="br" value="[{ oxmultilang ident="BASKET_POPUP_FULL_CONTINUESHOPPING" }]" onclick="oxid.popup.hide();return false;">
    </div>
    </form>
</div>
[{oxscript add="oxid.popup.show();" }]
[{/if }]
