[{if $oView->getTagCloudManager()}]

    [{if $oView->displayInBox()}]
        [{* Display tags in separate box *}]
        <nav id="tagBox" class="box tagCloud">
            <h3>[{oxmultilang ident="TAGS"}]</h3>
            <div class="content">
    [{else}]
        <nav class="categoryTagsBox">
            <h3>[{oxmultilang ident="TAGS"}]</h3>
            <div class="categoryTags">
    [{/if}]
    [{assign var="oCloudManager" value=$oView->getTagCloudManager()}]
    [{assign var="oTagSet" value=$oCloudManager->getCloudArray()}]
    [{foreach from=$oTagSet item=oTag}]
        <a class="tagitem_[{$oCloudManager->getTagSize($oTag->getTitle())}]" href="[{$oTag->getLink()}]">[{$oTag->getTitle()}]</a>
    [{/foreach}]
    [{if $oView->isMoreTagsVisible()}]
        <br>
        <a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=tags"}]" class="readMore">[{oxmultilang ident="MORE" suffix="ELLIPSIS"}]</a>
    [{/if}]
        </div>
    </nav>
[{/if}]
