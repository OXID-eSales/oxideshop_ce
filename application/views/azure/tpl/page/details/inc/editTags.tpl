[{assign var="oDetailsProduct" value=$oView->getProduct() }]
[{if $oView->showTags() && $oView->getTagCloudManager() && $oDetailsProduct && $oView->canChangeTags()}]
    <p>[{oxmultilang ident="HIGHLIHGT_TAGS"}]</p>
    <p class="tagCloud">
        [{assign var="oCloudManager" value=$oView->getTagCloudManager()}]
        [{assign var="oTagSet" value=$oCloudManager->getCloudArray() }]
        [{foreach from=$oTagSet item=oTag }]
            <a class="tagitem_[{ $oCloudManager->getTagSize($oTag->getTitle()) }]" href="[{ $oTag->getLink() }]">[{ $oTag->getTitle() }]</a>
        [{/foreach}]
    </p>

    <p class="tagError inlist" >[{oxmultilang ident="ALREADY_ADDED_TAG" suffix="COLON" }] <span></span></p>
    <p class="tagError invalid">[{oxmultilang ident="INVALID_TAGS_REMOVED" suffix="COLON" }] <span></span></p>
    <form action="[{$oViewConf->getSelfActionLink()}]#tags" method="post" id="tagsForm" >
    <div>
        [{$oViewConf->getHiddenSid()}]
        [{$oViewConf->getNavFormParams()}]
        <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
        <input type="hidden" name="aid" value="[{$oDetailsProduct->oxarticles__oxid->value}]">
        <input type="hidden" name="anid" value="[{$oDetailsProduct->oxarticles__oxnid->value}]">
        <input type="hidden" id="tagsInput" name="highTags">
        <input type="hidden" name="fnc" value="addTags">
        <label for="newTags">[{oxmultilang ident="ADD_TAGS" suffix='COLON' }]</label>
        <input class="input" type="text" name="newTags" id="newTags" maxlength="[{$oCloudManager->getTagMaxLength()}]">
        <button class="submitButton" id="saveTag" type="submit">[{oxmultilang ident="SUBMIT"}]</button>
        <button class="submitButton" id="cancelTag" type="submit">[{oxmultilang ident="CANCEL"}]</button>
    </div>
</form>
[{/if}]