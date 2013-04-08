[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="adminlinks_main">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

      <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <colgroup><col width="20%"><col width="5%"><col width="75%"></colgroup>
        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" onSubmit="copyLongDesc( 'oxlinks__oxurldesc' );">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="adminlinks_main">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="voxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxlinks__oxid]" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxlinks__oxurldesc]" value="">
        [{* T workaround for #1552 *}]
        <tr>
          <td valign="top" class="edittext">
            <table cellspacing="0" cellpadding="0" border="0">
              [{block name="admin_adminlinks_main_form"}]
                  <tr>
                    <td class="edittext" width="90">
                    [{ oxmultilang ident="GENERAL_ACTIVE" }]&nbsp;
                    </td>
                    <td class="edittext">
                    <input class="edittext" type="checkbox" name="editval[oxlinks__oxactive]" value='1' [{if $edit->oxlinks__oxactive->value == 1}]checked[{/if}] [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_GENERAL_ACTIVE" }]
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext">
                    [{ oxmultilang ident="GENERAL_DATE" }]&nbsp;
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="30" name="editval[oxlinks__oxinsert]" value="[{$edit->oxlinks__oxinsert|oxformdate }]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_GENERAL_DATE" }]
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext">
                     [{ oxmultilang ident="GENERAL_URL" }]&nbsp;
                    </td>
                    <td class="edittext">
                    <input type="text" class="editinput" size="30" maxlength="[{$edit->oxlinks__oxurl->fldmax_length}]" name="editval[oxlinks__oxurl]" value="[{$edit->oxlinks__oxurl->value }]" [{ $readonly }]>
                    [{ oxinputhelp ident="HELP_GENERAL_URL" }]
                    </td>
                  </tr>
              [{/block}]
              <tr>
                <td class="edittext">
                </td>
                <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
                </td>
              </tr>
              <tr>
                <td class="edittext">
                </td>
                <td class="edittext"><br>
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]>
                </td>
              </tr>
            </table>
          </td>
        <td></td>
        <!-- Anfang rechte Seite -->
          <td valign="top" class="edittext vr" align="left">
              [{block name="admin_adminlinks_main_editor"}]
                  [{ $editor }]
              [{/block}]
          </td>
        </tr>
      </table>

</form>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
