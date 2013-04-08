[{block name="checkout_payment_trustedshops"}]
    [{if $oView->getTSExcellenceId()}]
        <div id="tsBox">
            <h3 class="blockHead" id="tsProtectionHeader">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTION" }]</h3>
            <div class="etrustlogocol">
            <a href="https://www.trustedshops.com/shop/certificate.php?shop_id=[{$oView->getTSExcellenceId()}]" target="_blank">
              <img src="[{$oViewConf->getImageUrl('trustedshops_m.gif')}]" title="[{ oxmultilang ident="INC_TRUSTEDSHOPS_ITEM_IMGTITLE" }]">
            </a>
            </div>
            <div>
            <input type="checkbox" name="bltsprotection" value="1" [{if $oView->getCheckedTsProductId()}]checked[{/if}]>
            [{assign var="aTsProtections" value=$oView->getTsProtections() }]
            [{if count($aTsProtections) > 1 }]
            <select name="stsprotection">
              [{foreach from=$aTsProtections item=oTsProduct}]
                  [{if $oView->isPaymentVatSplitted() }]
                     <option value="[{$oTsProduct->getTsId()}]" [{if $oView->getCheckedTsProductId() == $oTsProduct->getTsId()}]SELECTED[{/if}]>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONFOR" }] [{ $oTsProduct->getAmount() }] [{ $currency->sign}] ([{ $oTsProduct->getFNettoPrice() }] [{ $currency->sign}] [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PLUSTAX1" }] [{ $oTsProduct->getFVatValue() }] [{ $currency->sign}] ) </option>
                  [{else}]
                      <option value="[{$oTsProduct->getTsId()}]" [{if $oView->getCheckedTsProductId() == $oTsProduct->getTsId()}]SELECTED[{/if}]>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONFOR" }] [{ $oTsProduct->getAmount() }] [{ $currency->sign}] ([{ $oTsProduct->getFPrice() }] [{ $currency->sign}] [{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_INCLUDEVAT" }]) </option>
                  [{/if}]
              [{/foreach}]
            </select>
            [{else}]
                [{assign var="oTsProduct" value=$aTsProtections[0] }]
                [{if $oView->isPaymentVatSplitted() }]
                <input type="hidden" name="stsprotection" value="[{$oTsProduct->getTsId()}]">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONFOR" }] [{ $oTsProduct->getAmount() }] [{ $currency->sign}] ([{ $oTsProduct->getFNettoPrice() }] [{ $currency->sign}] [{ oxmultilang ident="PAGE_CHECKOUT_BASKETCONTENTS_PLUSTAX1" }] [{ $oTsProduct->getFVatValue() }] [{ $currency->sign}])
                [{else}]
                <input type="hidden" name="stsprotection" value="[{$oTsProduct->getTsId()}]">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONFOR" }] [{ $oTsProduct->getAmount() }] [{ $currency->sign}] ([{ $oTsProduct->getFPrice() }] [{ $currency->sign}] [{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_INCLUDEVAT" }])
                [{/if}]
            [{/if}]
              <br>
              <br>
            [{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT" }] <a href="http://www.trustedshops.com/shop/data_privacy.php?shop_id=[{$oView->getTSExcellenceId()}]" target="_blank">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT2" }]</a>
            [{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT3" }] <a href="http://www.trustedshops.com/shop/protection_conditions.php?shop_id=[{$oView->getTSExcellenceId()}]" target="_blank">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT4" }]</a> [{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT5" }]
            </div>
            <div class="clear"></div>
        </div>
    [{/if}]
[{/block}]