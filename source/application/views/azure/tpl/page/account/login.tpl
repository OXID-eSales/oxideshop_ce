[{assign var="template_title" value="LOGIN"|oxmultilangassign }]
[{capture append="oxidBlock_content"}]
    <div class="accountLoginView">
        <h1 id="loginAccount" class="pageHead">[{ oxmultilang ident="LOGIN" }]</h1>
        <p>[{ oxmultilang ident="LOGIN_ALREADY_CUSTOMER" suffix="COLON" }]</p>

        [{include file="form/login_account.tpl"}]
    </div>
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]

[{include file="layout/page.tpl"}]



