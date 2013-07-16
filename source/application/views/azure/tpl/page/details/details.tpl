[{capture append="oxidBlock_content"}]

    [{oxid_include_widget cl="oxwDetailsPage" oParentView=$oView _parent=$oView->getClassName() force_sid=$force_sid nocookie=$blAnon _navurlparams=$oViewConf->getNavUrlParams() anid=$oViewConf->getActArticleId()}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
