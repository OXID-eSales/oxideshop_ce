[{capture append="oxidBlock_content"}]
    [{if $oView->getTagCloudManager() }]
        <h1 class="pageHead" id="tags">[{$oView->getTitle()}]</h1>
        <div >
            <p id="tagsCloud">
                [{assign var="oCloudManager" value=$oView->getTagCloudManager() }]
                [{assign var="oTagList" value=$oCloudManager->getTagList() }]
                [{assign var="oTagSet" value=$oTagList->get() }]
                [{foreach from=$oTagSet->get() item=oTag }]
                    <a class="tagitem_[{ $oCloudManager->getTagSize($oTag->getTitle()) }]" href="[{ $oTag->getLink() }]">[{ $oTag->getTitle() }]</a>
                [{/foreach}]
            </p>
        </div>
    [{/if}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]