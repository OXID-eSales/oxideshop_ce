[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="LISTMANIA"|oxmultilangassign }]
    [{if $oView->getActiveRecommList() }]
        [{assign var="_actvrecommlist" value=$oView->getActiveRecommList() }]
        [{assign var="recommendation_head" value=$_actvrecommlist->oxrecommlists__oxtitle->value}]

        <h1 class="pageHead">[{$recommendation_head}]</h1>
        <div class="listmaniaView">
            [{include file="form/recommendation_edit.tpl" actvrecommlist=$_actvrecommlist}]
        </div>
        [{if $oView->getArticleList()}]
            [{assign var="blEdit" value=true }]
            [{include file="widget/product/list.tpl" type="line" listId="recommendProductList" products=$oView->getArticleList() recommid=$_actvrecommlist->getId() removeFunction="removeArticle"}]
            [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
        [{/if}]
    [{/if}]
    [{ insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="recommendationlist"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
