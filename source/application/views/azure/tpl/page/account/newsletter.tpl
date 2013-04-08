[{capture append="oxidBlock_content"}]
[{assign var="template_title" value="NEWSLETTER_SETTINGS"|oxmultilangassign }]
[{if $oView->getSubscriptionStatus() != 0 }]
    [{if $oView->getSubscriptionStatus() == 1 }]
      <div class="status success corners">[{ oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION_SUCCESS" }]</div>
    [{else }]
      <div class="status success corners">[{ oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED" }]</div>
    [{/if }]
[{/if }]
<h1 id="newsletterSettingsHeader" class="pageHead">[{ oxmultilang ident="NEWSLETTER_SETTINGS" }]</h1>
[{include file="form/account_newsletter.tpl"}]
[{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="newsletter"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]