[{assign var="oContent" value=$oView->getContent()}]
[{assign var="template_title" value=$oView->getTitle()}]
[{assign var="tpl" value=$oViewConf->getActTplName()}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

    <h1 id="test_contentHeader" class="boxhead">[{$template_title}]</h1>
    <div id="test_contentBody" class="box">[{$oView->getParsedContent()}]</div>

[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]