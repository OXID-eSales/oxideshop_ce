[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{oxscript add="$('div.tabbedWidgetBox').tabs();"}]

[{block name="details_tabs_longdescription"}]
    [{oxhasrights ident="SHOWLONGDESCRIPTION"}]
        [{assign var="oLongdesc" value=$oDetailsProduct->getLongDescription()}]
        [{if $oLongdesc->value}]
            [{capture append="tabs"}]<a href="#description">[{oxmultilang ident="DESCRIPTION"}]</a>[{/capture}]
            [{capture append="tabsContent"}]
            <div id="description" class="cmsContent">
                [{oxeval var=$oLongdesc}]
                [{if $oDetailsProduct->oxarticles__oxexturl->value}]
                    <a id="productExturl" class="js-external" href="http://[{$oDetailsProduct->oxarticles__oxexturl->value}]">
                    [{if $oDetailsProduct->oxarticles__oxurldesc->value }]
                        [{$oDetailsProduct->oxarticles__oxurldesc->value }]
                    [{else}]
                        [{$oDetailsProduct->oxarticles__oxexturl->value }]
                    [{/if}]
                    </a>
                [{/if}]
            </div>
            [{/capture}]
        [{/if}]
    [{/oxhasrights}]
[{/block}]

[{block name="details_tabs_attributes"}]
    [{if $oView->getAttributes()}]
        [{capture append="tabs"}]<a href="#attributes">[{oxmultilang ident="SPECIFICATION"}]</a>[{/capture}]
        [{capture append="tabsContent"}]<div id="attributes">[{include file="page/details/inc/attributes.tpl"}]</div>[{/capture}]
    [{/if}]
[{/block}]

[{block name="details_tabs_pricealarm"}]
    [{if $oView->isPriceAlarm() && !$oDetailsProduct->isParentNotBuyable()}]
        [{capture append="tabs"}]<a href="#pricealarm">[{oxmultilang ident="PRICE_ALERT"}]</a>[{/capture}]
        [{capture append="tabsContent"}]<div id="pricealarm">[{include file="form/pricealarm.tpl"}]</div>[{/capture}]
    [{/if}]
[{/block}]

[{block name="details_tabs_tags"}]
    [{if $oView->showTags() && ( $oView->getTagCloudManager() || ( ( $oView->getTagCloudManager() || $oxcmp_user) && $oDetailsProduct ) )}]
        [{capture append="tabs"}]<a href="#tags">[{oxmultilang ident="TAGS"}]</a>[{/capture}]
        [{capture append="tabsContent"}]<div id="tags">[{oxid_include_dynamic file="page/details/inc/tags.tpl"}]</div>[{/capture}]
    [{/if}]
[{/block}]

[{block name="details_tabs_media"}]
    [{if $oView->getMediaFiles() || $oDetailsProduct->oxarticles__oxfile->value}]
        [{capture append="tabs"}]<a href="#media">[{oxmultilang ident="MEDIA"}]</a>[{/capture}]
        [{capture append="tabsContent"}]<div id="media">[{include file="page/details/inc/media.tpl"}]</div>[{/capture}]
    [{/if}]
[{/block}]

[{block name="details_tabs_fbcomments"}]
    [{if $oView->isActive('FbComments') && $oViewConf->getFbAppId()}]
        [{capture append="FBtabs"}]<a href="#productFbComments">[{oxmultilang ident="FACEBOOK_COMMENTS"}]</a>[{/capture}]
        [{assign var='_fbScript' value="http://connect.facebook.net/en_US/all.js#appId="|cat:$oViewConf->getFbAppId()|cat:"&amp;xfbml=1"}]
        [{capture append="FBtabsContent"}]<div id="productFbComments">[{include file="widget/facebook/enable.tpl" source="widget/facebook/comments.tpl" ident="#productFbComments" script=$_fbScript type="text"}]</div>[{/capture}]
    [{/if}]
[{/block}]

[{block name="details_tabs_fbinvite"}]
    [{if $oView->isActive('FbInvite') && $oViewConf->getFbAppId()}]
        [{capture append="FBtabs"}]<a href="#productFbInvite">[{oxmultilang ident="FACEBOOK_INVITE"}]</a>[{/capture}]
        [{capture append="FBtabsContent"}]
            <div id="productFbInvite">
                <fb:serverfbml width="560px" id="productFbInviteFbml">
                    [{include file="widget/facebook/enable.tpl" source="widget/facebook/invite.tpl" ident="#productFbInviteFbml" type="text"}]
                </fb:serverfbml>
            </div>
        [{/capture}]
    [{/if}]
[{/block}]

[{block name="details_tabs_main"}]
    [{if $tabs}]
        <div class="tabbedWidgetBox clear">
            <ul id="itemTabs" class="tabs clear">
                [{foreach from=$tabs item="tab"}]
                    <li>[{$tab}]</li>
                [{/foreach}]
            </ul>
            <div class="widgetBoxBottomRound">
                [{foreach from=$tabsContent item="tabContent"}]
                    [{$tabContent}]
                [{/foreach}]
            </div>
        </div>
    [{/if}]
[{/block}]

[{block name="details_tabs_facebook"}]
    [{if $FBtabs}]
        <div class="tabbedWidgetBox clear">
            <ul id="itemFbTabs" class="tabs clear">
                [{foreach from=$FBtabs item="FBtab"}]
                    <li class="fbTab">[{$FBtab}]</li>
                [{/foreach}]
            </ul>
            <div class="widgetBoxBottomRound FXgradBlueLight">
                [{foreach from=$FBtabsContent item="FBtabContent"}]
                    [{$FBtabContent}]
                [{/foreach}]
            </div>
        </div>
    [{/if}]
[{/block}]
