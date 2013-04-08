  [{if $oView->isConnectedWithFb() }]
    <div id="loggedFbUserBox">
        <div style="margin-top: 5px;">[{ oxmultilang ident="INC_CMP_FBCONNECT_YOUARELOGGEDINAS" }]:</div>
        <div style="margin: 10px; 0">
            <fb:profile-pic uid="[{$oView->getFbUserId()}]" linked="true" width="30" height="30" size="square" facebook-logo="true"></fb:profile-pic> <fb:name uid="[{$oView->getFbUserId()}]" useyou="false"></fb:name>
        </div>
        <hr>
    </div>
  [{/if}]
  <div align="center" style="margin: 10px 0 5px 0;">
      <fb:login-button size="medium" autologoutlink="true" length="short">[{if $oView->isConnectedWithFb() }][{ oxmultilang ident="INC_CMP_FBCONNECT_LOGOUTBTNTEXT" }][{else}][{ oxmultilang ident="INC_CMP_FBCONNECT_LOGINBTNTEXT" }][{/if}]</fb:login-button>
  </div>
