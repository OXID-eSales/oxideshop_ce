[{if $oView->isRootCatChanged() && $oxcmp_basket->getContents()}]
<div id="popup" class="popup">

    <strong>[{ oxmultilang ident="BASKET_EXCLUDE_HEAD" }]</strong>
    <p><b class="err">[{ oxmultilang ident="BASKET_EXCLUDE_MSG" }]</b></p>
    <p>[{ oxmultilang ident="BASKET_EXCLUDE_INFO" }]</p>

    <form action="[{ $oViewConf->getCurrentHomeDir() }]index.php" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
        <input type="hidden" name="fnc" value="executeuserchoice">
        <input type="hidden" name="tpl" value="[{$oViewConf->getActTplName()}]">
        [{if $oView->getArticleId()}]
          <input type="hidden" name="aid" value="[{$oView->getArticleId()}]">
        [{/if}]
        [{if $oView->getProduct()}]
          [{assign var="product" value=$oView->getProduct() }]
          <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
        [{/if}]
        [{oxhasrights ident="TOBASKET"}]
        <input type="submit" class="bl" name="tobasket" value="[{ oxmultilang ident="BASKET_POPUP_FULL_DISPLAYCART" }]" onclick="oxid.popup.hide();">
        [{/oxhasrights}]
        <input type="submit" class="br" value="[{ oxmultilang ident="BASKET_POPUP_FULL_CONTINUESHOPPING" }]" onclick="oxid.popup.hide();">
    </div>
    </form>
</div>
[{oxscript add="oxid.popup.show();" }]
[{/if}]
