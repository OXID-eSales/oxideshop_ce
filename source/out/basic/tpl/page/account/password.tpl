[{assign var="template_title" value="ACCOUNT_PASSWORD_TITLE"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location="ACCOUNT_PASSWORD_LOCATION"|oxmultilangassign|cat:$template_title}]

[{include file="inc/account_header.tpl" active_link=1 }]<br>
<strong id="test_personalSettingsHeader" class="boxhead">[{ oxmultilang ident="ACCOUNT_PASSWORD_PERSONALSETTINGS" }]</strong>
<div class="box info">
    [{if $oView->isPasswordChanged() }]
     <div>
      [{ oxmultilang ident="ACCOUNT_PASSWORD_PASSWORDCHANGED" }]
     </div>
    [{else}]
      <form action="[{ $oViewConf->getSelfActionLink() }]" name="changepassword" method="post">
        <div class="account">
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="fnc" value="changePassword">
            <input type="hidden" name="cl" value="account_password">
            <input type="hidden" name="CustomError" value='user'>
            <strong class="h4">[{ oxmultilang ident="ACCOUNT_PASSWORD_TOCHANGEPASSWORD" }]</strong>
            <div class="dot_sep"></div>
            [{include file="inc/error.tpl" Errorlist=$Errors.user errdisplay="inbox"}]
             <small><span class="note">[{ oxmultilang ident="ACCOUNT_PASSWORD_NOTE" }]</span><span class="def_color_1">[{ oxmultilang ident="ACCOUNT_PASSWORD_PASSWORDMINLENGTH" }]</span></small><br><br>
             <table class="form">
               <tr>
                 <td><label>[{ oxmultilang ident="ACCOUNT_PASSWORD_OLDPASSWORD" }]</label></td>
                 <td><input type="password" name="password_old"></td>
               </tr>
               <tr>
                 <td><label>[{ oxmultilang ident="ACCOUNT_PASSWORD_NEWPASSWORD" }]</label></td>
                 <td><input type="password" name="password_new"></td>
               </tr>
               <tr>
                 <td><label>[{ oxmultilang ident="ACCOUNT_PASSWORD_CONFIRMPASSWORD" }]&nbsp;&nbsp;&nbsp;</label></td>
                 <td><input type="password" name="password_new_confirm"></td>
               </tr>
             </table>
             <div class="dot_sep"></div>
             <div class="right">
               <span class="btn"><input id="test_savePass" type="submit" value="[{ oxmultilang ident="ACCOUNT_PASSWORD_SAVE" }]" class="btn"></span>
             </div>
            <br><br>
        </div>
       </form>
    [{/if }]
</div>

<div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
               <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_PASSWORD_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>

[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
