[{assign var="_oBoxProduct" value=$oView->getProduct()}]
[{assign var="_sTitle" value="`$_oBoxProduct->oxarticles__oxtitle->value` `$_oBoxProduct->oxarticles__oxvarselect->value`"|strip_tags}]
[{block name="widget_product_boxproduct_image"}]
    <li class="articleImage" [{if !$iProdCount}] style="display:none;" [{/if}]>
        <a class="articleBoxImage" href="[{$_oBoxProduct->getMainLink()}]">
            <img src="[{$_oBoxProduct->getIconUrl()}]" alt="[{$_sTitle}]">
        </a>
    </li>
[{/block}]

[{block name="widget_product_boxproduct_price"}]
    <li class="articleTitle">
        <a href="[{$_oBoxProduct->getMainLink()}]">
            [{$_sTitle}]<br>
            [{oxhasrights ident="SHOWARTICLEPRICE"}]
                [{block name="widget_product_boxproduct_price_value"}]
                    [{if $_oBoxProduct->getPrice()}]
                        <strong> [{if $_oBoxProduct->isRangePrice()}]
                            [{oxmultilang ident="PRICE_FROM"}]
                            [{if !$_oBoxProduct->isParentNotBuyable()}]
                                [{assign var="oPrice" value=$_oBoxProduct->getMinPrice()}]
                            [{else}]
                                [{assign var="oPrice" value=$_oBoxProduct->getVarMinPrice()}]
                            [{/if}]
                            [{else}]
                            [{if !$_oBoxProduct->isParentNotBuyable()}]
                                [{assign var="oPrice" value=$_oBoxProduct->getPrice()}]
                            [{else}]
                                [{assign var="oPrice" value=$_oBoxProduct->getVarMinPrice()}]
                            [{/if}]
                            [{/if}]
                            [{oxprice price=$oPrice currency=$oView->getActCurrency()}]
                            [{if $oView->isVatIncluded()}]
                                [{if !( $_oBoxProduct->getVariantsCount() || $_oBoxProduct->hasMdVariants() || ($oViewConf->showSelectListsInList()&&$_oBoxProduct->getSelections(1)) )}]*[{/if}]
                            [{/if}]
                        </strong>
                    [{/if}]
                [{/block}]
            [{/oxhasrights}]
        </a>
    </li>
[{/block}]

[{oxscript widget=$oView->getClassName()}]