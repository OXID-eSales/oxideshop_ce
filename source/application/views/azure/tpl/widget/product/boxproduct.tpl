[{assign var="_oBoxProduct" value=$oView->getBoxProduct()}]
[{assign var="_sTitle" value="`$_oBoxProduct->oxarticles__oxtitle->value` `$_oBoxProduct->oxarticles__oxvarselect->value`"|strip_tags}]
[{block name="widget_product_boxproduct_image"}]
    <li class="articleImage" [{if !$iProdCount}] style="display:none;" [{/if}]>
    <a class="articleBoxImage" href="[{ $_oBoxProduct->getMainLink() }]">
        <img src="[{$_oBoxProduct->getIconUrl()}]" alt="[{$_sTitle}]">
    </a>
    </li>
[{/block}]

[{block name="widget_product_boxproduct_price"}]
    <li class="articleTitle">
        <a href="[{ $_oBoxProduct->getMainLink() }]">
            [{ $_sTitle }]<br>
            [{oxhasrights ident="SHOWARTICLEPRICE"}]
            [{if $_oBoxProduct->getFPrice()}]
            <strong> [{if $_oBoxProduct->isRangePrice()}]
                [{ oxmultilang ident="PRICE_FROM" }]
                [{if !$_oBoxProduct->isParentNotBuyable() }]
                [{ $_oBoxProduct->getFMinPrice() }]
                [{else}]
                [{ $_oBoxProduct->getFVarMinPrice() }]
                [{/if}]
                [{else}]
                [{if !$_oBoxProduct->isParentNotBuyable() }]
                [{ $_oBoxProduct->getFPrice() }]
                [{else}]
                [{ $_oBoxProduct->getFVarMinPrice() }]
                [{/if}]
                [{/if}]
                [{ $currency->sign}]
                        [{if $isVatIncluded }]
                [{if !( $_oBoxProduct->hasMdVariants() || ($oViewConf->showSelectListsInList()&&$_oBoxProduct->getSelections(1)) || $_oBoxProduct->getVariants() )}]*[{/if}]
                        [{/if}]
                    </strong>
            [{/if}]
            [{/oxhasrights}]
        </a>
    </li>
[{/block}]

[{oxscript widget=$oView->getClassName()}]