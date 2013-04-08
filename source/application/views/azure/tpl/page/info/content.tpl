[{capture append="oxidBlock_content"}]
    [{assign var="oContent" value=$oView->getContent()}]
    [{assign var="tpl" value=$oViewConf->getActTplName()}]
    [{assign var="oxloadid" value=$oViewConf->getActContentLoadId()}]
    [{assign var="template_title" value=$oView->getTitle()}]
    <h1 class="pageHead">[{$template_title}]</h1>
    <div class="cmsContent">
        [{$oView->getParsedContent()}]
    </div>
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]