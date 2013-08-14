<div id="detailsMain">
    [{include file="page/details/inc/productmain.tpl"}]
</div>
<div id="detailsRelated" class="detailsRelated clear">
    <div class="relatedInfo[{if !$oView->getSimilarProducts() && !$oView->getCrossSelling() && !$oView->getAccessoires()}] relatedInfoFull[{/if}]">
        [{include file="page/details/inc/tabs.tpl"}]
       <!--
       separate logic here, different widget -->
       [{if $oView->getAlsoBoughtTheseProducts()}]
            [{include file="widget/product/list.tpl" type="grid" listId="alsoBought" header="light" head="CUSTOMERS_ALSO_BOUGHT"|oxmultilangassign|colon products=$oView->getAlsoBoughtTheseProducts()}]
        [{/if}]
        [{if $oView->isReviewActive() }]
        <div class="widgetBox reviews">
            <h4>[{oxmultilang ident="WRITE_PRODUCT_REVIEW"}]</h4>
            [{include file="widget/reviews/reviews.tpl"}]
        </div>
        [{/if}]
    </div>
    [{ include file="page/details/inc/related_products.tpl" }]
</div>
