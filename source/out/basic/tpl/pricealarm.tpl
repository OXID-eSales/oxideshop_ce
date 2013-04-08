[{assign var="template_title" value="PRICEALARM_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]
[{assign var="product" value=$oView->getProduct()}]
[{assign var="currency" value=$oView->getActCurrency() }]

<strong id="test_priceAlarmHeader" class="boxhead">[{$template_title}]</strong>
<div class="box info">
    [{if $oView->getPriceAlarmStatus() == 1}]
      [{ oxmultilang ident="PRICEALARM_THANKYOUMESSAGE1" }] [{ $oxcmp_shop->oxshops__oxname->value }] [{ oxmultilang ident="PRICEALARM_THANKYOUMESSAGE2" }]<br><br>
      [{ oxmultilang ident="PRICEALARM_THANKYOUMESSAGE3" }] [{ $product->oxarticles__oxtitle->value }][{if $product->oxarticles__oxvarselect->value}] [{ $product->oxarticles__oxvarselect->value }][{/if}] [{ oxmultilang ident="PRICEALARM_THANKYOUMESSAGE4" }] [{ $oView->getBidPrice()}] [{ $currency->sign}] [{ oxmultilang ident="PRICEALARM_THANKYOUMESSAGE5" }]<br><br>
    [{elseif $oView->getPriceAlarmStatus() == 2}]
      [{ oxmultilang ident="PRICEALARM_WRONGVERIFICATIONCODE" }]<br><br>
    [{else}]
      [{ oxmultilang ident="PRICEALARM_NOTABLETOSENDEMAIL" }] <br>
      [{ oxmultilang ident="PRICEALARM_VERIFYYOUREMAIL" }]<br><br>
    [{/if}]
    <a href="[{ $product->getLink()|oxaddparams:$oViewConf->getNavUrlParams() }]"><b>[{ oxmultilang ident="PRICEALARM_BACKTOPRODUCT" }]</b></a>
</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
