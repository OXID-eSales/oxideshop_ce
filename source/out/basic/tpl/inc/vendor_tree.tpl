[{strip}]
<ul [{if $class}]class="[{$class}]"[{/if}]>
    <li>
        [{assign var="_rootvendor" value=$oView->getRootVendor() }]
        <a id="test_leftRootVendor" href="[{if $_rootvendor}][{ $_rootvendor->getLink() }][{/if}]" class="root[{if $oView->getVendorId()}] exp[{/if}] [{if $oView->getVendorId()==$_rootvendor->getId()}]act[{/if}]">[{ $_rootvendor->oxvendor__oxtitle->value }]</a>
        [{if $oView->getVendorId() }]
            <ul>
            [{foreach from=$tree item=ovnd key=vndkey name=test_vendor }]
                <li><a id="test_BoxLeft_SubVend_[{$smarty.foreach.test_vendor.iteration}]" href="[{$ovnd->getLink()}]" class="[{if $oView->getVendorId()==$ovnd->getId()}]act last[{/if}]">[{ $ovnd->oxvendor__oxtitle->value }][{ if $ovnd->getNrOfArticles() > 0 }] ([{$ovnd->getNrOfArticles()}])[{/if}]</a></li>
            [{/foreach}]
            </ul>
        [{/if}]
    </li>
</ul>
[{/strip}]