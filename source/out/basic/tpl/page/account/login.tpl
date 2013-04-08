[{assign var="template_title" value="ACCOUNT_LOGIN_LOGIN"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location=$template_title}]

[{include file="inc/cmp_login.tpl" }]

<div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
              <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_LOGIN_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]