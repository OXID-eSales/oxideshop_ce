[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="TAGS"|oxmultilangassign }]
    [{if $oView->getTagCloudManager() }]
        <h1 class="pageHead" id="tags">[{ oxmultilang ident="TAGS"}]</h1>
        <div >
            <p id="tagsCloud">
                [{assign var="oCloudManager" value=$oView->getTagCloudManager() }]
                [{foreach from=$oCloudManager->getCloudArray() item=iCount key=sTagTitle}]
                    <a class="tagitem_[{$oCloudManager->getTagSize($sTagTitle)}]" href="[{$oCloudManager->getTagLink($sTagTitle)}]">[{$oCloudManager->getTagTitle($sTagTitle)}]</a>
                [{/foreach}]
            </p>
        </div>
    [{/if}]
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]