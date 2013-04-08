[{capture append="oxidBlock_content"}]
[{assign var="template_title" value="PAGE_ACCOUNT_NEWSLETTER_SETTINGS"|oxmultilangassign }]
[{if $oView->getSubscriptionStatus() != 0 }]
    [{if $oView->getSubscriptionStatus() == 1 }]
      <div class="status success corners">[{ oxmultilang ident="PAGE_ACCOUNT_NEWSLETTER_SUBSCRIPTIONSUCCESS" }]</div>
    [{else }]
      <div class="status success corners">[{ oxmultilang ident="PAGE_ACCOUNT_NEWSLETTER_SUBSCRIPTIONREJECT" }]</div>
    [{/if }]
[{/if }]
<h1 id="newsletterSettingsHeader" class="pageHead">[{ oxmultilang ident="PAGE_ACCOUNT_NEWSLETTER_SETTINGS" }]</h1>
[{include file="form/account_newsletter.tpl"}]
[{insert name="oxid_tracker" title=$template_title }]
[{/capture}]
[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="newsletter"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]