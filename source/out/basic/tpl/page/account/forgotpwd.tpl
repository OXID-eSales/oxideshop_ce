[{if $oView->showUpdateScreen() }]
  [{assign var="template_title" value="FORGOTPWD_UPDATETITLE"|oxmultilangassign}]
[{elseif $oView->updateSuccess() }]
  [{assign var="template_title" value="FORGOTPWD_UPDATESUCCESSTITLE"|oxmultilangassign}]
[{else}]
  [{assign var="template_title" value="FORGOTPWD_TITLE"|oxmultilangassign}]
[{/if}]

[{if $oView->isActive('PsLogin') }]
    [{include file="_header_plain.tpl" title=$template_title location=$template_title cssclass="body"}]
    <div class="psLoginPlainBox">
    [{include file="inc/error.tpl" Errorlist=$Errors.default}]
[{else}]
    [{include file="_header.tpl" title=$template_title location=$template_title}]
[{/if}]

[{if $oView->isExpiredLink() }]

  <strong class="boxhead">[{$template_title}]</strong>
  <div class="box info">
    [{ oxmultilang ident="FORGOTPWD_ERRLINKEXPIRED" }]
  </div>

[{elseif $oView->showUpdateScreen() }]

  <strong class="boxhead">[{$template_title}]</strong>
  <div class="box info">
    [{ oxmultilang ident="FORGOTPWD_ENTERNEWPASSWORD" }]<br><br>
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          <input type="hidden" name="fnc" value="updatePassword">
          <input type="hidden" name="uid" value="[{ $oView->getUpdateId() }]">
          <input type="hidden" name="cl" value="forgotpwd">
      </div>
      <table class="form">
          <tr>
            <td><label>[{ oxmultilang ident="FORGOTPWD_NEWPASSWORD" }]</label></td>
            <td><input type="password" name="password_new" size="45" ></td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="FORGOTPWD_CONFIRMPASSWORD" }]</label></td>
            <td><input type="password" name="password_new_confirm" size="45" ></td>
          </tr>
          <tr>
            <td></td>
            <td><span class="btn"><input type="submit" name="save" value="[{ oxmultilang ident="FORGOTPWD_UPDATEPASSWORD" }]" class="btn"></span></td>
          </tr>
      </table>
    </form>
  </div>

[{elseif $oView->updateSuccess() }]

  <strong class="boxhead">[{$template_title}]</strong>
  <div class="box info">
    [{ oxmultilang ident="FORGOTPWD_UPDATE_SUCCESS" }]
  </div>

  <div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="start">
        <div class="right">
          <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="FORGOTPWD_BACKTOSHOP" }]">
        </div>
      </div>
    </form>
  </div>
[{else}]

  <strong class="boxhead">[{$template_title}]</strong>
  [{ if $oView->getForgotEmail()}]
    <div class="box info">
      [{ oxmultilang ident="FORGOTPWD_PWDWASSEND" }] [{$oView->getForgotEmail()}]
    </div>
    <div class="bar prevnext">
      <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
        <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
            <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="FORGOTPWD_BACKTOSHOP" }]">
          </div>
        </div>
      </form>
    </div>
  [{else}]
    <div class="box info">
      [{ oxmultilang ident="FORGOTPWD_FORGOTPWD" }]<br>
      [{ oxmultilang ident="FORGOTPWD_WEWILLSENDITTOYOU" }]<br><br>
      <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
        <div>
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          <input type="hidden" name="fnc" value="forgotpassword">
          <input type="hidden" name="cl" value="forgotpwd">
        </div>
        <table class="form">
          <tr>
            <td><label>[{ oxmultilang ident="FORGOTPWD_YOUREMAIL" }]</label></td>
            <td><input id="test_lgn_usr" type="text" name="lgn_usr" value="[{$oView->getActiveUsername()}]" size="45" ></td>
          </tr>
          <tr>
            <td></td>
            <td><span class="btn"><input type="submit" name="save" value="[{ oxmultilang ident="FORGOTPWD_REQUESTPWD" }]" class="btn"></span></td>
          </tr>
        </table>
      </form>
      [{ oxmultilang ident="FORGOTPWD_AFTERCLICK" }]<br><br>
      [{ oxcontent ident="oxforgotpwd" }]
    </div>
  [{ /if}]

[{/if}]

[{if $oView->isActive('PsLogin') }]
    </div>
    [{include file="_footer_plain.tpl" }]
[{else}]
    [{ insert name="oxid_tracker" title=$template_title }]
    [{include file="_footer.tpl" }]
[{/if}]