[{oxscript add="$( 'a.js-amountPriceSelector' ).oxAmountPriceSelect();"}]
<a class="selector corners FXgradBlueDark js-amountPriceSelector" href="#priceinfo" id="amountPrice" rel="nofollow"><img src="[{$oViewConf->getImageUrl('selectbutton.png')}]" alt="Select"></a>
[{assign var="currency" value=$oView->getActCurrency()}]
<ul class="pricePopup corners shadow" id="priceinfo">
<li><span><h4>[{oxmultilang ident="BLOCK_PRICE" suffix="COLON"}]</h4></span></li>
<li><label>[{oxmultilang ident="FROM"}]</label><span>[{oxmultilang ident="PCS"}]</span></li>
[{foreach from=$oDetailsProduct->loadAmountPriceInfo() item=priceItem name=amountPrice}]
    <li>
        <label>[{$priceItem->oxprice2article__oxamount->value}]</label>
        <span>
        [{if $priceItem->oxprice2article__oxaddperc->value}]
            [{$priceItem->oxprice2article__oxaddperc->value}] % [{oxmultilang ident="DISCOUNT"}]
        [{else}]
            [{$priceItem->fbrutprice}] [{$currency->sign}]
        [{/if}]
        </span>
    </li>
[{/foreach}]
</ul>
