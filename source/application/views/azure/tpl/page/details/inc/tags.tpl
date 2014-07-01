[{assign var="oDetailsProduct" value=$oView->getProduct()}]
[{if $oView->showTags() && ( $oView->getTagCloudManager() || ( $oxcmp_user && $oDetailsProduct ) ) }]
    [{oxscript include='js/widgets/oxajax.js'}]
    [{oxscript include='js/widgets/oxtag.js'}]
    [{oxscript add="$('p.tagCloud a.tagText').click(oxTag.highTag);"}]
    [{oxscript add="$('#saveTag').click(oxTag.saveTag);"}]
    [{oxscript add="$('#cancelTag').click(oxTag.cancelTag);"}]
    [{oxscript add="$('#editTag').click(oxTag.editTag);"}]
    <p class="tagCloud">
        [{assign var="oCloudManager" value=$oView->getTagCloudManager()}]
        [{if $oCloudManager->getCloudArray()|count < 0}]
            [{oxmultilang ident="NO_TAGS"}]
        [{/if}]
        [{assign var="oTagSet" value=$oCloudManager->getCloudArray() }]
        [{foreach from=$oTagSet item=oTag }]
            <a class="tagitem_[{ $oCloudManager->getTagSize($oTag->getTitle()) }]" href="[{ $oTag->getLink() }]">[{ $oTag->getTitle() }]</a>
        [{/foreach}]
    </p>
    [{if $oDetailsProduct && $oView->canChangeTags()}]
      <form action="[{$oViewConf->getSelfActionLink()}]#tags" method="post" id="tagsForm" >
        <div>
          [{$oViewConf->getHiddenSid()}]
          [{$oViewConf->getNavFormParams()}]
          <input type="hidden" name="cl" value="[{$oViewConf->getTopActiveClassName()}]">
          <input type="hidden" name="aid" value="[{$oDetailsProduct->oxarticles__oxid->value}]">
          <input type="hidden" name="anid" value="[{$oDetailsProduct->oxarticles__oxnid->value}]">
          <input type="hidden" name="fnc" value="editTags">
          <button class="submitButton" id="editTag" type="submit">[{oxmultilang ident="EDIT_TAGS"}]</button>
        </div>
      </form>
    [{/if}]
[{/if}]