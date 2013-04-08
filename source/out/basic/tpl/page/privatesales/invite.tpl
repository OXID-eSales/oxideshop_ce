[{assign var="template_title" value="INVITE_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]
[{assign var="product" value=$oView->getProduct()}]

<strong id="test_recommendHeader" class="boxhead">[{$template_title}]</strong>
[{ if !$oView->getInviteSendStatus() }]
  [{assign var="editval" value=$oView->getInviteData()}]
  <div class="box info" >
    [{ oxmultilang ident="INVITE_RECOMMENDSITE" }]<br><br>
    [{ oxmultilang ident="INVITE_ENTERFRIENDSEMAILS" }]
    <br>
    <div class="dot_sep mid"></div>

    <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          <input type="hidden" name="fnc" value="send">
          <input type="hidden" name="cl" value="invite">
          <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
          <input type="hidden" name="CustomError" value='invite'>
          [{assign var="oCaptcha" value=$oView->getCaptcha() }]
          <input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]"/>
          <table>
            <tr>
              <td><b>[{ oxmultilang ident="INVITE_SENDTO" }]</b></td>
              <td ></td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_RECIPIENTEMAIL" }] #1&nbsp;<span class="note">*</span>:</td>
              <td><input type="text" name="editval[rec_email][1]" size=73 maxlength=73 value="[{$editval->rec_email.1}]" ></td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_RECIPIENTEMAIL" }] #2:</td>
              <td><input type="text" name="editval[rec_email][2]" size=73 maxlength=73 value="[{$editval->rec_email.2}]" ></td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_RECIPIENTEMAIL" }] #3:</td>
              <td><input type="text" name="editval[rec_email][3]" size=73 maxlength=73 value="[{$editval->rec_email.3}]" ></td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_RECIPIENTEMAIL" }] #4:</td>
              <td><input type="text" name="editval[rec_email][4]" size=73 maxlength=73 value="[{$editval->rec_email.4}]" ></td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_RECIPIENTEMAIL" }] #5:</td>
              <td><input type="text" name="editval[rec_email][5]" size=73 maxlength=73 value="[{$editval->rec_email.5}]" ></td>
            </tr>
            <tr>
              <td><br></td>
              <td><br></td>
            </tr>
            <tr>
              <td><b>[{ oxmultilang ident="INVITE_FROM" }]</b></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_SENDERNAME" }]&nbsp;<span class="note">*</span></td>
              <td><input type="text" name="editval[send_name]" size=73 maxlength=73 value="[{$editval->send_name}]" ></td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_SENDEREMAIL" }]&nbsp;<span class="note">*</span></td>
              <td><input type="text" name="editval[send_email]" size=73 maxlength=73 value="[{$editval->send_email}]" ></td>
            </tr>
            <tr>
              <td>[{ oxmultilang ident="INVITE_CAPTION" }]&nbsp;<span class="note">*</span></td>
              <td><input type="text" name="editval[send_subject]" size=73 maxlength=73 value="[{if $editval->send_subject}][{$editval->send_subject}][{else}][{ oxmultilang ident="INVITE_SUBJECT" }] [{ $product->oxarticles__oxtitle->value|strip_tags }][{/if}]" ></td>
            </tr>
            <tr>
              <td valign="top">[{ oxmultilang ident="INVITE_YOURMESSAGE" }]&nbsp;<span class="note">*</span></td>
              <td>
                <textarea cols="70" rows="8" name="editval[send_message]" >[{if $editval->send_message}][{$editval->send_message}][{else}][{ oxmultilang ident="INVITE_MESSAGE1" }] [{ $oxcmp_shop->oxshops__oxname->value }] [{ oxmultilang ident="INVITE_MESSAGE2" }][{/if}]</textarea>
              </td>
            </tr>
            <tr>
              <td><label>[{ oxmultilang ident="INVITE_VERIFICATIONCODE" }]</label></td>
              <td>
               [{assign var="oCaptcha" value=$oView->getCaptcha() }]
               [{if $oCaptcha->isImageVisible()}]
                 <div class="left"><img src="[{$oCaptcha->getImageUrl()}]" alt=""></div>
               [{else}]
                 <div id="test_verificationCode" class="verification_code">[{$oCaptcha->getText()}]</div>
               [{/if}]
               &nbsp;<input type="text" name="c_mac" value="">&nbsp;<span class="note">*</span>
              </td>
            </tr>
            <tr>
              <td></td>
              <td align="right"><span class="btn"><input  type="submit" value="[{ oxmultilang ident="INVITE_SEND" }]" class="btn"></span></td>
            </tr>
        </table>
      </div>
    </form>
    <div class="dot_sep mid"></div>
    [{oxifcontent ident="oxsecurityinfo" object="oCont"}]
    [{ oxmultilang ident="INVITE_ABOUTDATAPROTECTION" }] <a id="test_infoProtection" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a><br>
    [{/oxifcontent}]
  </div>
[{else}]
  <div class="box info">
    [{ oxmultilang ident="INVITE_EMAILWASSENT" }]<br><br>
[{/if}]

<div class="clear_left">
    &nbsp;
</div>


[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
