[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<div id="liste">

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>

    <table cellspacing="0" cellpadding="0" border="0">
    <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="dyn_trusted_ratings">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{ $oxid }]">

    [{ if $errorsaving }]
    <tr>
      <td colspan="3">
        <div class="error">[{ oxmultilang ident=$errorsaving }]</div>
      </td>
    </tr>
    [{ /if}]
    <tr>
      <td colspan="3">[{ oxmultilang ident="DYN_TRUSTED_RATINGS_CONFIGFILE" }]</td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" colspan="3">
        <p>[{ oxmultilang ident="DYN_TRUSTED_RATINGS_ID_COMMENT" }]</p>
      </td>
    </tr>
    [{foreach from=$alllang key=lang item=language}]
    [{assign var="abbr" value=$language->abbr }]
    <tr>
      <td align="left">
        [{ oxmultilang ident="DYN_TRUSTED_RATINGS_ID" }] [{ $language->name }]
      </td>
      <td valign="left" class="edittext">
        <input type="text" class="editinput" style="width:270px" name="confaarrs[aTsLangIds][[{$abbr}]]" value="[{$confaarrs.aTsLangIds.$abbr}]" maxlength="33" [{ $readonly }]>
        [{ oxinputhelp ident="HELP_DYN_TRUSTED_RATINGS_ID" }]
      </td>
      <td class="[{if $confarrs.aTsActiveLangIds.$abbr}] active[{/if}]">
        <div class="listitemfloating">&nbsp;</div>
      </td>
    </tr>
    [{/foreach}]
    <tr>
      <td align="left">
        [{ oxmultilang ident="DYN_TRUSTED_RATINGS_WIDGET" }]
      </td>
      <td valign="left" class="edittext">
        <input type=hidden name=confbools[blTsWidget] value="false">
        <input type="checkbox" class="editinput" name="confbools[blTsWidget]" [{if $confbools.blTsWidget}]checked[{/if}] value="true" [{ $readonly }]>
        [{ oxinputhelp ident="HELP_DYN_TRUSTED_RATINGS_WIDGET" }]
      </td>
      <td></td>
    </tr>

    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" colspan="3">
        <p>[{ oxmultilang ident="DYN_TRUSTED_RATINGS_COMMENT" }]</p>
      </td>
    </tr>
    <tr>
      <td align="left" colspan="3">
        [{ oxmultilang ident="DYN_TRUSTED_RATINGS" }]
      </td>
    </tr>
    <tr>
      <td align="left">
        [{ oxmultilang ident="DYN_TRUSTED_RATINGS_THANKYOU" }]
      </td>
      <td valign="left" class="edittext">
        <input type=hidden name=confbools[blTsThankyouReview] value="false">
        <input type="checkbox" class="editinput" name="confbools[blTsThankyouReview]" [{if $confbools.blTsThankyouReview}]checked[{/if}] value="true" [{ $readonly }]>
        [{ oxinputhelp ident="HELP_DYN_TRUSTED_RATINGS_THANKYOU" }]
      </td>
      <td></td>
    </tr>
    <tr>
      <td align="left">
        [{ oxmultilang ident="DYN_TRUSTED_RATINGS_ORDEREMAIL" }]
      </td>
      <td valign="left" class="edittext">
        <input type=hidden name=confbools[blTsOrderEmailReview] value="false">
        <input type="checkbox" class="editinput" name="confbools[blTsOrderEmailReview]" [{if $confbools.blTsOrderEmailReview}]checked[{/if}] value="true" [{ $readonly }]>
        [{ oxinputhelp ident="HELP_DYN_TRUSTED_RATINGS_ORDEREMAIL" }]
      </td>
      <td></td>
    </tr>
    <tr>
      <td align="left">
        [{ oxmultilang ident="DYN_TRUSTED_RATINGS_ORDERSENDEMAIL" }]
      </td>
      <td valign="left" class="edittext">
        <input type=hidden name=confbools[blTsOrderSendEmailReview] value="false">
        <input type="checkbox" class="editinput" name="confbools[blTsOrderSendEmailReview]" [{if $confbools.blTsOrderSendEmailReview}]checked[{/if}] value="true" [{ $readonly }]>
        [{ oxinputhelp ident="HELP_DYN_TRUSTED_RATINGS_ORDERSENDEMAIL" }]
      </td>
      <td></td>
    </tr>
    <tr>
      <td class="edittext">
      </td>
      <td class="edittext" colspan="2"><br>
        <input type="submit" class="confinput" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'; return true;" [{ $readonly }]>
      </td>
      <td></td>
    </tr>
    </form>
</table>

</div>

[{include file="bottomnaviitem.tpl" }]
[{include file="bottomitem.tpl"}]