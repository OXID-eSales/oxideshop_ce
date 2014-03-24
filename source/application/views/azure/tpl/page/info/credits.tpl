[{capture append="oxidBlock_content"}]
    [{ assign var="oContent" value=$oView->getCreditsCmsPage() }]
    [{ assign var="template_title" value=$oContent->oxcontents__oxtitle->value }]
    <h1 class="pageHead">[{$template_title}]</h1>
    <div class="cmsContent">
        [{ $oContent->oxcontents__oxcontent->value }]
        <br>
        [{ assign var="oContributors" value=$oView->getGithubContributors() }]
        [{ if $oContributors }]
            [{ foreach from=$oContributors item=contributor }]
                <a href="[{ $contributor->html_url }]" target="_blank">
                    <img src="[{ $contributor->avatar_url }]" border="0" alt="[{ $contributor->login }]" width="25" style="float: left; margin-right: 10px;">
                    <b>[{ $contributor->login }]</b> ([{ $contributor->contributions }])
                </a><br><br>
            [{ /foreach}]
        [{ /if }]
    </div>
    [{ insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]
