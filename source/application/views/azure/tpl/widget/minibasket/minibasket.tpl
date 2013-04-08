[{oxscript include="js/widgets/oxcountdown.js" priority=10 }]
[{oxscript include="js/widgets/oxminibasket.js" priority=10 }]
[{oxscript include="js/widgets/oxmodalpopup.js" priority=10}]
[{oxscript add="$( '#miniBasket' ).oxMiniBasket();"}]
[{if $oxcmp_basket->getProductsCount() gte 8}]
    [{oxscript include="js/libs/scrollpane/jscrollpane.min.js"}]
    [{oxscript include="js/libs/scrollpane/mousewheel.js"}]
    [{oxscript include="js/libs/scrollpane/mwheelIntent.js"}]
    [{oxstyle include="css/libs/jscrollpane.css"}]
[{/if}]

[{block name="widget_minibasket"}]
    <div id="[{$_prefix}]miniBasket" class="basketBox">


        [{if $_prefix != "modal"}]
            [{if $oxcmp_basket->getItemsCount() > 0}]
                <span class="counter FXgradOrange">
                    [{insert name="oxid_newbasketitem" tpl="widget/minibasket/newbasketitemmsg.tpl" type="message"}]
                    <span id="[{$_prefix}]countValue">
                        [{$oxcmp_basket->getItemsCount()}]
                    </span>
                </span>
            [{/if}]
            <img src="[{$oViewConf->getImageUrl('basket.png')}]" id="[{$_prefix}]minibasketIcon" alt="Basket" class="basket">
        [{/if}]
    </div>

    [{if $oxcmp_basket->getProductsCount()}]
            [{oxhasrights ident="TOBASKET"}]
            [{if $oxcmp_basket->getProductsCount() gte 8}]
                [{assign var="scrollableBasket" value=true}]
            [{/if}]
                [{assign var="currency" value=$oView->getActCurrency() }]
                <div id="[{$_prefix}]basketFlyout" class="basketFlyout corners[{if $scrollableBasket}] scrollable[{/if}]">
                    <p class="title">
                        [{if $_prefix != "modal"}]
                            <strong>[{$oxcmp_basket->getItemsCount()}] [{ oxmultilang ident="WIDGET_MINIBASKET_ITEMS_IN_BASKET" }]</strong>
                        [{else}]
                            <strong class="note">[{ oxmultilang ident="WIDGET_NEWBASKETITEMMSG" }]</strong>
                        [{/if}]
                        <img src="[{$oViewConf->getImageUrl('x.png')}]" alt="" class="closePop">
                    </p>
                    [{if $_prefix != "modal"}]
                        [{if $oxcmp_basket->getItemsCount() > 0}]
                            <span class="counter FXgradOrange">
                                [{insert name="oxid_newbasketitem" tpl="widget/minibasket/newbasketitemmsg.tpl" type="message"}]
                                <span id="[{$_prefix}]countValue">
                                    [{$oxcmp_basket->getItemsCount()}]
                                </span>
                            </span>
                        [{/if}]
                        <img src="[{$oViewConf->getImageUrl('basket.png')}]" id="[{$_prefix}]minibasketIcon" alt="Basket" class="basket">
                    [{/if}]

                    [{if $scrollableBasket}]
                        <div class="scrollbarBox">
                        <div class="basketItems">
                        <hr>
                    [{/if}]
                    <ul>
                    [{foreach from=$oxcmp_basket->getContents() name=miniBasketList item=_product}]
                        [{block name="widget_minibasket_product"}]
                            [{ assign var="minibasketItemTitle" value=$_product->getTitle() }]
                            <li>
                                <a href="[{$_product->getLink()}]" title="[{ $minibasketItemTitle|strip_tags }]">
                                    <span class="item">
                                        [{if $_product->getAmount() gt 1}]
                                            [{$_product->getAmount()}] x
                                        [{/if}]
                                        [{ $minibasketItemTitle|strip_tags }]
                                    </span>
                                    <strong class="price">[{$_product->getFTotalPrice()}] [{ $currency->sign}]</strong>
                                </a>
                            </li>
                        [{/block}]
                    [{/foreach}]
                    </ul>
                    [{if $scrollableBasket}]
                        </div>
                        </div>
                        <hr>
                    [{/if}]
                    [{block name="widget_minibasket_total"}]
                        <p class="totals">
                           <span class="item">[{ oxmultilang ident="WIDGET_MINIBASKET_TOTAL" }]</span>
                           <strong class="price">
                           [{if $oxcmp_basket->isPriceViewModeNetto()}]
                           [{ $oxcmp_basket->getProductsNetPrice()}]
                           [{else}]
                           [{ $oxcmp_basket->getFProductsPrice()}]
                           [{/if}]
                           [{ $currency->sign}]</strong>
                        </p>
                    [{/block}]
                    <hr>
                    [{include file="widget/minibasket/countdown.tpl"}]
                    <p class="functions clear">
                       [{if $oxcmp_user}]
                            <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=payment" }]" class="submitButton largeButton">[{ oxmultilang ident="WIDGET_MINIBASKET_CHECKOUT" }]</a>
                       [{else}]
                            <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=user" }]" class="submitButton largeButton">[{ oxmultilang ident="WIDGET_MINIBASKET_CHECKOUT" }]</a>
                       [{/if}]
                       <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=basket" }]" class="textButton">[{ oxmultilang ident="WIDGET_MINIBASKET_DISPLAY_BASKET" }]</a>
                    </p>
                </div>
            [{/oxhasrights}]
        [{/if}]
[{/block}]
[{oxstyle widget=$oView->getClassName()}]