<div>
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
            <input type="hidden" name="ord_agb" value="0">
        </div>
            <ul class="clear agb">
            <li>
                <input id="orderConfirmAgb" type="checkbox" name="ord_agb" value="1">
                <label id="confirmLabel" for="orderConfirmAgb">[{oxifcontent ident="oxrighttocancellegend" object="oContent"}]
                    [{ $oContent->oxcontents__oxcontent->value }]
                    [{/oxifcontent}]</label>
            </li>
            <li class="formSubmit">
                <button id="confirmButton" type="submit" class="submitButton largeButton">[{ oxmultilang ident="LOGIN" }]</button>
            </li>
        </ul>
    </form>
</div>
