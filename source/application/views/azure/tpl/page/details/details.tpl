[{capture append="oxidBlock_content"}]
    [{if $oxcmp_user}]
        [{assign var="force_sid" value=$oView->getSidForWidget()}]
    [{/if}]
    <div id="details_container">
        [{oxid_include_widget cl="oxwArticleDetails" _parent=$oView->getClassName() nocookie=1 force_sid=$force_sid _navurlparams=$oViewConf->getNavUrlParams() object=$oView->getProduct() anid=$oViewConf->getActArticleId() iPriceAlarmStatus=$oView->getPriceAlarmStatus() skipESIforUser=1}]
    </div>
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
