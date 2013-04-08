[{if $oViewConf->getShowBasketTimeout()}]
    <p class="totals">
        <span class="item">
            [{ oxmultilang ident="WIDGET_COUNTDOWN_BASKET_TIMEOUT" }]
            [{counter name="mini_basket_countdown_nr" assign="countdown_nr"}]
        </span>
        <strong class="price" id="countdown">[{$oViewConf->getBasketTimeLeft()|oxformattime}]</strong>
    </p>
    <hr>
[{/if}]