[{foreach from=$Errors.dyn_cmp_login_right item=oEr key=key }]
  <p class="err">[{ $oEr->getOxMessage()}]</p>
[{/foreach}]
[{if !$oxcmp_user->oxuser__oxpassword->value}]
  <form name="rlogin" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
    <div class="form">
        [{ $oViewConf->getHiddenSid() }]
        [{$_login_additional_form_parameters}]
        <input type="hidden" name="fnc" value="login_noredirect">
        <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
        <input type="hidden" name="pgNr" value="[{$_login_pgnr}]">
        <input type="hidden" name="tpl" value="[{$_login_tpl}]">
        <input type="hidden" name="CustomError" value='dyn_cmp_login_right'>
        [{if $oView->getProduct()}]
          [{assign var="product" value=$oView->getProduct() }]
          <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
        [{/if}]

        <label for="test_RightLogin_Email">[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_EMAIL" }]</label>
        <input id="test_RightLogin_Email" type="text" name="lgn_usr" value="" class="txt">

        <label for="test_RightLogin_Pwd">[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_PWD" }]</label>
        <input id="test_RightLogin_Pwd" type="password" name="lgn_pwd" value="" class="txt">

        [{if $oView->showRememberMe()}]
        <label for="test_RightLogin_KeepLogggedIn">
            <input id="test_RightLogin_KeepLogggedIn" type="checkbox" name="lgn_cook" value="1" class="chk">
            [{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_KEEPLOGGEDIN" }]
        </label>
        [{/if}]

        <span class="btn"><input id="test_RightLogin_Login" type="submit" name="send" value="[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_LOGIN" }]" class="btn"></span>
        <a id="test_RightLogin_Register" class="link" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=register" params=$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_OPENACCOUNT" }]</a>
        <a id="test_RightLogin_LostPwd" class="link" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=forgotpwd" params=$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_FORGOTPWD" }]</a>
    </div>
  </form>
[{else}]

      <div id="test_LoginUser">
        [{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_LOGGEDINAS" }]<br>
        [{assign var="fullname" value=$oxcmp_user->oxuser__oxfname->value|cat:" "|cat:$oxcmp_user->oxuser__oxlname->value }]
        <b>&quot;[{ $oxcmp_user->oxuser__oxusername->value|oxtruncate:25:"...":true }]&quot;</b> <br>
        ([{ $fullname|oxtruncate:25:"...":true }])
      </div>

      <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        <div class="form">
          [{ $oViewConf->getHiddenSid() }]
          [{$_login_additional_form_parameters}]
          <input type="hidden" name="fnc" value="logout">
          <input type="hidden" name="redirect" value="1">
          <input type="hidden" name="cl" value="[{ $oViewConf->getActionClassName() }]">
          <input type="hidden" name="lang" value="[{ $oViewConf->getActLanguageId() }]">
          <input type="hidden" name="pgNr" value="[{$_login_pgnr-1}]">
          <input type="hidden" name="tpl" value="[{$_login_tpl}]">
          [{if $oView->getProduct()}]
              [{assign var="product" value=$oView->getProduct() }]
              <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
          [{/if}]
          [{if $oViewConf->getShowListmania() && $oView->getActiveRecommList()}]
              [{assign var="actvrecommlist" value=$oView->getActiveRecommList() }]
            <input type="hidden" name="recommid" value="[{ $actvrecommlist->oxrecommlists__oxid->value }]">
          [{/if}]

              <span class="btn"><input id="test_RightLogout" type="submit" name="send" value="[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_LOGOUT" }]" class="btn"></span>
        </div>
      </form>
[{/if }]
