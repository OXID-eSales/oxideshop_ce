<strong id="test_LoginHeader" class="boxhead">[{ oxmultilang ident="INC_CMP_LOGIN_LOGIN2" }]</strong>
<div class="box info" id="selID_LoginBox">
  [{ oxmultilang ident="INC_CMP_LOGIN_ALREADYCUSTOMER" }]
  <div class="dot_sep"></div>
  <form name="login" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="fnc" value="login_noredirect">
        <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
        <input type="hidden" name="tpl" value="[{$oViewConf->getActTplName()}]">
        [{if $oView->getArticleId()}]
          <input type="hidden" name="aid" value="[{$oView->getArticleId()}]">
        [{/if}]
        [{if $oView->getProduct()}]
          [{assign var="product" value=$oView->getProduct() }]
          <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
        [{/if}]
         <table class="form">
            <tr>
              <td><label><strong>[{ oxmultilang ident="INC_CMP_LOGIN_EMAIL" }]</strong></label></td>
              <td><label><strong>[{ oxmultilang ident="INC_CMP_LOGIN_PWD" }]</strong></label></td>
              <td></td>
            </tr>
            <tr>
              <td><input id="test_LoginEmail" type="text" name="lgn_usr" value="" size="25">&nbsp;&nbsp;</td>
              <td><input id="test_LoginPwd" type="password" name="lgn_pwd" value="" size="25">&nbsp;&nbsp;</td>
              <td><span class="btn"><input id="test_Login" type="submit" class="btn" name="send" value="[{ oxmultilang ident="INC_CMP_LOGIN_LOGIN" }]"></span></td>
            </tr>
            <tr>
              <td colspan="2">
                [{if $oView->showRememberMe()}]<input id="test_LoginKeepLoggedIn" class="chbox" type="checkbox" name="lgn_cook" value="1">[{ oxmultilang ident="INC_CMP_LOGIN_KEEPLOGGEDIN" }][{else}]&nbsp;[{/if}]
              </td>
              <td>
                <a id="test_LoginRegister" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=register" }]" class="link" rel="nofollow">[{ oxmultilang ident="INC_CMP_LOGIN_OPENACCOUNT" }]</a><br />
                <a id="test_LoginLostPwd" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=forgotpwd" }]" class="link" rel="nofollow">[{ oxmultilang ident="INC_CMP_LOGIN_FORGOTPWD" }]</a>
              </td>
            </tr>
          </table>
    </div>
  </form>
</div>
