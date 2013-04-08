[{capture append="oxidBlock_pageBody"}]
    [{oxid_include_widget cl="oxwCookieNote" _parent=$oView->getClassName() nocookie=1}]
[{/capture}]
[{capture append="oxidBlock_content"}]
    <div class="accountLoginView">
        <h1 id="loginAccount" class="pageHead">[{ oxmultilang ident="LOGIN" }]</h1>
        [{ if $oView->confirmTerms()}]
            [{include file="form/privatesales/accept_terms.tpl"}]
        [{else}]
            [{include file="widget/header/languages.tpl"}]
            <p>[{ oxmultilang ident="LOGIN_ALREADY_CUSTOMER" suffix="COLON" }]</p>
            [{include file="form/login_account.tpl"}]
        [{/if }]
    </div>
[{/capture}]
[{include file="layout/popup.tpl"}]