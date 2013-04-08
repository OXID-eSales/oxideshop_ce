
<div class="ordersteps[{if !$oViewConf->showFinalStep() }] nofinalstep[{/if}]">

    [{if $oxcmp_basket->getProductsCount() }]
        [{assign var=showStepLinks value=true}]
    [{/if}]

    <dl[{ if $highlight == 1}] class="active"[{/if}]>
        <dt>
            [{if $showStepLinks}]<a id="test_Step1" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getBasketLink() }]">[{/if}]
            <big>1.</big> [{ oxmultilang ident="INC_STEPS_ITEM_BASKET" }]
            [{if $showStepLinks}]</a>[{/if}]
        </dt>
        <dd>
            [{if $showStepLinks}]<a id="test_Step1_Text" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getBasketLink() }]">[{/if}][{ oxmultilang ident="INC_STEPS_ITEM_CHANGEITEMS" }][{if $showStepLinks}]</a>[{/if}]
        </dd>
    </dl>

    [{assign var=showStepLinks value=false}]
    [{if !$oView->isLowOrderPrice() && $oxcmp_basket->getProductsCount() }]
        [{assign var=showStepLinks value=true}]
    [{/if}]

    <dl[{ if $highlight == 2}] class="active"[{/if}]>
        <dt>
            [{if $showStepLinks}]<a id="test_Step2" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getOrderLink() }]">[{/if}]
            <big>2.</big> [{ oxmultilang ident="INC_STEPS_ITEM_SEND" }]
            [{if $showStepLinks}]</a>[{/if}]
        </dt>
        <dd>
            [{if $showStepLinks}]<a id="test_Step2_Text" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getOrderLink() }]">[{/if}][{ oxmultilang ident="INC_STEPS_ITEM_LOGINSELECTBILLINGINFO" }][{if $showStepLinks}]</a>[{/if}]
        </dd>
    </dl>

    [{assign var=showStepLinks value=false}]
    [{if $highlight != 1 && $oxcmp_user && !$oView->isLowOrderPrice() && $oxcmp_basket->getProductsCount() }]
        [{assign var=showStepLinks value=true}]
    [{/if}]

    <dl[{ if $highlight == 3}] class="active"[{/if}]>
        <dt>
            [{if $showStepLinks}]<a id="test_Step3" rel="nofollow" href="[{ if $oViewConf->getActiveClassName() == "user"}]javascript:document.getElementById('test_UserNextStepTop').click();[{else}][{ oxgetseourl ident=$oViewConf->getPaymentLink() }][{/if}]">[{/if}]
            <big>3.</big> [{ oxmultilang ident="INC_STEPS_ITEM_PAY" }]
            [{if $showStepLinks}]
                </a>
                
            [{/if}]            
        </dt>
        <dd>
            [{if $showStepLinks}]<a id="test_Step3_Text" rel="nofollow" href="[{ if $oViewConf->getActiveClassName() == "user"}]javascript:document.getElementById('test_UserNextStepTop').click();[{else}][{ oxgetseourl ident=$oViewConf->getPaymentLink() }][{/if}]">[{/if}][{ oxmultilang ident="INC_STEPS_ITEM_SELECTSHIPPINGANDPAYMENT" }][{if $showStepLinks}]</a>[{/if}]
        </dd>
    </dl>

    [{assign var=showStepLinks value=false}]
    [{if $highlight != 1 && $oxcmp_user && $oxcmp_basket->getProductsCount() && $oView->getPaymentList() && !$oView->isLowOrderPrice()}]
        [{assign var=showStepLinks value=true}]
    [{/if}]

    <dl class="[{ if $highlight == 4}]active[{/if}][{if !$oViewConf->showFinalStep() }] lastinrow[{/if}]">
        <dt>
            [{if $showStepLinks}]<a id="test_Step4" rel="nofollow" href="[{ if $oViewConf->getActiveClassName() == "payment"}]javascript:document.forms.order.submit();[{else}][{ oxgetseourl ident=$oViewConf->getOrderConfirmLink() }][{/if}]">[{/if}]
            <big>4.</big> [{ oxmultilang ident="INC_STEPS_ITEM_ORDER" }]
            [{if $showStepLinks}]</a>[{/if}]
        </dt>
        <dd>
            [{if $showStepLinks}]<a id="test_Step4_Text" rel="nofollow" href="[{ if $oViewConf->getActiveClassName() == "payment"}]javascript:document.forms.order.submit();[{else}][{ oxgetseourl ident=$oViewConf->getOrderConfirmLink() }][{/if}]">[{/if}][{ oxmultilang ident="INC_STEPS_ITEM_SUBMITORDER" }][{if $showStepLinks}]</a>[{/if}]
        </dd>
    </dl>

    [{if $oViewConf->showFinalStep() }]
    <dl class="lastinrow[{ if $highlight == 5}] active[{/if}]">
        <dt>
            <big>5.</big> [{ oxmultilang ident="INC_STEPS_ITEM_LASTSTEP1" }]
        </dt>
        <dd>
            [{ oxmultilang ident="INC_STEPS_ITEM_LASTSTEP2" }]
        </dd>
    </dl>
    [{/if}]

</div>