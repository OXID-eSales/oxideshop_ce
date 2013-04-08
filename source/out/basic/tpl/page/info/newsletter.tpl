[{assign var="template_title" value="NEWSLETTER_NEWSLWTTERTITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

[{if $oView->getNewsletterStatus() == 4 || !$oView->getNewsletterStatus()}]
<strong id="test_stayInformedHeader" class="boxhead">[{ oxmultilang ident="NEWSLETTER_STAYINFORMED" }]</strong>
<div class="box info">
  [{oxifcontent ident="oxnewstlerinfo" object="oCont"}]
      [{ $oCont->oxcontents__oxcontent->value }]
  [{/oxifcontent}]
  [{assign var="aRegParams" value=$oView->getRegParams()}]
    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="fnc" value="send">
          <input type="hidden" name="cl" value="newsletter">
          <input type="hidden" name="editval[oxuser__oxcountryid]" value="[{$oView->getHomeCountryId()}]">
      </div>
      <table class="form">
          <tr>
            <td><label>[{ oxmultilang ident="NEWSLETTER_TITLE" }]</label></td>
            <td>
              [{include file="inc/salutation.tpl" name="editval[oxuser__oxsal]" value=$aRegParams.oxuser__oxsal class="newsletter_text"}]
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="NEWSLETTER_FIRSTNAME" }]</label></td>
            <td>
              <input id="test_newsletterFname" type="text" name="editval[oxuser__oxfname]" size=40 maxlength=40 value="[{if $aRegParams.oxuser__oxfname}][{$aRegParams.oxuser__oxfname}][{/if}]">
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="NEWSLETTER_LASTNAME" }]&nbsp;&nbsp;</label></td>
            <td>
              <input id="test_newsletterLname" type="text" name="editval[oxuser__oxlname]" size=40 maxlength=40 value="[{if $aRegParams.oxuser__oxlname}][{$aRegParams.oxuser__oxlname}][{/if}]">
            </td>
          </tr>
          <tr>
            <td><label>[{ oxmultilang ident="NEWSLETTER_EMAIL" }]&nbsp;&nbsp;</label></td>
            <td>
              <input id="test_newsletterUserName" type="text" name="editval[oxuser__oxusername]" size=40 maxlength=40 value="[{if $aRegParams.oxuser__oxusername}][{$aRegParams.oxuser__oxusername}][{/if}]">&nbsp;<span class="req">*</span>
            </td>
          </tr>
          <tr>
            <td></td>
            <td valign="top">
              <input id="test_newsletterSubscribeOn" type="radio" name="subscribeStatus" value="1" checked><label for="test_newsletterSubscribeOn">[{ oxmultilang ident="NEWSLETTER_SUBSCRIBE" }]</label>
              <input id="test_newsletterSubscribeOff" type="radio" name="subscribeStatus" value="0"><label for="test_newsletterSubscribeOff">[{ oxmultilang ident="NEWSLETTER_UNSUBSCRIBE" }]</label>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td></td>
            <td valign="top">[{ oxmultilang ident="NEWSLETTER_COMPLETEMARKEDFIELEDS" }]</td>
          </tr>
          <tr>
            <td></td>
            <td>
              <br>
              <span class="btn"><input id="newsLetterSubmit" class="btn" type="submit" value="[{ oxmultilang ident="NEWSLETTER_SUBSCRIBE" }]"></span>
            </td>
          </tr>
      </table>
    </form>
</div>
[{elseif $oView->getNewsletterStatus() == 1}]
  <strong class="boxhead">[{ oxmultilang ident="NEWSLETTER_THANKYOU" }]</strong>
  <div class="box"><br>
    [{ oxmultilang ident="NEWSLETTER_YOUHAVEBEENSENTCONFIRMATION" }]<br><br>
  </div>
[{elseif $oView->getNewsletterStatus() == 2}]
  <strong class="boxhead">[{ oxmultilang ident="NEWSLETTER_CONGRATULATIONS" }]</strong>
  <div class="box"><br><br>
    [{ oxmultilang ident="NEWSLETTER_SUBSCRIPTIONACTIVATED" }]<br><br>
    <br>
  </div>
[{elseif $oView->getNewsletterStatus() == 3}]
  <strong class="boxhead">[{ oxmultilang ident="NEWSLETTER_SECCESS" }]</strong>
  <div class="box"><br>
    [{ oxmultilang ident="NEWSLETTER_SUBSCRIPTIONCANCELED" }]<br><br>
  </div>
[{/if}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
