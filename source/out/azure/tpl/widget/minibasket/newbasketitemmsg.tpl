[{if $oxcmp_basket->getProductsCount() && $_newitem}]
[{oxhasrights ident="TOBASKET"}]
    <span id="newItemMsg">[{ oxmultilang ident="WIDGET_NEWBASKETITEMMSG" }]</span>
[{/oxhasrights}]
[{/if}]