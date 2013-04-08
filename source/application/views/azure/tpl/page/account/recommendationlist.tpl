[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="LISTMANIA"|oxmultilangassign }]
    [{assign var="_actvrecommlist" value=$oView->getActiveRecommList() }]
    <h1 class="pageHead">[{ oxmultilang ident="LISTMANIA" }]</h1>
    [{if  $oView->isSavedList()}]
        [{assign var="_statusMessage" value="LISTMANIA_LIST_SAVED"|oxmultilangassign}]
        [{include file="message/success.tpl" statusMessage=$_statusMessage}]
    [{/if}]
    [{block name="account_redommendationlist_content"}]
        <div class="listmaniaView clear">
            [{include file="form/recommendation_edit.tpl" actvrecommlist=$_actvrecommlist}]
        </div>
        [{if !$oView->getActiveRecommList() }]
            [{assign var="blEdit" value=true }]
            [{include file="page/recommendations/inc/list.tpl"}]
        [{/if}]
    [{/block}]
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="recommendationlist"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]

