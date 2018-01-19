[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function editThis( sID )
{
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.oxid.value = sID;
    oTransfer.cl.value = top.basefrm.list.sDefClass;

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = top.basefrm.list.document.getElementById( "search" );
    oSearch.oxid.value = sID;
    oSearch.actedit.value = 0;
    oSearch.submit();
}
[{if !$oxparentid}]
window.onload = function ()
{
    [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
    [{/if}]
    var oField = top.oxid.admin.getLockTarget();
    oField.onchange = oField.onkeyup = oField.onmouseout = top.oxid.admin.unlockSave;
}
[{/if}]
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
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="article_main">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

    <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post" onSubmit="return copyLongDesc('oxarticles__oxlongdesc');" style="padding: 0px;margin: 0px;height:0px;">
      [{$oViewConf->getHiddenSid()}]
      <input type="hidden" name="cl" value="article_main">
      <input type="hidden" name="fnc" value="">
      <input type="hidden" name="oxid" value="[{$oxid}]">
      <input type="hidden" name="voxid" value="[{$oxid}]">
      <input type="hidden" name="oxparentid" value="[{$oxparentid}]">
      <input type="hidden" name="editval[oxarticles__oxid]" value="[{$oxid}]">
      <input type="hidden" name="editval[oxarticles__oxlongdesc]" value="">
      <table cellspacing="0" cellpadding="0" border="0" style="width:98%;">
        <tr>
          <td valign="top" class="edittext" style="padding-top:10px;padding-left:10px;">
            <table cellspacing="0" cellpadding="0" border="0">
              [{block name="admin_article_main_form"}]
                  [{if $errorsavingatricle}]
                  <tr>
                    <td colspan="2">
                      [{if $errorsavingatricle eq 1}]
                      <div class="errorbox">[{oxmultilang ident="ARTICLE_MAIN_ERRORSAVINGARTICLE"}]</div>
                      [{/if}]
                    </td>
                  </tr>
                  [{/if}]
                  [{block name="admin_article_main_extended_errorbox"}][{/block}]
                  [{if $oxparentid}]
                  <tr>
                    <td class="edittext" width="120">
                        <b>[{oxmultilang ident="ARTICLE_MAIN_VARIANTE"}]</b>
                    </td>
                    <td class="edittext">
                      <a href="Javascript:editThis('[{$parentarticle->oxarticles__oxid->value}]');" class="edittext"><b>[{$parentarticle->oxarticles__oxartnum->value}] [{$parentarticle->oxarticles__oxtitle->value}] [{if !$parentarticle->oxarticles__oxtitle->value}][{$parentarticle->oxarticles__oxvarselect->value}][{/if}]</b></a>
                    </td>
                  </tr>
                  [{/if}]

                    <tr>
                      <td class="edittext" width="120">
                        [{oxmultilang ident="ARTICLE_MAIN_ACTIVE"}]
                      </td>
                      <td class="edittext">
                        <input type="hidden" name="editval[oxarticles__oxactive]" value="0">
                        <input class="edittext" type="checkbox" name="editval[oxarticles__oxactive]" value='1' [{if $edit->oxarticles__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
                        [{oxmultilang ident="ARTICLE_MAIN_HIDDEN"}]&nbsp;&nbsp;&nbsp;
                        <input type="hidden" name="editval[oxarticles__oxhidden]" value="0">
                        <input class="edittext" type="checkbox" name="editval[oxarticles__oxhidden]" value='1' [{if $edit->oxarticles__oxhidden->value == 1}]checked[{/if}] [{$readonly}]>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_ACTIVE"}]
                      </td>
                    </tr>

                    [{if $blUseTimeCheck}]
                    <tr>
                      <td class="edittext">
                        [{ oxmultilang ident="ARTICLE_MAIN_ACTIVFROMTILL" }]&nbsp;
                      </td>
                      <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_ACTIVEFROM"}]&nbsp;<input type="text" class="editinput" size="27" name="editval[oxarticles__oxactivefrom]" value="[{$edit->oxarticles__oxactivefrom|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]><br>
                        [{oxmultilang ident="ARTICLE_MAIN_ACTIVETO"}]&nbsp;&nbsp;<input type="text" class="editinput" size="27" name="editval[oxarticles__oxactiveto]" value="[{$edit->oxarticles__oxactiveto|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_ACTIVFROMTILL"}]
                      </td>
                    </tr>
                    [{/if}]

                    <tr>
                      <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_TITLE"}]&nbsp;
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxtitle->fldmax_length}]" id="oLockTarget" name="editval[oxarticles__oxtitle]" value="[{$edit->oxarticles__oxtitle->value}]">
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_TITLE"}]
                      </td>
                    </tr>
                    <tr>
                      <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_ARTNUM"}]&nbsp;
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxartnum->fldmax_length}]" name="editval[oxarticles__oxartnum]" value="[{$edit->oxarticles__oxartnum->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_ARTNUM"}]
                      </td>
                    </tr>

                    <tr>
                      <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_EAN"}]&nbsp;
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxean->fldmax_length}]" name="editval[oxarticles__oxean]" value="[{$edit->oxarticles__oxean->value}]">
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_EAN"}]
                      </td>
                    </tr>

                    <tr>
                      <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_DISTEAN"}]&nbsp;
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxdistean->fldmax_length}]" name="editval[oxarticles__oxdistean]" value="[{$edit->oxarticles__oxdistean->value}]">
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_DISTEAN"}]
                      </td>
                    </tr>

                    <tr>
                      <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_MPN"}]&nbsp;
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxmpn->fldmax_length}]" name="editval[oxarticles__oxmpn]" value="[{$edit->oxarticles__oxmpn->value}]">
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_MPN"}]
                      </td>
                    </tr>

                  <tr>
                    <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_SHORTDESC"}]&nbsp;
                    </td>
                    <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxshortdesc->fldmax_length}]" name="editval[oxarticles__oxshortdesc]" value="[{$edit->oxarticles__oxshortdesc->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_SHORTDESC"}]
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_SEARCHKEYS"}]&nbsp;
                    </td>
                    <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxsearchkeys->fldmax_length}]" name="editval[oxarticles__oxsearchkeys]" value="[{$edit->oxarticles__oxsearchkeys->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_SEARCHKEYS"}]
                    </td>
                  </tr>

                  [{block name="admin_article_main_extended"}][{/block}]

                  <tr>
                    <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_VENDORID"}]
                    </td>
                    <td class="edittext">
                        <select class="editinput" name="editval[oxarticles__oxvendorid]" [{$readonly}]>
                        <option value="" selected>---</option>
                        [{foreach from=$oView->getVendorList() item=oVendor}]
                        <option value="[{$oVendor->oxvendor__oxid->value}]"[{if $edit->oxarticles__oxvendorid->value == $oVendor->oxvendor__oxid->value}] selected[{/if}]>[{ $oVendor->oxvendor__oxtitle->value }]</option>
                        [{/foreach}]
                        </select>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_VENDORID"}]
                    </td>
                  </tr>

                  <tr>
                    <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_MANUFACTURERID"}]
                    </td>
                    <td class="edittext">
                        <select class="editinput" name="editval[oxarticles__oxmanufacturerid]" [{$readonly}]>
                        <option value="" selected>---</option>
                        [{foreach from=$oView->getManufacturerList() item=oManufacturer }]
                        <option value="[{$oManufacturer->oxmanufacturers__oxid->value}]"[{if $edit->oxarticles__oxmanufacturerid->value == $oManufacturer->oxmanufacturers__oxid->value}] selected[{/if}]>[{ $oManufacturer->oxmanufacturers__oxtitle->value }]</option>
                        [{/foreach}]
                        </select>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_MANUFACTURERID"}]
                    </td>
                  </tr>

                  [{if $edit->isParentNotBuyable()}]
                  <tr>
                    <td colspan="2">
                      <div class="errorbox">[{oxmultilang ident="ARTICLE_MAIN_PARENTNOTBUYABLE"}]</div>
                    </td>
                  </tr>
                  [{/if}]
                    <tr>
                      <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_PRICE"}] ([{$oActCur->sign}])
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="8" maxlength="[{$edit->oxarticles__oxprice->fldmax_length}]" name="editval[oxarticles__oxprice]" value="[{$edit->oxarticles__oxprice->value}]" [{$readonly}]>
                        [{assign var="oPrice" value=$edit->getPrice()}]
                        &nbsp;<em>( [{$oPrice->getBruttoPrice()}] )</em>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_PRICE"}]
                      </td>
                    </tr>

                  <tr>
                    <td class="edittext">
                    [{oxmultilang ident="ARTICLE_MAIN_ALDPRICE"}] ([{$oActCur->sign}])
                    </td>
                    <td class="edittext" nowrap>
                        [{oxmultilang ident="ARTICLE_MAIN_PRICEA"}]: <input type="text" class="editinput" size="4" maxlength="[{$edit->oxarticles__oxpricea->fldmax_length}]" name="editval[oxarticles__oxpricea]" value="[{$edit->oxarticles__oxpricea->value}]" [{$readonly}]>
                        [{oxmultilang ident="ARTICLE_MAIN_PRICEB"}]: <input type="text" class="editinput" size="4" maxlength="[{$edit->oxarticles__oxpriceb->fldmax_length}]" name="editval[oxarticles__oxpriceb]" value="[{$edit->oxarticles__oxpriceb->value}]" [{$readonly}]>
                        [{oxmultilang ident="ARTICLE_MAIN_PRICEC"}]: <input type="text" class="editinput" size="4" maxlength="[{$edit->oxarticles__oxpricec->fldmax_length}]" name="editval[oxarticles__oxpricec]" value="[{$edit->oxarticles__oxpricec->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_ALDPRICE"}]
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext">
                        [{oxmultilang ident="ARTICLE_MAIN_VAT"}]
                    </td>
                    <td class="edittext">
                        <input type="text" class="editinput" size="10" maxlength="[{$edit->oxarticles__oxvat->fldmax_length}]" name="editval[oxarticles__oxvat]" value="[{$edit->oxarticles__oxvat->value}]" [{include file="help.tpl" helpid=article_vat}] [{$readonly}]>
                        [{oxinputhelp ident="HELP_ARTICLE_MAIN_VAT"}]
                    </td>
                  </tr>

              [{/block}]

              <tr>
                <td class="edittext" colspan="2"><br><br>
                <input type="submit" class="edittext" id="oLockButton" name="saveArticle" value="[{oxmultilang ident="ARTICLE_MAIN_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{if !$edit->oxarticles__oxtitle->value && !$oxparentid}]disabled[{/if}] [{$readonly}]>
                [{if $oxid!=-1 && !$readonly}]
                  <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="ARTICLE_MAIN_ARTCOPY"}]" onClick="Javascript:document.myedit.fnc.value='copyArticle';" [{$readonly}]>&nbsp;&nbsp;&nbsp;
                [{/if}]
                </td>
              </tr>
              [{if $oxid == -1}]
                <tr>
                  <td class="edittext">
                [{oxmultilang ident="ARTICLE_MAIN_INCATEGORY"}]:
                </td>
                <td class="edittext">
                <select name="art_category" class="editinput" onChange="Javascript:top.oxid.admin.changeLstrt()" [{$readonly}]>
                <option value="-1">[{oxmultilang ident="ARTICLE_MAIN_NONE"}]</option>
                [{foreach from=$oView->getCategoryList() item=pcat}]
                <option value="[{$pcat->oxcategories__oxid->value}]">[{$pcat->oxcategories__oxtitle->getRawValue()|oxtruncate:40:"..":true}]</option>
                [{/foreach}]
                </select>
                [{oxinputhelp ident="HELP_"}]
                </td>
              </tr>
              [{/if}]
              <tr>
                <td class="edittext" colspan="2"><br>
                [{include file="language_edit.tpl"}]<br>
                </td>
              </tr>
              [{if $oxid!=-1 && $thisvariantlist}]
              <tr>
                <td class="edittext">[{oxmultilang ident="ARTICLE_MAIN_GOTO"}]</td>
                <td class="edittext">
                [{include file="variantlist.tpl"}]
                </td>
              </tr>
              [{/if}]
            </table>
          </td>
    <!-- Anfang rechte Seite -->
          <td valign="top" class="edittext" align="left" style="width:100%;height:99%;padding-left:5px;padding-bottom:30px;padding-top:10px;">
            [{block name="admin_article_main_editor"}]
              [{include file="include/editor.tpl"}]
            [{/block}]
          </td>
    <!-- Ende rechte Seite -->
        </tr>
      </table>
    </form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
