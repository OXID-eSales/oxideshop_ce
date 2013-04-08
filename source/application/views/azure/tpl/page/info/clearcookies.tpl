[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="INFO_ABOUT_COOKIES"|oxmultilangassign}]
    <h1 class="pageHead">[{ oxmultilang ident="INFO_ABOUT_COOKIES" }]</h1>
    <div class="cmsContent">
        <p>
            [{ oxmultilang ident="COOKIES_EXPLANATION" }]
            [{ insert name="oxid_tracker" title=$template_title }]
        </p>
    </div>
[{/capture}]
[{include file="layout/page.tpl"}]