[{assign var="template_title" value="ACCOUNT_NEWSLETTER_TITLE"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location="ACCOUNT_NEWSLETTER_LOCATION"|oxmultilangassign|cat:$template_title}]

[{include file="inc/account_header.tpl" active_link=2 }]<br>
<strong id="test_newsletterSettingsHeader" class="boxhead">[{ oxmultilang ident="ACCOUNT_NEWSLETTER_SETTINGS" }]</strong>
<div class="box info">
    [{if $oView->getSubscriptionStatus() != 0 }]
        [{if $oView->getSubscriptionStatus() == 1 }]
          <br>[{ oxmultilang ident="ACCOUNT_NEWSLETTER_SUBSCRIPTIONSUCCESS" }]<br><br>
        [{else }]
          <br>[{ oxmultilang ident="ACCOUNT_NEWSLETTER_SUBSCRIPTIONREJECT" }]<br><br>
        [{/if }]
    [{else }]
        <form action="[{ $oViewConf->getSelfActionLink() }]" name="newsletter" method="post">
          <div>
              [{ $oViewConf->getHiddenSid() }]
              [{ $oViewConf->getNavFormParams() }]
              <input type="hidden" name="fnc" value="subscribe">
              <input type="hidden" name="cl" value="account_newsletter">
              <label>[{ oxmultilang ident="ACCOUNT_NEWSLETTER_SUBSCRIPTION" }]&nbsp;&nbsp;</label>
              <select name="status">
                <option value="1"   [{if $oView->isNewsletter() }]selected[{/if }] >[{ oxmultilang ident="ACCOUNT_NEWSLETTER_YES" }]</option>
                <option value="0"   [{if !$oView->isNewsletter() }]selected[{/if }] >[{ oxmultilang ident="ACCOUNT_NEWSLETTER_NO" }]</option>
              </select>
              <br>
              <span class="fs10">[{ oxmultilang ident="ACCOUNT_NEWSLETTER_MESSAGE" }]</span>
              <div class="dot_sep"></div>
              <div class="right">
                <span class="btn"><input id="test_newsletterSettingsSave" type="submit" value="[{ oxmultilang ident="ACCOUNT_NEWSLETTER_SAVE" }]" class="btn"></span>
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
              <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_NEWSLETTER_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
