[{assign var="template_title" value="GUESTBOOKENTRY_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title }]

<!-- page locator -->
[{include file="inc/guestbook_locator.tpl" }]
[{if !$oView->floodProtection()}]
  <strong class="boxhead">[{ oxmultilang ident="GUESTBOOKENTRY_WRITEENTRY" }]</strong>
  <div class="box">
    <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          [{oxid_include_dynamic file="dyn/formparams.tpl" }]
          <input type="hidden" name="fnc" value="saveEntry">
          <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
          <label>[{ oxmultilang ident="GUESTBOOKENTRY_YOURMESSAGE" }]&nbsp;</label><br>
          <textarea cols="66" rows="15" name="rvw_txt" ></textarea><br>
          <span class="btn"><input type="submit" value="[{ oxmultilang ident="GUESTBOOKENTRY_SEND" }]" class="btn"></span>
      </div>
    </form>
  </div>
[{/if}]
<strong class="boxhead">[{ oxmultilang ident="GUESTBOOKENTRY_GUESTBOOK" }]</strong>
<div class="box">
  [{ if $oView->getEntries() }]
    <table style="margin-right:-3px;min-width:100%;width:94%;">
      <colgroup><col width="60%"><col width="25%"><col width="15%"></colgroup>
      [{foreach from=$oView->getEntries() item=entry}]
        <tr><td colspan="3"><div class="categoryline"></div></td></tr>
        <tr>
          <td class="fontblack font11">
            <span class="fontbold">[{ $entry->oxuser__oxfname->value }]</span> [{ oxmultilang ident="GUESTBOOKENTRY_WRITES" }]
          </td>
          <td class="fontgray1 font11">
            <span class="fontgray1 fontbold">[{ oxmultilang ident="GUESTBOOKENTRY_DATE" }]</span>&nbsp;[{ $entry->oxgbentries__oxcreate->value|date_format:"%d.%m.%Y" }]
          </td>
          <td class="fontgray1 font11">
            <span class="fontgray1 fontbold">[{ oxmultilang ident="GUESTBOOKENTRY_TIME" }]</span>&nbsp;[{ $entry->oxgbentries__oxcreate->value|date_format:"%H:%M" }]
          </td>
        </tr>
        <tr><td colspan="3"><div class="categoryline"></div></td></tr>
        <tr><td class="fontblack" colspan="3">[{ $entry->oxgbentries__oxcontent->value|nl2br }]<br><br></td></tr>
        <tr></tr>
      [{/foreach}]
    </table>
  [{else}]
    <br>[{ oxmultilang ident="GUESTBOOKENTRY_NOENTRYAVAILABLE" }]<br>
  [{/if}]
</div>
<!-- page locator -->
[{include file="inc/guestbook_locator.tpl" }]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
