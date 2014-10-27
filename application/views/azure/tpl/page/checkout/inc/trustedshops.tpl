[{block name="checkout_payment_trustedshops"}]
    [{if $oView->getTSExcellenceId()}]
        <div id="tsBox">
            <h3 class="blockHead" id="tsProtectionHeader">[{ oxmultilang ident="TRUSTED_SHOP_BUYER_PROTECTION" }]</h3>
            <div class="etrustlogocol">
            <a href="https://www.trustedshops.com/shop/certificate.php?shop_id=[{$oView->getTSExcellenceId()}]" target="_blank">
              <img src="[{$oViewConf->getImageUrl('trustedshops_m.gif')}]" title="[{oxmultilang ident="TRUSTED_SHOPS_IMGTITLE"}]">
            </a>
            </div>
            <div>
            <input type="checkbox" name="bltsprotection" value="1" [{if $oView->getCheckedTsProductId()}]checked[{/if}]>
            [{assign var="aTsProtections" value=$oView->getTsProtections() }]
            [{if count($aTsProtections) > 1 }]
            <select name="stsprotection">
              [{foreach from=$aTsProtections item=oTsProduct}]
                  [{assign var='oTsProductPrice' value=$oTsProduct->getPrice() }]
                  [{if $oView->isPaymentVatSplitted() }]
                     <option value="[{$oTsProduct->getTsId()}]" [{if $oView->getCheckedTsProductId() == $oTsProduct->getTsId()}]SELECTED[{/if}]>[{oxmultilang ident="TRUSTED_SHOP_PROTECTION_FROM"}] [{oxprice price=$oTsProduct->getAmount() currency=$currency}] ([{oxprice price=$oTsProductPrice->getNettoPrice() currency=$currency}] [{oxmultilang ident="PLUS_VAT"}] [{oxprice price=$oTsProductPrice->getVatValue() currency=$currency}] ) </option>
                  [{else}]
                      <option value="[{$oTsProduct->getTsId()}]" [{if $oView->getCheckedTsProductId() == $oTsProduct->getTsId()}]SELECTED[{/if}]>[{oxmultilang ident="TRUSTED_SHOP_PROTECTION_FROM"}] [{oxprice price=$oTsProduct->getAmount() currency=$currency}] ([{oxprice price=$oTsProductPrice->getBruttoPrice() currency=$currency}] [{oxmultilang ident="INCLUDE_VAT"}]) </option>
                  [{/if}]
              [{/foreach}]
            </select>
            [{else}]
                [{assign var="oTsProduct" value=$aTsProtections[0] }]
                [{assign var="oTsProductPrice" value=$oTsProduct->getPrice() }]
                [{if $oView->isPaymentVatSplitted() }]
                <input type="hidden" name="stsprotection" value="[{$oTsProduct->getTsId()}]">[{oxmultilang ident="TRUSTED_SHOP_PROTECTION_FROM"}] [{oxprice price=$oTsProduct->getAmount() currency=$currency}] ([{oxprice price=$oTsProductPrice->getNettoPrice() currency=$currency}] [{oxmultilang ident="PLUS_VAT"}] [{oxprice price=$oTsProductPrice->getVatValue() currency=$currency}])
                [{else}]
                <input type="hidden" name="stsprotection" value="[{$oTsProduct->getTsId()}]">[{oxmultilang ident="TRUSTED_SHOP_PROTECTION_FROM"}] [{oxprice price=$oTsProduct->getAmount() currency=$currency}] ([{oxprice price=$oTsProductPrice->getBruttoPrice() currency=$currency}] [{oxmultilang ident="INCLUDE_VAT"}])
                [{/if}]
            [{/if}]
              <br>
              <br>
                <div class="cmsContent">
                    [{oxifcontent ident="oxtsprotectiontext" object="oCont"}]
                        [{$oCont->oxcontents__oxcontent->value}]
                    [{/oxifcontent}]
                </div>
            </div>
            <div class="clear"></div>
        </div>
    [{/if}]
[{/block}]