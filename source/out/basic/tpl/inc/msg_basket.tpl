[{ if $oxcmp_basket->getProductsCount() && $_newitem}]
[{oxhasrights ident="TOBASKET"}]
    [{foreach from=$Errors.basket item=oEr key=key }]
        [{if $oEr->getErrorClassType() == 'oxOutOfStockException'}]
            <div class="error">[{ $oEr->getOxMessage() }]</div>
        [{/if}]
    [{/foreach}]

    <div class="msg">
        [{ oxmultilang ident="INC_NEWBASKETITEM_ADDEDTOBASKET1" }] [{$_newitem->sTitle}] [{ oxmultilang ident="INC_NEWBASKETITEM_ADDEDTOBASKET2" }] <a rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getBasketLink() }]">[{ oxmultilang ident="INC_NEWBASKETITEM_ADDEDTOBASKET3" }]</a>[{ oxmultilang ident="INC_NEWBASKETITEM_ADDEDTOBASKET4" }]
    </div>
[{/oxhasrights}]
[{/if }]