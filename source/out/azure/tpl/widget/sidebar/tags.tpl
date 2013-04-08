[{if $oView->showTags()}]
    <div id="tagBox" class="box tagCloud">
        <h3>[{ oxmultilang ident="WIDGET_TAGS_HEADER" }]</h3>
        <div class="content">
            [{foreach from=$oTagsManager->getCloudArray() item=iCount key=sTagTitle }]
                <a class="tagitem_[{ $oTagsManager->getTagSize($sTagTitle) }]" href="[{ $oTagsManager->getTagLink($sTagTitle) }]">[{ $oTagsManager->getTagTitle($sTagTitle) }]</a>
            [{/foreach}]
            [{if $oView->isMoreTagsVisible()}]
                <br>
                <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=tags" }]" class="readMore">[{ oxmultilang ident="WIDGET_TAGS_LINKMORE" }]</a>
            [{/if}]
        </div>
    </div>
[{/if}]