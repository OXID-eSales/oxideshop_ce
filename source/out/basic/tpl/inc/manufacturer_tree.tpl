[{strip}]
<ul [{if $class}]class="[{$class}]"[{/if}]>
    <li>
        [{assign var="_rootManufacturer" value=$oView->getRootManufacturer() }]
        <a id="test_leftRootManufacturer" href="[{if $_rootManufacturer}][{ $_rootManufacturer->getLink() }][{/if}]" class="root[{if $oView->getManufacturerId()}] exp[{/if}] [{if $oView->getManufacturerId()==$_rootManufacturer->getId()}]act[{/if}]">[{ $_rootManufacturer->oxmanufacturers__oxtitle->value }]</a>
        [{if $oView->getManufacturerId() }]
            <ul>
            [{foreach from=$tree item=oman key=mankey name=test_manufacturer }]
                <li><a id="test_BoxLeft_SubVend_[{$smarty.foreach.test_manufacturer.iteration}]" href="[{$oman->getLink()}]" class="[{if $oView->getManufacturerId()==$oman->getId()}]act[{/if}]">[{ $oman->oxmanufacturers__oxtitle->value }][{ if $oman->getNrOfArticles() > 0 }] ([{$oman->getNrOfArticles()}])[{/if}]</a></li>
            [{/foreach}]
            </ul>
        [{/if}]
    </li>
</ul>
[{/strip}]