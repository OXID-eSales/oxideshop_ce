[{if $oView->showTags() && ( $oView->getTagCloudManager() || ( ( $oView->getTagCloudManager() || $oxcmp_user) && $product ) ) }]
    <strong class="boxhead" id="tags">[{ oxmultilang ident="TAGS"}]</strong>
    <div class="box tags">
        [{if $oView->getEditTags()}]
            <p>[{ oxmultilang ident="TAGS_HIGHLIHGT_INSTRUCTIONS" }]</p>
        [{/if}]

        <p class="tags" id="tagsCloud">
          [{assign var="oCloudManager" value=$oView->getTagCloudManager() }]
          [{foreach from=$oCloudManager->getCloudArray() item=iCount key=sTagTitle}]
            <a class="tagitem_[{$oCloudManager->getTagSize($sTagTitle)}]" href="[{$oCloudManager->getTagLink($sTagTitle)}]">[{$oCloudManager->getTagTitle($sTagTitle)}]</a>
          [{/foreach}]
        </p>

        [{if !$product && $oView->isMoreTagsVisible()}]
            <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=tags" }]">[{ oxmultilang ident="TAGS_MORE" }]...</a>
        [{/if}]

        [{ if $product && $oxcmp_user}]
        <form name="basket" action="[{$product->getLink()}]#tags" method="post">
            <div>
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
            <input type="hidden" name="aid" value="[{ $product->oxarticles__oxid->value }]">
            <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
            [{if $oView->getEditTags()}]
                <input type="hidden" id="tagsInput" name="highTags">
                <input type="hidden" name="fnc" value="addTags">
                <label>[{ oxmultilang ident="TAGS_ADD" }]:</label><input type=text name=newTags>
                <span class="btn"><input id="test_saveTag" type="submit" class="btn" value="[{ oxmultilang ident="TAGS_SUBMIT" }]"></span>
                [{oxscript add="oxid.tags.addSelect('tagsCloud','tagsInput');" }]
            [{else}]
                <input type="hidden" name="fnc" value="editTags">
                <span class="btn"><input id="test_editTag" type="submit" class="btn" value="[{ oxmultilang ident="TAGS_EDIT" }]"></span>
            [{/if}]
            </div>
        </form>
        [{/if}]
    </div>
[{/if}]