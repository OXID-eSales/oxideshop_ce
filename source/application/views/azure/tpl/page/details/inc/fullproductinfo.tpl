<div id="detailsMain">
    [{include file="page/details/inc/productmain.tpl"}]
</div>
<div id="detailsRelated" class="detailsRelated clear">
    <div class="relatedInfo[{if !$oView->getSimilarProducts() && !$oView->getCrossSelling() && !$oView->getAccessoires()}] relatedInfoFull[{/if}]">
        [{include file="page/details/inc/tabs.tpl"}]
        [{if $oView->getAlsoBoughtTheseProducts()}]
            [{include file="widget/product/list.tpl" type="grid" listId="alsoBought" header="light" head="CUSTOMERS_ALSO_BOUGHT"|oxmultilangassign|colon products=$oView->getAlsoBoughtTheseProducts()}]
        [{/if}]
        [{if $oView->isReviewActive() }]
        <div class="widgetBox reviews">
            <h4>[{oxmultilang ident="WRITE_PRODUCT_REVIEW"}]</h4>
            [{assign var="product" value=$oView->getProduct()}]
            [{if $oxcmp_user}]
                [{assign var="force_sid" value=$oView->getSidForWidget()}]
            [{/if}]
            [{oxid_include_widget cl="oxwReview" nocookie=1 force_sid=$force_sid _parent=$oViewConf->getTopActiveClassName() type=oxarticle anid=$product->oxarticles__oxnid->value aid=$product->oxarticles__oxid->value canrate=$oView->canRate() skipESIforUser=1}]
        </div>
        [{/if}]
    </div>
    [{ include file="page/details/inc/related_products.tpl" }]
</div>
