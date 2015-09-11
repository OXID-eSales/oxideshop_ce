[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{$oView->getTitle()}]</h1>
    [{if $oView->getNewsletterStatus() == 4 || !$oView->getNewsletterStatus()}]
      [{oxifcontent ident="oxnewstlerinfo" object="oCont"}]
           [{$oCont->oxcontents__oxcontent->value}]
      [{/oxifcontent}]
      <br>
      [{include file="form/newsletter.tpl"}]
    [{elseif $oView->getNewsletterStatus() == 1}]
      [{oxmultilang ident="MESSAGE_SENT_CONFIRMATION_EMAIL"}]<br><br>
    [{elseif $oView->getNewsletterStatus() == 2}]
      [{oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED"}]<br><br>
    [{elseif $oView->getNewsletterStatus() == 3}]
      [{oxmultilang ident="MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED"}]<br><br>
    [{/if}]
[{/capture}]
[{include file="layout/page.tpl"}]