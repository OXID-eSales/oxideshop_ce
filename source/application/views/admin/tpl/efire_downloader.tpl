[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{ if $message}]<div class="messagebox">[{ $message }]</div>[{/if}]

<p>
[{ oxmultilang ident="EFIRE_GETCONNECTOR" }]
</p>

<p>
[{ oxmultilang ident="EFIRE_CONNECTORINSTRUCTION" }]
</p>


<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
  [{ $oViewConf->getHiddenSid() }]
  <input type=hidden name=cl value=efire_downloader>
  <input type=hidden name=fnc value=getConnector>
<p>
[{ oxmultilang ident="EFIRE_USERNAME" }]:<br>
<input type="text" name=etUsername value="[{$sEfiUsername}]" [{ $readonly }]>

<p>
[{ oxmultilang ident="EFIRE_PASSWORD" }]:<br>
<input type="password" name=etPassword value="[{$sEfiPassword}]" [{ $readonly }]>

<p>

<input type="hidden" name="blSaveCredentials" value="0">
<input type="checkbox" name="blSaveCredentials" value="1" checked [{ $readonly }]>
[{ oxmultilang ident="EFIRE_SAVECREDENTIALS" }]

<p>
<input type="Submit" name="etSubmit" value="[{ oxmultilang ident="BUTTON_DOWNLOAD" }]" [{ $readonly }]>
</form>
<br />
[{assign var="blWhite" value=""}]
[{assign var="ctr" value="1"}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr>
    <td class="listheader first" height="15" colspan="4">[{ oxmultilang ident="EFIRE_USERDETAILS" }]</td>
    <td class="listheader">[{ oxmultilang ident="EFIRE_USERPASSHASH" }]</td>
  </tr>
[{foreach from=$oView->getAdminList() item=oUser }]
  <tr>
    <td class="listitem[{ $blWhite }]">&nbsp;[{ $ctr }]&nbsp;</td>
    <td class="listitem[{ $blWhite }]">&nbsp;[{ $oUser->oxuser__oxusername->value }]</td>
    <td class="listitem[{ $blWhite }]">&nbsp;[{ $oUser->oxuser__oxfname->value }]</td>
    <td class="listitem[{ $blWhite }]">&nbsp;[{ $oUser->oxuser__oxlname->value }]</td>
    <td class="listitem[{ $blWhite }]">&nbsp;[{ $oUser->getPasswordHash() }]</td>
  </tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{assign var="ctr" value=`$ctr+1`}]
[{/foreach}]
</table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
