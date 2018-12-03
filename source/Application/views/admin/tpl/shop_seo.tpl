[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
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
//-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{cycle assign="_clear_" values=",2"}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="shop_seo">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="shop_seo">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

   [{include file="include/update_views_notice.tpl"}]
   [{oxmultilang ident="SHOP_SEO_NEWINSTALL"}]<br>
   <br>

    <table border=0 width="98%">
        <tr>
          <td colspan="2">

            [{if $languages}]
            <FIELDSET id=fldLayout>
              <LEGEND id=lgdLayout>
                <select name="editlanguage" id="test_editlanguage" class="saveinnewlanginput" onChange="Javascript:document.myedit.submit();" [{$readonly}]>
                [{foreach from=$languages key=lang item=olang}]
                <option value="[{$lang}]"[{if $subjlang == $lang}]SELECTED[{/if}]>[{$olang->name}]</option>
                [{/foreach}]
                </select>
              </LEGEND>
            [{/if}]

            <table>
                [{block name="admin_shop_seo_prefix"}]
                    <tr>
                      <td class="edittext" >
                        [{oxmultilang ident="SHOP_SEO_TITLEPREFIX"}]
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxtitleprefix->fldmax_length}]" name="editval[oxshops__oxtitleprefix]" value="[{$edit->oxshops__oxtitleprefix->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_SHOP_SEO_TITLEPREFIX"}]
                      </td>
                    </tr>
                    <tr>
                      <td class="edittext" >
                        [{oxmultilang ident="SHOP_SEO_TITLESUFFIX"}]
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxtitlesuffix->fldmax_length}]" name="editval[oxshops__oxtitlesuffix]" value="[{$edit->oxshops__oxtitlesuffix->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_SHOP_SEO_TITLESUFFIX"}]
                      </td>
                    </tr>
                    <tr>
                      <td class="edittext" >
                        [{oxmultilang ident="SHOP_SEO_STARTTITLE"}]
                      </td>
                      <td class="edittext">
                        <input type="text" class="editinput" size="35" maxlength="[{$edit->oxshops__oxstarttitle->fldmax_length}]" name="editval[oxshops__oxstarttitle]" value="[{$edit->oxshops__oxstarttitle->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_SHOP_SEO_STARTTITLE"}]
                      </td>
                    </tr>
                [{/block}]
            </table>

            [{if $languages}]
            </FIELDSET>
            [{/if}]
            <br>
          </td>
        </tr>

        <tr class="conftext[{cycle}]">
         <td valign="top">
           <select class="saveinnewlanginput" name=confstrs[iDefSeoLang] [{$readonly}]>
             [{foreach from=$languages key=lang item=olang}]
             <option value="[{$lang}]"[{if $confstrs.iDefSeoLang == $lang}]SELECTED[{/if}]>[{$olang->name}]</option>
             [{/foreach}]
           </select>
         </td>
         <td valign="top" width="100%">
           [{oxmultilang ident="SHOP_SEO_DEFSEOLANGUAGE"}]
         </td>
        </tr>
        [{block name="admin_shop_seo_form"}]
            <tr class="conftext[{cycle}]">
             <td valign="top">
                <input type="hidden" name="confbools[blSEOLowerCaseUrls]" value="false">
                <input type="checkbox" name="confbools[blSEOLowerCaseUrls]" value="true" [{if ($confbools.blSEOLowerCaseUrls)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_SEO_LOWERCASEURLS"}]
             </td>
             <td valign="top" width="100%">
               [{oxmultilang ident="SHOP_SEO_LOWERCASEURLS"}]
             </td>
            </tr>
            <tr class="conftext[{cycle}]">
             <td valign="top" nowrap>
                <input type=text class="confinput" style="width:270px;" name=confstrs[sSEOSeparator] value="[{$confstrs.sSEOSeparator}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_SEO_IDSSEPARATOR"}]
             </td>
             <td valign="top" width="100%">
               [{oxmultilang ident="SHOP_SEO_IDSSEPARATOR"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
                <input type=text class="confinput" style="width:270px;" name=confstrs[sSEOuprefix] value="[{$confstrs.sSEOuprefix}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_SEO_SAFESEOPREF"}]
             </td>
             <td valign="top" width="100%">
               [{oxmultilang ident="SHOP_SEO_SAFESEOPREF"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
                <textarea class="confinput" style="width: 270px; height: 72px;" name=confarrs[aSEOReservedWords] [{$readonly}]>[{$confarrs.aSEOReservedWords}]</textarea>
                [{oxinputhelp ident="HELP_SHOP_SEO_RESERVEDWORDS"}]
             </td>
             <td valign="top" width="100%">
               [{oxmultilang ident="SHOP_SEO_RESERVEDWORDS"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
                <textarea class="confinput" style="width: 270px; height: 72px;" name=confarrs[aSkipTags] [{$readonly}]>[{$confarrs.aSkipTags}]</textarea><BR>
                [{oxinputhelp ident="HELP_SHOP_SEO_SKIPTAGS"}]
             </td>
             <td valign="top" width="100%">
               [{oxmultilang ident="SHOP_SEO_SKIPTAGS"}]
             </td>
            </tr>

            <tr>
             <td valign="top" class="conftext">
               <br><b>[{oxmultilang ident="SHOP_SEO_STATICURLS"}]</b>
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top" class="nowrap">
               <select class="confinput" style="width:270px;" name=aStaticUrl[oxseo__oxobjectid] [{$readonly}] onchange="document.myedit.submit();">
                 <option value="-1">[{oxmultilang ident="SHOP_SEO_NEWSTATICURL"}]</option>
                 [{foreach from=$aStaticUrls item=oItem}]

                   [{if $sActSeoObject && $sActSeoObject != '-1' && $oItem->oxseo__oxobjectid->value == $sActSeoObject}]
                     [{assign var="oActItem" value=$oItem}]
                   [{/if}]

                 <option value="[{$oItem->oxseo__oxobjectid->value}]" [{if $oItem->oxseo__oxobjectid->value == $sActSeoObject}]selected[{/if}]>[{$oItem->oxseo__oxstdurl->getRawValue()}]</option>
                 [{/foreach}]
               </select>
               [{oxinputhelp ident="HELP_SHOP_SEO_STATICURLS"}]
             </td>
             <td>
              [{if $oActItem}]
              <a href="#" onclick="document.myedit.fnc.value='deleteStaticUrl';document.myedit.submit();" [{$readonly}] class="delete left" [{include file="help.tpl" helpid=item_delete}]></a>
              [{/if}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td>
              <input type=text class="confinput" style="width:270px;" name="aStaticUrl[oxseo__oxstdurl]" id="oxseo__oxstdurl" value="[{if $oActItem->oxseo__oxstdurl}][{$oActItem->oxseo__oxstdurl->getRawValue()}][{/if}]" [{$readonly}]>
              [{oxinputhelp ident="HELP_SHOP_SEO_STDURL"}]
             </td>
             <td>
               [{oxmultilang ident="SHOP_SEO_STDURL"}]
             </td>
            </tr>

            [{foreach from=$languages key=lang item=olang}]
            <tr class="conftext[{cycle}]">
             <td>
              <input type=text class="confinput" style="width:270px;" name="aStaticUrl[oxseo__oxseourl][[{$lang}]]" value="[{$aSeoUrls.$lang.1}]" [{$readonly}]>
             </td>
             <td>
              [{$olang->name}]
             </td>
            </tr>
            [{/foreach}]
        [{/block}]

    </table>

   <br>
   <input type="submit" class="confinput" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
   <input type="submit" class="confinput" name="save" value="[{oxmultilang ident="SHOP_SEO_RESETIDS"}]" onClick="Javascript:var agree=confirm('[{oxmultilang ident="SHOP_SEO_QRESETIDS"}]');if (!agree) {return false;} else {document.myedit.fnc.value='dropSeoIds';return true;}" [{$readonly}]>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
