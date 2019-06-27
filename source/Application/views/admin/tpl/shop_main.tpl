[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function loadLang(obj)
{
    var langvar = document.getElementById("agblang");
    if (langvar != null )
        langvar.value = obj.value;
    document.myedit.submit();
}
function setSmtpField()
{
    var sPass = '';
    for ( var i = 0; i < [{$edit->oxshops__oxsmtppwd->value|count_characters}]; i++ ) {
        sPass += ' ';
    }
    document.getElementsByName( 'oxsmtppwd' )[0].value = sPass;
    document.getElementsByName( 'oxsmtppwd' )[0].userValueSet = false;
}
function unsetSmtpField()
{
    if ( !document.getElementsByName( 'oxsmtppwd' )[0].userValueSet ) {
        document.getElementsByName( 'oxsmtppwd' )[0].value = '';
    }
}

function modSmtpField()
{
    if ( !document.getElementsByName( 'oxsmtppwd' )[0].userValueSet ) {
        document.getElementsByName( 'oxsmtppwd' )[0].value = '';
        document.getElementsByName( 'oxsmtppwd' )[0].userValueSet = true;
    }
}
function editThis(sID)
{
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.oxid.value = '';
    oTransfer.cl.value = top.oxid.admin.getClass( sID );

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = top.basefrm.list.document.getElementById( "search" );
    oSearch.oxid.value = sID;
    oSearch.updatenav.value = 1;
    oSearch.submit();
}
window.onload = function ()
{
    [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
    [{/if}]

    setSmtpField();

    [{if $updatenav}]
        top.oxid.admin.reloadNavigation('[{$oxid}]');
    [{/if}]

    var oField = top.oxid.admin.getLockTarget();
    oField.onchange = oField.onkeyup = oField.onmouseout = top.oxid.admin.unlockSave;
}
//-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="shop_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

[{if $sMandateWarning}]
  <div class="errorbox">[{oxmultilang ident="SHOP_MAIN_MANDATE_WARNING"}]</div>
[{/if}]

[{if $sMaxShopWarning}]
   <div class="errorbox">[{oxmultilang ident="SHOP_MAIN_MAXSHOP_WARNING"}]</div>
[{/if}]

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post" onSubmit="unsetSmtpField()">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="shop_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

<table border="0" width="98%">
<tr>
    <td valign="top" class="edittext">
        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_shop_main_rightform"}]
            <tr>
             <td class="edittext"  [{if !($edit->oxshops__oxproductive->value)}]style="border: 3px Red; border-style: solid none solid solid;"[{/if}]>
                [{oxmultilang ident="SHOP_MAIN_PRODUCTIVE"}]
             </td>
             <td class="edittext" [{if !($edit->oxshops__oxproductive->value)}]style="border: 3px Red; border-style: solid solid solid none;"[{/if}]>
                <input type=checkbox name=editval[oxshops__oxproductive] value=true  [{if ($edit->oxshops__oxproductive->value)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_PRODUCTIVE"}]
             </td>
            </tr>
            <tr>
             <td class="edittext" >
                [{oxmultilang ident="GENERAL_ACTIVE"}]
             </td>
             <td class="edittext" >
                <input type=checkbox name=editval[oxshops__oxactive] value=true  [{if ($edit->oxshops__oxactive->value)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
             </td>
            </tr>
            <tr>
                <td class="edittext" >
                   [{oxmultilang ident="SHOP_MAIN_COMPANY"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxcompany->fldmax_length}]" name="editval[oxshops__oxcompany]" value="[{$edit->oxshops__oxcompany->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_COMPANY"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" width="100">
                    [{oxmultilang ident="GENERAL_NAME"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="10" maxlength="[{$edit->oxshops__oxfname->fldmax_length}]" name="editval[oxshops__oxfname]" value="[{$edit->oxshops__oxfname->value}]" [{$readonly}]>
                <input type="text" class="editinput" size="21" maxlength="[{$edit->oxshops__oxlname->fldmax_length}]" name="editval[oxshops__oxlname]" value="[{$edit->oxshops__oxlname->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_NAME"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_STREET"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxstreet->fldmax_length}]" name="editval[oxshops__oxstreet]" value="[{$edit->oxshops__oxstreet->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_STREET"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_ZIPCITY"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="5" maxlength="[{$edit->oxshops__oxzip->fldmax_length}]" name="editval[oxshops__oxzip]" value="[{$edit->oxshops__oxzip->value}]" [{$readonly}]>
                <input type="text" class="editinput" size="26" maxlength="[{$edit->oxshops__oxcity->fldmax_length}]" name="editval[oxshops__oxcity]" value="[{$edit->oxshops__oxcity->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ZIPCITY"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_COUNTRY"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxcountry->fldmax_length}]" name="editval[oxshops__oxcountry]" value="[{$edit->oxshops__oxcountry->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_COUNTRY"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_TELEPHONE"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxtelefon->fldmax_length}]" name="editval[oxshops__oxtelefon]" value="[{$edit->oxshops__oxtelefon->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_TELEPHONE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_FAX"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxtelefax->fldmax_length}]" name="editval[oxshops__oxtelefax]" value="[{$edit->oxshops__oxtelefax->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_FAX"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_URL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxurl->fldmax_length}]" name="editval[oxshops__oxurl]" value="[{$edit->oxshops__oxurl->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_URL"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_BANKNAME"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxbankname->fldmax_length}]" name="editval[oxshops__oxbankname]" value="[{$edit->oxshops__oxbankname->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_BANKNAME"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_BANKCODE"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxbankcode->fldmax_length}]" name="editval[oxshops__oxbankcode]" value="[{$edit->oxshops__oxbankcode->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_BANKCODE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_BANKNUMBER"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxbanknumber->fldmax_length}]" name="editval[oxshops__oxbanknumber]" value="[{$edit->oxshops__oxbanknumber->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_BANKNUMBER"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_BICCODE"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxbiccode->fldmax_length}]" name="editval[oxshops__oxbiccode]" value="[{$edit->oxshops__oxbiccode->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_BICCODE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_IBANNUMBER"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxibannumber->fldmax_length}]" name="editval[oxshops__oxibannumber]" value="[{$edit->oxshops__oxibannumber->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_IBANNUMBER"}]
                </td>
            </tr>

            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_VATNUMBER"}]
                </td>
                <td class="edittext">
                <input type="text" name="editval[oxshops__oxvatnumber]" value="[{$edit->oxshops__oxvatnumber->value}]" size="35" maxlength="[{$edit->oxshops__oxvatnumber->fldmax_length}]" class="editinput" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_VATNUMBER"}]
                </td>
            </tr>

            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_TAXNUMBER"}]
                </td>
                <td class="edittext">
                <input type="text" name="editval[oxshops__oxtaxnumber]" value="[{$edit->oxshops__oxtaxnumber->value}]" size="35" maxlength="[{$edit->oxshops__oxtaxnumber->fldmax_length}]" class="editinput" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_TAXNUMBER"}]
                </td>
            </tr>

            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_HRBNR"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxhrbnr->fldmax_length}]" name="editval[oxshops__oxhrbnr]" value="[{$edit->oxshops__oxhrbnr->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_HRBNR"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_COURT"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxcourt->fldmax_length}]" name="editval[oxshops__oxcourt]" value="[{$edit->oxshops__oxcourt->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_COURT"}]
                </td>
            </tr>
        [{/block}]
        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left">
        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_shop_main_leftform"}]
            [{include file="include/shop_information.tpl"}]
            <tr>
                <td class="edittext" >
                    [{oxmultilang ident="SHOP_MAIN_SHOPNAME"}]
                </td>
                <td class="edittext">
                    <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxname->fldmax_length}]" name="editval[oxshops__oxname]" value="[{$edit->oxshops__oxname->value}]" id="oLockTarget" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_MAIN_SHOPNAME"}]
                </td>
            </tr>
            [{if !$IsOXDemoShop}]
            <tr>
                <td class="edittext" >
                            [{oxmultilang ident="SHOP_MAIN_SMTPSERVER"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxsmtp->fldmax_length}]" name="editval[oxshops__oxsmtp]" value="[{$edit->oxshops__oxsmtp->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_SMTPSERVER"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                            [{oxmultilang ident="SHOP_MAIN_SMTPUSER"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxsmtpuser->fldmax_length}]" name="editval[oxshops__oxsmtpuser]" value="[{$edit->oxshops__oxsmtpuser->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_SMTPUSER"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                            [{oxmultilang ident="SHOP_MAIN_SMTPPASSWORD"}]
                </td>
                <td class="edittext">
                <input type="password" name="oxsmtppwd" size="35" maxlength="50" class="editinput" [{$readonly}] onfocus="modSmtpField()" onChange="modSmtpField()">
                [{oxmultilang ident="SHOP_MAIN_SMTPPWUNSET"}]
                [{oxinputhelp ident="HELP_SHOP_MAIN_SMTPPASSWORD"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                            [{oxmultilang ident="SHOP_MAIN_INFOEMAIL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxinfoemail->fldmax_length}]" name="editval[oxshops__oxinfoemail]" value="[{$edit->oxshops__oxinfoemail->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_INFOEMAIL"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                            [{oxmultilang ident="SHOP_MAIN_ORDEREMAIL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxorderemail->fldmax_length}]" name="editval[oxshops__oxorderemail]" value="[{$edit->oxshops__oxorderemail->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_ORDEREMAIL"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" >
                            [{oxmultilang ident="SHOP_MAIN_OWNEREMAIL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxowneremail->fldmax_length}]" name="editval[oxshops__oxowneremail]" value="[{$edit->oxshops__oxowneremail->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_MAIN_OWNEREMAIL"}]
                </td>
            </tr>
            [{/if}]
        [{/block}]

          <tr>
            <td colspan="2">
              <FIELDSET id=fldLayout>
                <LEGEND id=lgdLayout>
                  [{if $languages}]
                  <select name="subjlang" class="editinput" onchange="Javascript:loadLang(this)" [{$readonly}]>
                      [{foreach key=key item=item from=$languages}]
                        <option value="[{$key}]"[{if $subjlang == $key}] SELECTED[{/if}]>[{$item->name}]</option>
                      [{/foreach}]
                  </select>
                  [{/if}]
                </LEGEND>

              <table cellspacing="0" cellpadding="1" border="0">
                [{block name="admin_shop_main_emailsubject"}]
                    <tr>
                      <td class="edittext" >
                        [{oxmultilang ident="SHOP_MAIN_ORDERSUBJECT"}]
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxordersubject->fldmax_length}]" name="editval[oxshops__oxordersubject]" value="[{$edit->oxshops__oxordersubject->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_SHOP_MAIN_ORDERSUBJECT"}]
                      </td>
                    </tr>
                    <tr>
                      <td class="edittext" >
                        [{oxmultilang ident="SHOP_MAIN_REGISTERSUBJECT"}]
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxregistersubject->fldmax_length}]" name="editval[oxshops__oxregistersubject]" value="[{$edit->oxshops__oxregistersubject->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_SHOP_MAIN_REGISTERSUBJECT"}]
                      </td>
                    </tr>
                    <tr>
                      <td class="edittext" >
                        [{oxmultilang ident="SHOP_MAIN_FORGOTPWDSUBJECT"}]
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxforgotpwdsubject->fldmax_length}]" name="editval[oxshops__oxforgotpwdsubject]" value="[{$edit->oxshops__oxforgotpwdsubject->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_SHOP_MAIN_FORGOTPWDSUBJECT"}]
                      </td>
                    </tr>
                    <tr>
                      <td class="edittext" >
                        [{oxmultilang ident="SHOP_MAIN_SENT_NOW_SUBJECT"}]
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxsendednowsubject->fldmax_length}]" name="editval[oxshops__oxsendednowsubject]" value="[{$edit->oxshops__oxsendednowsubject->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_SHOP_MAIN_NOWSENDEDSUBJECT"}]
                    </tr>
                [{/block}]
              </table>
              </FIELDSET>
            </td>
          </tr>
          <tr>
            <td class="edittext"></td>
            <td class="edittext"><br>
              <input type="submit" class="edittext" id="oLockButton" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{if $oxid==-1}]disabled[{/if}] [{$readonly}]>
            </td>
          </tr>
        </table>

    </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
