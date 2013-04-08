[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]


  [{ if $shopid != "oxbaseshop" }]
    [{assign var="readonly" value="readonly disabled"}]
  [{else}]
    [{assign var="readonly" value=""}]
  [{/if}]

<script type="text/javascript">
<!--
function editThis( sID, sListType)
{
    var oTransfer = document.getElementById("transfer");
    oTransfer.oxid.value=sID;
    oTransfer.cl.value=sListType+'_main';
    oTransfer.submit();

    if (parent.list != null)
    {
        var oSearch = parent.list.document.getElementById("search");
        oSearch.sort.value = '';
        oSearch.cl.value=sListType+'_list';
        oSearch.actedit.value=0;
        oSearch.oxid.value=sID;
        oSearch.submit();
    }
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="pricealarm_main">
</form>


        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" onSubmit="copyLongDesc( 'oxpricealarm__oxlongdesc' );">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="pricealarm_main">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxpricealarm__oxid]" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxpricealarm__oxlongdesc]" value="">

        <table cellspacing="0" cellpadding="0" border="0" width="98%">
        [{if $edit}]
        [{assign var="oArticle" value=$edit->getArticle()}]
        <tr>
          <td valign="top" class="edittext" valign="top" style="padding-top:10px;padding-left:10px;">
            <table cellspacing="0" cellpadding="0" border="0" width="100%">
              [{block name="admin_pricealarm_main_summary"}]
                  [{if $mail_succ}]
                  <tr><td class="edittext" height="17" colspan="2"><b>[{ oxmultilang ident="PRICEALARM_MAIN_SUCCESS" }]</b></td></tr>
                  [{/if}]
                  [{if $mail_err}]
                  <tr><td class="edittext" height="17" colspan="2" style="color: #D81F01;"><b>[{ oxmultilang ident="PRICEALARM_MAIN_ERROR" }]</b></td></tr>
                  [{/if}]
                  <tr><td class="edittext" height="17"><b>[{ oxmultilang ident="PRICEALARM_MAIN_EMAIL" }]</b></td><td class="edittext">[{$edit->oxpricealarm__oxemail->value}]</td></tr>
                  <tr><td class="edittext" height="17"><b>[{ oxmultilang ident="PRICEALARM_MAIN_CUSTOMER" }]</b></td><td class="edittext" nowrap><a href="Javascript:editThis( '[{$edit->oUser->oxuser__oxid->value}]','user');" class="edittext">[{$edit->oUser->oxuser__oxlname->value}] [{$edit->oUser->oxuser__oxfname->value}]</a></td></tr>
                  <tr><td class="edittext" height="17"><b>[{ oxmultilang ident="GENERAL_LANGUAGE" }]</b></td><td class="edittext" nowrap>[{$edit_lang}]</td></tr>
                  <tr><td class="edittext" height="17" nowrap><b>[{ oxmultilang ident="PRICEALARM_MAIN_SUBSCRIPTIONDATE" }]&nbsp;&nbsp;</b></td><td class="edittext" nowrap>[{$edit->oxpricealarm__oxinsert|oxformdate}]</td></tr>
                  <tr><td class="edittext" height="17"><b>[{ oxmultilang ident="PRICEALARM_MAIN_MAILINGDATE" }]</b></td><td class="edittext">[{$edit->oxpricealarm__oxsended|oxformdate}]</td></tr>
                  <tr><td class="edittext" height="17"><b>[{ oxmultilang ident="PRICEALARM_MAIN_PRODUCT" }]</b></td><td class="edittext"><a href="Javascript:editThis( '[{$oArticle->oxarticles__oxid->value}]','article');" class="edittext">[{$edit->getTitle()}]</a></td></tr>
                  <tr><td class="edittext" height="17"><b>[{ oxmultilang ident="PRICEALARM_MAIN_CUSTOMERPRICE" }]</b></td><td class="edittext">[{$edit->getFProposedPrice()}] [{ $edit->oxpricealarm__oxcurrency->value }]</td></tr>
                  <tr><td class="edittext" height="17"><b>[{ oxmultilang ident="PRICEALARM_MAIN_REGULARPRICE" }]</b></td><td class="edittext">[{$oArticle->getFPrice()}] [{ $edit->oxpricealarm__oxcurrency->value }]</td></tr>
              [{/block}]
              <tr><td class="edittext" height="17"><br><br><br></td><td class="edittext">
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="PRICEALARM_MAIN_EMAILSEND" }]" onClick="Javascript:document.myedit.fnc.value='send'" [{$readonly }]>
              </td></tr>
            </table>
          </td>
          <td>&nbsp;&nbsp;&nbsp;</td>
          [{block name="admin_pricealarm_main_editor"}]
              <td valign="top" class="edittext" align="left">
                  [{ $editor }]
              </td>
          [{/block}]
        </tr>
        [{/if}]
      </table>
  </form>
[{include file="bottomnaviitem.tpl" }]

[{include file="bottomitem.tpl"}]
