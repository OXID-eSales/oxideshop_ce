[{capture append="oxidBlock_pageBody"}]
    [{if $oView->isEnabledPrivateSales()}]
        [{oxid_include_widget cl="oxwCookieNote" _parent=$oView->getClassName() nocookie=1}]
    [{/if}]
[{/capture}]
[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{$oView->getTitle()}]</h1>
    [{if $oView->isExpiredLink()}]
        <div class="box info">[{oxmultilang ident="ERROR_MESSAGE_PASSWORD_LINK_EXPIRED"}]</div>
    [{elseif $oView->showUpdateScreen()}]
        [{include file="form/forgotpwd_change_pwd.tpl"}]
    [{elseif $oView->updateSuccess()}]
        <div class="box info">[{oxmultilang ident="PASSWORD_CHANGED"}]</div>
        <div class="bar">
            <form action="[{$oViewConf->getSelfActionLink()}]" name="forgotpwd" method="post">
                <div>
                    [{$oViewConf->getHiddenSid()}]
                    <input type="hidden" name="cl" value="start">
                    <button id="backToShop" class="submitButton largeButton" type="submit">[{oxmultilang ident="BACK_TO_SHOP"}]</button>
                </div>
            </form>
        </div>
    [{else}]
        [{if $oView->getForgotEmail()}]
            <div class="box info">[{oxmultilang ident="PASSWORD_WAS_SEND_TO" suffix="COLON"}] [{$oView->getForgotEmail()}]</div>
            <div class="bar">
                <form action="[{$oViewConf->getSelfActionLink()}]" name="forgotpwd" method="post">
                    <div>
                        [{$oViewConf->getHiddenSid()}]
                        <input type="hidden" name="cl" value="start">
                        <button id="backToShop" class="submitButton largeButton" type="submit">[{oxmultilang ident="BACK_TO_SHOP"}]</button>
                    </div>
                 </form>
             </div>
        [{else}]
            [{include file="form/forgotpwd_email.tpl"}]
        [{/if}]
    [{/if}]
[{/capture}]
[{if $oView->isActive('PsLogin')}]
    [{include file="layout/popup.tpl"}]
[{else}]
    [{include file="layout/page.tpl"}]
[{/if}]

