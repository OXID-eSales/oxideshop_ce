[{capture append="oxidBlock_content"}]
    [{if $oView->getNewsletterStatus() == 4 || !$oView->getNewsletterStatus()}]
      <h1 class="pageHead">[{ oxmultilang ident="STAY_INFORMED" }]</h1>
      [{oxifcontent ident="oxnewstlerinfo" object="oCont"}]
           [{ $oCont->oxcontents__oxcontent->value }]
      [{/oxifcontent}]
      <br>
      [{include file="form/newsletter.tpl"}]
    [{elseif $oView->getNewsletterStatus() == 1}]
      <h1 class="pageHead">[{ oxmultilang ident="MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS" }]</h1>
      [{ oxmultilang ident="MESSAGE_SENT_CONFIRMATION_EMAIL" }]<br><br>
    [{elseif $oView->getNewsletterStatus() == 2}]
      <h1 class="pageHead">[{ oxmultilang ident="MESSAGE_NEWSLETTER_CONGRATULATIONS" }]</h1>
      [{ oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED" }]<br><br>
    [{elseif $oView->getNewsletterStatus() == 3}]
      <h1 class="pageHead">[{ oxmultilang ident="SUCCESS" }]</h1>
      [{ oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED" }]<br><br>
    [{/if}]
    [{ insert name="oxid_tracker"}]
[{/capture}]

[{include file="layout/page.tpl"}]