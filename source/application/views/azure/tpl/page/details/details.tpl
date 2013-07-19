[{capture append="oxidBlock_content"}]

<div id="details">
    [{oxid_include_widget cl="oxwDetailsPage" _parent=$oView->getClassName() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() anid=$oViewConf->getActArticleId()}]
    [{ include file="page/details/inc/related_products.tpl" }]
</div>

    [{if $oView->getAlsoBoughtTheseProducts()}]
    [{include file="widget/product/list.tpl" type="grid" listId="alsoBought" header="light" head="CUSTOMERS_ALSO_BOUGHT"|oxmultilangassign|colon products=$oView->getAlsoBoughtTheseProducts()}]
    [{/if}]

[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
