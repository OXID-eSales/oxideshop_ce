[{if $oViewConf->getFbAppId()}]
    <div id="fb-root"></div>
    [{oxscript include="js/widgets/oxmodalpopup.js"}]
    [{oxscript include="js/widgets/oxfacebook.js"}]
    [{if $oView->isActive('FacebookConfirm') && !$oView->isFbWidgetVisible()}]
        <div id="fbinfo" class="fbInfoPopup popupBox corners FXgradGreyLight glowShadow">
            <img src="[{$oViewConf->getImageUrl('x.png')}]" alt="" class="closePop">
            <div class="wrappingIntro clear">
                <h3>[{oxmultilang ident="FACEBOOK_ENABLE_INFOTEXTHEADER"}]</h3>
                [{oxifcontent ident="oxfacebookenableinfotext" object="oCont"}]
                    [{$oCont->oxcontents__oxcontent->value}]
                [{/oxifcontent}]
            </div>
        </div>
        [{capture name="facebookInit"}]
            [{oxscript include="js/libs/cookie/jquery.cookie.js"}]
            [{assign var="sFbAppId" value=$oViewConf->getFbAppId()}]
            [{assign var="sLocale" value="FACEBOOK_LOCALE"|oxmultilangassign}]
            [{assign var="sLoginUrl" value=$oView->getLink()|oxaddparams:"fblogin=1"}]
            [{assign var="sLogoutUrl" value=$oViewConf->getLogoutLink()}]
            [{oxscript add="$('.oxfbenable').click( function() { oxFacebook.showFbWidgets('`$sFbAppId`','`$sLocale`','`$sLoginUrl`','`$sLogoutUrl`'); return false;});"}]
            [{oxscript add="$('.oxfbinfo').oxModalPopup({ target: '#fbinfo',width: '490px'});"}]
        [{/capture}]
    [{else}]
        [{capture name="facebookInit"}]
            oxFacebook.fbInit("[{$oViewConf->getFbAppId()}]", "[{oxmultilang ident="FACEBOOK_LOCALE"}]", "[{$oView->getLink()|oxaddparams:"fblogin=1"}]", "[{$oViewConf->getLogoutLink()}]");
        [{/capture}]
    [{/if}]
    [{oxscript add="`$smarty.capture.facebookInit`"}]
[{/if}]