<div class="lineBlock"></div>
<br>

<div class="col">
    [{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
    [{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]
    <form name="login" class="js-oxValidate" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
        <div>
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="fnc" value="login_noredirect">
            <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
            <input type="hidden" name="tpl" value="[{$oViewConf->getActTplName()}]">
            <input type="hidden" name="oxloadid" value="[{$oViewConf->getActContentLoadId()}]">
            [{if $oView->getArticleId()}]
              <input type="hidden" name="aid" value="[{$oView->getArticleId()}]">
            [{/if}]
            [{if $oView->getProduct()}]
              [{assign var="product" value=$oView->getProduct() }]
              <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
            [{/if}]
        </div>

        <ul class="form clear">
            <li [{if $aErrors}]class="oxInValid"[{/if}]>
                <label class="req">[{ oxmultilang ident="EMAIL" suffix="COLON" }]</label>
                <input id="loginUser" class="js-oxValidate js-oxValidate_notEmpty" type="text" name="lgn_usr" value="" size="37" >
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li [{if $aErrors}]class="oxInValid"[{/if}]>
                <label class="req">[{ oxmultilang ident="PASSWORD" suffix="COLON" }]</label>
                <input id="loginPwd" class="js-oxValidate js-oxValidate_notEmpty textbox" type="password" name="lgn_pwd" value="" size="37">
                <p class="oxValidateError">
                    <span class="js-oxError_notEmpty">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            [{if $oView->showRememberMe()}]
            <li>
                <label for="loginCookie">[{ oxmultilang ident="KEEP_LOGGED_IN" suffix="COLON" }]</label>
                <input id="loginCookie" type="checkbox" class="checkbox" name="lgn_cook" value="1">
            </li>
            [{/if}]

            <li class="formSubmit">
                <button id="loginButton" type="submit" class="submitButton largeButton">[{ oxmultilang ident="LOGIN" }]</button>
            </li>
        </ul>
    </form>
</div>

<div class="col">
    <a id="openAccountLink" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=register" }]" class="textLink" rel="nofollow">[{ oxmultilang ident="OPEN_ACCOUNT" }]</a><br />
    <a id="forgotPasswordLink" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=forgotpwd" }]" class="textLink" rel="nofollow">[{ oxmultilang ident="FORGOT_PASSWORD" }]</a>
</div>

<div class="clear"></div>
