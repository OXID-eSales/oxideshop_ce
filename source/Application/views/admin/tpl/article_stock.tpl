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
    <input type="hidden" name="cl" value="article_stock">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>
<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="article_stock">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="voxid" value="[{ $oxid }]">
    <input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
    <input type="hidden" name="editval[article__oxid]" value="[{ $oxid }]">
    <table cellspacing="0" cellpadding="0" border="0" style="width:100%;">
        <tr>
          <td valign="top" class="edittext" style="padding-left:10px;width:50%">
            <table cellspacing="0" cellpadding="0" border="0">
              [{block name="admin_article_stock_form"}]
                  [{if $oxparentid}]
                  <tr>
                    <td class="edittext" width="160">
                      <b>[{oxmultilang ident="GENERAL_VARIANTE"}]</b>
                    </td>
                    <td class="edittext">
                      <a href="Javascript:editThis('[{$parentarticle->oxarticles__oxid->value}]');" class="edittext"><b>[{$parentarticle->oxarticles__oxartnum->value}] [{$parentarticle->oxarticles__oxtitle->value}]</b></a>
                    </td>
                  </tr>
                  [{/if}]
                  <tr>
                    <td class="edittext">
                      [{oxmultilang ident="ARTICLE_STOCK_STOCK"}]
                    </td>
                    <td class="edittext">
                      <input type="text" class="editinput" size="20" maxlength="[{$edit->oxarticles__oxstock->fldmax_length}]" name="editval[oxarticles__oxstock]" value="[{$edit->oxarticles__oxstock->value}]" [{include file="help.tpl" helpid=article_stock}] [{$readonly}]>
                      [{oxinputhelp ident="HELP_ARTICLE_STOCK_STOCK"}]
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext">
                      [{oxmultilang ident="ARTICLE_STOCK_STOCKFLAG"}]
                    </td>
                    <td class="edittext">
                      <select name="editval[oxarticles__oxstockflag]" class="editinput" [{$readonly}]>
                        <option value="1" [{if $edit->oxarticles__oxstockflag->value == 1}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_STANDARD"}]</option>
                        <option value="4" [{if $edit->oxarticles__oxstockflag->value == 4}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_EXTERNALSTOCK"}]</option>
                        <option value="2" [{if $edit->oxarticles__oxstockflag->value == 2}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_OFFLINE"}]</option>
                        <option value="3" [{if $edit->oxarticles__oxstockflag->value == 3}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_NONORDER"}]</option>
                      </select>
                      [{oxinputhelp ident="HELP_ARTICLE_STOCK_STOCKFLAG"}]
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext">
                    [{oxmultilang ident="ARTICLE_STOCK_DELIVERY"}]
                    </td>
                    <td class="edittext">
                      <input type="text" class="editinput" size="20" maxlength="[{$edit->oxarticles__oxdelivery->fldmax_length}]" name="editval[oxarticles__oxdelivery]" value="[{$edit->oxarticles__oxdelivery|oxformdate}]" [{include file="help.tpl" helpid=article_delivery}] [{$readonly}]>
                      [{oxinputhelp ident="HELP_ARTICLE_STOCK_DELIVERY"}]
                    </td>
                  </tr>

                <tr>
                  <td class="edittext">
                    [{oxmultilang ident="ARTICLE_STOCK_DELTIME"}]
                  </td>
                  <td class="edittext">
                    [{oxmultilang ident="ARTICLE_STOCK_MINDELTIME"}]&nbsp;<input type="text" class="editinput" size="3" maxlength="[{$edit->oxarticles__oxmindeltime->fldmax_length}]" name="editval[oxarticles__oxmindeltime]" value="[{$edit->oxarticles__oxmindeltime->value}]">

                    [{oxmultilang ident="ARTICLE_STOCK_MAXDELTIME"}]&nbsp;<input type="text" class="editinput" size="3" maxlength="[{$edit->oxarticles__oxmaxdeltime->fldmax_width}]" name="editval[oxarticles__oxmaxdeltime]" value="[{$edit->oxarticles__oxmaxdeltime->value}]">

                    &nbsp;<select name="editval[oxarticles__oxdeltimeunit]" class="editinput">
                    <option value="DAY" [{if $edit->oxarticles__oxdeltimeunit->value == "DAY"}]SELECTED[{/if}]>[{oxmultilang ident="ARTICLE_STOCK_DAYS"}]</option>
                    <option value="WEEK" [{if $edit->oxarticles__oxdeltimeunit->value == "WEEK"}]SELECTED[{/if}]>[{oxmultilang ident="ARTICLE_STOCK_WEEKS"}]</option>
                    <option value="MONTH" [{if $edit->oxarticles__oxdeltimeunit->value == "MONTH"}]SELECTED[{/if}]>[{oxmultilang ident="ARTICLE_STOCK_MONTHS"}]</option>
                    </select>
                  [{oxinputhelp ident="HELP_ARTICLE_STOCK_DELTIME"}]
                  </td>
                </tr>

                  <tr>
                    <td class="edittext wrap">
                      [{oxmultilang ident="ARTICLE_STOCK_REMINDACTIV"}]
                    </td>
                    <td class="edittext">
                      <input type="checkbox" class="editinput" name="editval[oxarticles__oxremindactive]" value='[{if $edit->oxarticles__oxremindactive->value}][{$edit->oxarticles__oxremindactive->value}][{else}]1[{/if}]' [{if $edit->oxarticles__oxremindactive->value}]checked[{/if}] [{$readonly}] [{if $oxparentid}]readonly disabled[{/if}]>
                      [{oxinputhelp ident="HELP_ARTICLE_STOCK_REMINDACTIV"}]
                      <input type="text" class="editinput" size="20" maxlength="[{$edit->oxarticles__oxremindamount->fldmax_length}]" name="editval[oxarticles__oxremindamount]" value="[{$edit->oxarticles__oxremindamount->value}]" [{$readonly}]>
                      [{oxinputhelp ident="HELP_ARTICLE_STOCK_REMINDAMAOUNT"}]
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext" colspan="2"><br>
                      <fieldset title="[{oxmultilang ident="GENERAL_ARTICLE_OXSTOCKTEXT"}]" style="padding-left: 5px;">
                      <legend>[{oxmultilang ident="GENERAL_ARTICLE_OXSTOCKTEXT"}]</legend><br>
                      <table>
                        <tr>
                          <td class="edittext">
                            [{oxmultilang ident="GENERAL_LANGUAGE"}]
                          </td>
                          <td class="edittext">
                             <select name="editlanguage" id="test_editlanguage" class="editinput" onChange="Javascript:loadLang(this);" [{$readonly}] [{$readonly_fields}]>
                             [{foreach from=$otherlang key=lang item=olang}]
                             <option value="[{$lang}]"[{if $olang->selected}]SELECTED[{/if}]>[{$olang->sLangDesc}]</option>
                             [{/foreach}]
                             </select>
                             [{oxinputhelp ident="HELP_GENERAL_LANGUAGE"}]
                          </td>
                        </tr>
                        <tr>
                          <td class="edittext">
                            [{oxmultilang ident="ARTICLE_STOCK_STOCKTEXT"}]
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="40" maxlength="[{$edit->oxarticles__oxstocktext->fldmax_length}]" name="editval[oxarticles__oxstocktext]" value="[{$edit->oxarticles__oxstocktext->value}]" [{$readonly}]>
                            [{oxinputhelp ident="HELP_ARTICLE_STOCK_STOCKTEXT"}]
                          </td>
                        </tr>
                        <tr>
                          <td class="edittext">
                            [{oxmultilang ident="ARTICLE_STOCK_NOSTOCKTEXT"}]
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="40" maxlength="[{$edit->oxarticles__oxnostocktext->fldmax_length}]" name="editval[oxarticles__oxnostocktext]" value="[{$edit->oxarticles__oxnostocktext->value}]" [{$readonly}]>
                            [{oxinputhelp ident="HELP_ARTICLE_STOCK_NOSTOCKTEXT"}]
                          </td>
                        </tr>
                      </table>
                      </fieldset>
                    </td>
                  </tr>
              [{/block}]
              <tr>
                <td class="edittext" colspan="2"><br><br>
                  <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}]><br>
                </td>
              </tr>
            </table>
          </td>
            <!-- Start right column -->
          <td valign="top" class="edittext" style="padding-top:10px;padding-left:10px;width:50%">
            <fieldset title="[{oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_TITLE"}]" style="padding-left: 5px; padding-right: 5px;">
            <legend>[{oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_TITLE"}]</legend><br>
            <table cellspacing="0" cellpadding="1" border="0" >
            [{assign var=oddclass value="2"}]
            [{foreach from=$amountprices item=amountprice}]
              [{if is_array($errorscaleprice) && in_array($amountprice->oxprice2article__oxid->value, $errorscaleprice)}]
              <tr>
                  <td colspan="3">
                      <div class="errorbox">[{oxmultilang ident="ARTICLE_STOCK_ERRORSCALEPRICE"}]</div>
                  </td>
              </tr>
              [{/if}]
              <tr>
              [{if $oddclass == 2}]
                [{assign var=oddclass value=""}]
              [{else}]
                [{assign var=oddclass value="2"}]
              [{/if}]
                <td class="listitem[{$oddclass}]" nowrap>
                    [{ oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_AMOUNTFROM" }]
                    <input type="text" size="6" name="updateval[[{$amountprice->oxprice2article__oxid->value}]][oxprice2article__oxamount]" value="[{$amountprice->oxprice2article__oxamount->value}]" />
                    [{ oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_AMOUNTTO" }]
                    <input type="text" size="6" name="updateval[[{$amountprice->oxprice2article__oxid->value}]][oxprice2article__oxamountto]" value="[{$amountprice->oxprice2article__oxamountto->value}]" />
                </td>
                <td class="listitem[{$oddclass}]" nowrap>
                    [{ oxmultilang ident="ARTICLE_STOCK_PRICE" }]
                    <select class="edittext" name="updateval[[{$amountprice->oxprice2article__oxid->value}]][pricetype]">
                    <option value="oxprice2article__oxaddabs" [{if $amountprice->oxprice2article__oxaddabs->value}] selected="selected" [{/if}]>[{ oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_ABS" }]
                    <option value="oxprice2article__oxaddperc" [{if $amountprice->oxprice2article__oxaddperc->value}] selected="selected" [{/if}]>[{ oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_DISCOUNT" }]
                    </select>
                    [{if $amountprice->oxprice2article__oxaddabs->value}]
                    <input class="edittext" size="6" type="text" name="updateval[[{$amountprice->oxprice2article__oxid->value}]][price]" value="[{$amountprice->oxprice2article__oxaddabs->value}]" />
                    [{elseif $amountprice->oxprice2article__oxaddperc->value }]
                    <input class="edittext" size="6" type="text" name="updateval[[{$amountprice->oxprice2article__oxid->value}]][price]" value="[{$amountprice->oxprice2article__oxaddperc->value}]" />
                    [{/if}]
                </td>
                <td class=listitem[{$oddclass}]>
                  <a href="[{$oViewConf->getSelfLink()}]&cl=article_stock&priceid=[{$amountprice->oxprice2article__oxid->value}]&fnc=deleteprice&oxid=[{$oxid}]" onClick='return confirm("[{oxmultilang ident="GENERAL_YOUWANTTODELETE"}]")' class="delete"></a>
                </td>
              </tr>
            [{/foreach}]
            [{if count( $amountprices ) > 0}]
            <tr>
                <td colspan=3><br>
                    <input type="submit" class="edittext" name="saveAll" value="[{ oxmultilang ident="ARTICLE_STOCK_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='updateprices'"><br><br>
                </td>
            </tr>
            <tr>
                <td colspan=3>
                    <hr />
                </td>
            </tr>
            [{/if}]
              <tr>
                <td class="edittext" colspan=3>
                  <table>
                    [{block name="admin_article_stock_scaleprice"}]
                        <tr>
                          <td class="edittext">
                            [{oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_AMOUNTFROM"}]
                          </td>
                          <td class="edittext">
                            <input class="edittext" type="text" name="editval[oxprice2article__oxamount]">
                          </td>
                          <td class="edittext">
                            [{ oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_AMOUNTTO" }]
                          </td>
                          <td class="edittext">
                            <input class="edittext" type="text" name="editval[oxprice2article__oxamountto]">
                            [{oxinputhelp ident="HELP_ARTICLE_STOCK_AMOUNTPRICE_AMOUNTFROM"}]
                          </td>
                        </tr>
                        <tr>
                          <td class="edittext">
                            [{oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_PRICE"}] ([{$oActCur->sign}])
                          </td>
                          <td class="edittext" nowrap colspan=3>
                            <select  class="edittext" name="editval[pricetype]">
                              <option value="oxprice2article__oxaddabs">[{oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_ABS"}]
                              <option value="oxprice2article__oxaddperc">[{oxmultilang ident="ARTICLE_STOCK_AMOUNTPRICE_DISCOUNT"}]
                            </select>
                            <input class="edittext" type="text" name="editval[price]">
                            [{oxinputhelp ident="HELP_ARTICLE_STOCK_AMOUNTPRICE_PRICE"}]
                          </td>
                          <td>
                          </td>
                        </tr>
                    [{/block}]
                  </table>
                </td>
              </tr>
              <tr>
                <td colspan=3><br>
                  <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="ARTICLE_STOCK_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='addprice'""><br><br>
                </td>
              </tr>
            </table>
            </fieldset>
          </td>
        </tr>
    </table>
</form>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
