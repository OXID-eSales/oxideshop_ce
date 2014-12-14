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
        <input type="hidden" name="cl" value="dyn_trusted">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxshops__oxid]" value="[{ $oxid }]">
            [{ if $errorsaving }]
                <tr>
                  <td colspan="3">
                    [{ if $errorsaving eq 1 }]
                      [{ if $errormessage }]
                        <div class="error">[{ oxmultilang ident="DYN_TRUSTED_"|cat:$errormessage }]</div>
                      [{else}]
                        <div class="error">[{ oxmultilang ident="DYN_TRUSTED_TRUSTEDSHOP_ERROR" }]</div>
                      [{/if}]
                    [{/if}]
                  </td>
                </tr>
                [{ /if}]
            <tr>
             <td align="left" class="saveinnewlangtext">
                [{ oxmultilang ident="GENERAL_LANGUAGE" }]&nbsp;&nbsp;
             </td>
               <td valign="left" class="edittext">
                    [{ oxmultilang ident="DYN_TRUSTED_TRUSTEDSHOP" }]&nbsp;&nbsp;
               </td>
               <td class="edittext">
              </td>
            </tr>
            [{foreach from=$alllang key=lang item=language}]
            <tr>
              <td align="left">
                [{ $language }]
              </td>
              <td valign="left" class="edittext">
                   <input type=text class="editinput" style="width:270px" name="aShopID_TrustedShops[[{$lang}]]" value="[{$aShopID_TrustedShops.$lang}]" maxlength="40" [{ $readonly }]>
                   [{ oxinputhelp ident="HELP_DYN_TRUSTED_TSID" }]
                </td>
                <td class="[{if $aShopID_TrustedShops.$lang != ''}] active[{/if}]">
                <div class="listitemfloating">&nbsp;</div>
              </td>
            </tr>
            <tr>
              <td align="left">
                &nbsp;&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="DYN_TRUSTED_USER" }]
              </td>
              <td valign="left" class="edittext">
                 <input type=text class="editinput" style="width:270px" name="aTsUser[[{$lang}]]" value="[{$aTsUser.$lang}]" maxlength="40" [{ $readonly }]>
                 [{ oxinputhelp ident="HELP_DYN_TRUSTED_USER" }]
              </td>
              <td class="edittext">
              </td>
            </tr>
            <tr>
              <td align="left">
                &nbsp;&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="DYN_TRUSTED_PASSWORD" }]
              </td>
              <td valign="left" class="edittext">
                 <input type=text class="editinput" style="width:270px" name="aTsPassword[[{$lang}]]" value="[{$aTsPassword.$lang}]" maxlength="40" [{ $readonly }]>
                 [{ oxinputhelp ident="HELP_DYN_TRUSTED_PASSWORD" }]
              </td>
              <td class="edittext">
              </td>
            </tr>
            [{/foreach}]
            <tr>
              <td align="left">
                [{ oxmultilang ident="DYN_TRUSTED_TESTMODUS" }]
              </td>
              <td valign="left" class="edittext">
                 <input type=hidden name="tsTestMode" value=false>
                 <input type=checkbox name="tsTestMode" value=true  [{if $tsTestMode}]checked[{/if}]>
                 [{ oxinputhelp ident="HELP_DYN_TRUSTED_TESTMODUS" }]
              </td>
              <td class="edittext">
              </td>
            </tr>
            <tr>
              <td align="left">
                [{ oxmultilang ident="DYN_TRUSTED_ACTIVE" }]
                 <br><br>
              </td>
              <td valign="left" class="edittext">
                 <input type=hidden name="tsSealActive" value=false>
                 <input type=checkbox name="tsSealActive" value=true  [{if $tsSealActive}]checked[{/if}]>
                 [{ oxinputhelp ident="HELP_DYN_TRUSTED_ACTIVE" }]
                 <br><br>
              </td>
              <td class="edittext">
              </td>
            </tr>
            <tr>
              <td align="left">
                [{ oxmultilang ident="DYN_TRUSTED_SHOPPAYMENT" }]
              </td>
              <td valign="left">
                 [{ oxmultilang ident="DYN_TRUSTED_TSPAYMENT" }]
                 [{ oxinputhelp ident="HELP_DYN_TRUSTED_TSPAYMENT" }]
              </td>
              <td class="edittext">
              </td>
            </tr>
            [{foreach from=$shoppaymenttypes item=payment}]
            <tr>
              <td align="left">
                [{ $payment->oxpayments__oxdesc->value }]
              </td>
              <td valign="left" class="edittext">
                 <select name="paymentids[[{$payment->oxpayments__oxid->value}]]" class="editinput" [{ $readonly}]>
                    [{foreach from=$tspaymenttypes item=tspayment}]
                    [{assign var="ident" value=DYN_TRUSTED_$tspayment}]
                    [{assign var="ident" value=$ident|oxupper }]
                    <option value="[{$tspayment}]" [{if $payment->oxpayments__oxtspaymentid->value == $tspayment }]SELECTED[{/if}]>[{ oxmultilang ident=$ident }]</option>
                    [{/foreach}]
                 </select>
              </td>
              <td class="edittext">
              </td>
            </tr>
            [{/foreach}]
            <tr>
              <td class="edittext">
              </td>
              <td class="edittext"><br>
                <input type="submit" class="confinput" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'; return true;" [{ $readonly }]>
              </td>
              <td class="edittext">
              </td>
            </tr>
            </form>
        </table>
</div>
[{include file="bottomnaviitem.tpl" }]
[{include file="bottomitem.tpl"}]
