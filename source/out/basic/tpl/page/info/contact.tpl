[{assign var="template_title" value="CONTACT_TITLECONTACT"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]


<strong id="test_companyName" class="boxhead">[{ $oxcmp_shop->oxshops__oxcompany->value }]</strong>
<div class="box info">
  [{ $oxcmp_shop->oxshops__oxzip->value }]&nbsp;[{ $oxcmp_shop->oxshops__oxcity->value }]<br>
  [{ $oxcmp_shop->oxshops__oxstreet->value }]<br>
  [{ $oxcmp_shop->oxshops__oxcountry->value }]<br><br>

  [{ if $oxcmp_shop->oxshops__oxtelefon->value}]
    [{ oxmultilang ident="CONTACT_PHONE" }] [{ $oxcmp_shop->oxshops__oxtelefon->value }]<br>
  [{/if}]

  [{ if $oxcmp_shop->oxshops__oxtelefax->value}]
    [{ oxmultilang ident="CONTACT_FAX" }] [{ $oxcmp_shop->oxshops__oxtelefax->value }]<br>
  [{/if}]

  [{ if $oxcmp_shop->oxshops__oxinfoemail->value}]
    [{ oxmultilang ident="CONTACT_EMAIL" }] [{oxmailto address=$oxcmp_shop->oxshops__oxinfoemail->value encode="javascript"}]<br>
  [{/if}]
</div>

<strong id="test_contactHeader" class="boxhead">[{ oxmultilang ident="CONTACT_CONTACT" }]</strong>
<div class="box info">
  [{ if !$oView->getContactSendStatus() }]
    [{assign var="editval" value=$oView->getUserData() }]
      <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
        <div>
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="fnc" value="send"/>
            <input type="hidden" name="cl" value="contact"/>
            [{assign var="oCaptcha" value=$oView->getCaptcha() }]
            <input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]"/>
        </div>
        <table width="100%" class="form">
        <tr>
          <td><label>[{ oxmultilang ident="CONTACT_TITLE" }]&nbsp;&nbsp;</label></td>
          <td>
            [{include file="inc/salutation.tpl" name="editval[oxuser__oxsal]" value=$editval.oxuser__oxsal }]
          </td>
        </tr>
        <tr>
          <td><label>[{ oxmultilang ident="CONTACT_FIRSTNAME" }]&nbsp;&nbsp;</label></td>
          <td><input type="text" name="editval[oxuser__oxfname]" size="70" maxlength="40" value="[{$editval.oxuser__oxfname}]" class="defaultcontent">&nbsp;<span class="req">*</span></td>
        </tr>
        <tr>
          <td><label>[{ oxmultilang ident="CONTACT_LASTNAME" }]&nbsp;&nbsp;</label></td>
          <td><input type="text" name="editval[oxuser__oxlname]" size=70 maxlength=40 value="[{$editval.oxuser__oxlname}]" class="defaultcontent">&nbsp;<span class="req">*</span></td>
        </tr>
        <tr>
          <td><label>[{ oxmultilang ident="CONTACT_EMAIL2" }]&nbsp;&nbsp;</label></td>
          <td><input id="test_contactEmail" type="text" name="editval[oxuser__oxusername]"  size=70 maxlength=40 value="[{$editval.oxuser__oxusername}]" class="defaultcontent">&nbsp;<span class="req">*</span></td>
        </tr>
        <tr>
          <td><label>[{ oxmultilang ident="CONTACT_SUBJECT" }]&nbsp;&nbsp;</label></td>
          <td><input type="text" name="c_subject" size="70" maxlength=80 value="[{$oView->getContactSubject()}]" class="defaultcontent">&nbsp;<span class="req">*</span></td>
        </tr>
        <tr>
          <td><label>[{ oxmultilang ident="CONTACT_MESSAGE" }]&nbsp;&nbsp;</label></td>
          <td><textarea rows="15" cols="70" name="c_message">[{$oView->getContactMessage()}]</textarea></td>
        </tr>

        <tr>
          <td><label>[{ oxmultilang ident="CONTACT_VERIFICATIONCODE" }]</label></td>
          <td>
               [{assign var="oCaptcha" value=$oView->getCaptcha() }]
               [{if $oCaptcha->isImageVisible()}]
                 <div class="left"><img src="[{$oCaptcha->getImageUrl()}]" alt=""></div>
               [{else}]
                 <div id="test_verificationCode" class="verification_code">[{$oCaptcha->getText()}]</div>
               [{/if}]
               &nbsp;<input type="text" name="c_mac" value=""/>&nbsp;<span class="note">*</span>
          </td>
        </tr>

        <tr>
          <td></td>
          <td class="fs10">[{ oxmultilang ident="CONTACT_COMPLETEMARKEDFIELDS2" }]</td>
        </tr>
        <tr>
          <td></td>
          <td><br />
            <span class="btn"><input id="test_contactSend" type="submit" class="btn" value="[{ oxmultilang ident="CONTACT_SEND" }]"></span>
          </td>
        </tr>
      </table>
      </form>
  [{else}]
    [{ oxmultilang ident="CONTACT_THANKYOU1" }] [{ $oxcmp_shop->oxshops__oxname->value }][{ oxmultilang ident="CONTACT_THANKYOU2" }]<br /><br />
  [{/if}]
</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
