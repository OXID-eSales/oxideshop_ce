<h3>[{ oxmultilang ident="FORM_PRIVATESALES_BASKET_EXCLUDE_HEAD" }]</h3>
[{ oxmultilang ident="FORM_PRIVATESALES_BASKET_EXCLUDE_MSG" }]<br><br>
<div class="introtext">
[{ oxmultilang ident="FORM_PRIVATESALES_BASKET_EXCLUDE_INFO" }]<br><br>
</div>
<form action="[{ $oViewConf->getCurrentHomeDir() }]index.php" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
        <input type="hidden" name="fnc" value="executeuserchoice">
        <input type="hidden" name="tpl" value="[{$oViewConf->getActTplName()}]">
        <input type="hidden" name="oxloadid" value="[{$oViewConf->getActContentLoadId()}]">
        [{if $oView->getArticleId()}]
          <input type="hidden" name="aid" value="[{$oView->getArticleId()}]">
        [{/if}]
        [{if $oView->getProduct()}]
          [{assign var="product" value=$oView->getProduct() }]
          <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
        [{/if}]
        [{oxhasrights ident="TOBASKET"}]
        <button name="tobasket" value="1" class="submitButton" type="submit">[{ oxmultilang ident="FORM_PRIVATESALES_BASKET_EXCLUDE_DISPLAYCART" }]</button>
        [{/oxhasrights}]
        <button class="submitButton" type="submit">[{ oxmultilang ident="FORM_PRIVATESALES_BASKET_EXCLUDE_CONTINUESHOPPING" }]</button>
    </div>
</form>

