[{assign var="template_title" value="GUESTBOOK_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

  <!-- page locator -->
  [{include file="inc/guestbook_locator.tpl" }]
  [{if !$oView->floodProtection() || !$oxcmp_user->oxuser__oxpassword->value}]
    <strong id="test_guestbookWriteHeader" class="boxhead">[{ oxmultilang ident="GUESTBOOK_WRITEENTRY" }]</strong>
    <div class="box info">
      [{ if $oxcmp_user->oxuser__oxpassword->value }]
        <form name="guestbook" action="[{ $oViewConf->getSelfLink() }]" method="post">
          <div>
              [{ $oViewConf->getHiddenSid() }]
              [{ $oViewConf->getNavFormParams() }]
              <input type="hidden" name="cl" value="guestbookentry">
              <span class="btn"><input type="submit" value="[{ oxmultilang ident="GUESTBOOK_CLICKHERETOWRITEENTRY" }]" class="btn"></span>
          </div>
        </form>
      [{else}]
        <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow"><b>[{ oxmultilang ident="GUESTBOOK_YOUHAVETOBELOGGED" }]</b></a>
      [{/if}]
    </div>
  [{/if}]

  <strong id="test_guestbookHeader" class="boxhead">[{ oxmultilang ident="GUESTBOOK_GUESTBOOK" }]</strong>
  <div class="box info">
    [{ if $oView->getEntries() }]
      <table width="100%" class="guestbook">
        <colgroup><col width="60%"><col width="25%"><col width="15%"></colgroup>
        [{foreach from=$oView->getEntries() item=entry}]
            <tr class="head">
                <td class="name">
                  <b>[{ $entry->oxuser__oxfname->value }]</b> [{ oxmultilang ident="GUESTBOOK_WRITES" }]
                </td>
                <td>
                  <b>[{ oxmultilang ident="GUESTBOOK_DATE" }]</b>&nbsp;[{ $entry->oxgbentries__oxcreate->value|date_format:"%d.%m.%Y" }]
                </td>
                <td>
                  <b>[{ oxmultilang ident="GUESTBOOK_TIME" }]</b>&nbsp;[{ $entry->oxgbentries__oxcreate->value|date_format:"%H:%M" }]
                </td>
            </tr>
            <tr class="body">
                <td colspan="3">[{ $entry->oxgbentries__oxcontent->value|nl2br }]</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
        [{/foreach}]
      </table>
    [{else}]
      <br>[{ oxmultilang ident="GUESTBOOK_NOENTRYAVAILABLE" }]<br>
    [{/if}]
  </div>
<!-- page locator -->
[{include file="inc/guestbook_locator.tpl" }]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
