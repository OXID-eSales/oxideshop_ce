[{assign var='oTagsManager' value=$oView->getTagCloudManager()}]

[{if $oView->getTagCloudManager() }]

    [{if $oView->displayInBox() }]
        [{* Display tags in separate box *}]
        <div id="tagBox" class="box tagCloud">
            <h3>[{ oxmultilang ident="TAGS" }]</h3>
            <div class="content">
    [{else}]
        <div class="categoryTagsBox">
            <h3>[{ oxmultilang ident="TAGS" }]</h3>
            <div class="categoryTags">
    [{/if}]
    [{foreach from=$oTagsManager->getCloudArray() item=iCount key=sTagTitle }]
        <a class="tagitem_[{ $oTagsManager->getTagSize($sTagTitle) }]" href="[{ $oTagsManager->getTagLink($sTagTitle) }]">[{ $oTagsManager->getTagTitle($sTagTitle) }]</a>
    [{/foreach}]
    [{if $oView->isMoreTagsVisible()}]
        <br>
        <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=tags" }]" class="readMore">[{ oxmultilang ident="MORE" suffix="ELLIPSIS" }]</a>
    [{/if}]
        </div>
    </div>
[{/if}]
