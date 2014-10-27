
[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function DeletePic( iIndex )
{
    var oForm = document.getElementById("myedit");
    oForm.fnc.value="deletePicture";
    oForm.masterPicIndex.value=iIndex;

    oForm.submit();
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



[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="article_pictures">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" enctype="multipart/form-data" action="[{ $oViewConf->getSelfLink() }]" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="article_pictures">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[article__oxid]" value="[{ $oxid }]">
<input type="hidden" name="voxid" value="[{ $oxid }]">
<input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
<input type="hidden" name="masterPicIndex" value="">

[{if $oViewConf->isAltImageServerConfigured() }]
    <div class="warning">[{ oxmultilang ident="ALTERNATIVE_IMAGE_SERVER_NOTE" }] [{ oxinputhelp ident="HELP_ALTERNATIVE_IMAGE_SERVER_NOTE" }]</div>
[{/if}]





    <table cellspacing="0" cellpadding="0" width="98%" border="0">
      <colgroup>
          <col width="1%" nowrap>
          <col width="99%">
      </colgroup>

      <tr>
        <td class="picPreviewCol" valign="top">
            [{assign var="sThumbUrl" value=$edit->getThumbnailUrl()}]

            <div class="picPreview">[{ if $sThumbUrl }]<img src="[{$sThumbUrl}]">[{ /if }]</div>
            <div class="picDescr">[{ oxmultilang ident="GENERAL_THUMB" }]</div>
            <br>
            <div class="picPreview" width="100%" align="center"><img src="[{$edit->getIconUrl()}]"></div>
            <div class="picDescr">[{ oxmultilang ident="GENERAL_ICON" }]</div>
        </td>

        <td class="picEditCol">

            <!-- ARTICLE MAIN PICTURES -->
            <table cellspacing="0" cellpadding="0" width="100%" border="0" class="listTable">
              [{block name="admin_article_pictures_main"}]
                  <colgroup>
                      <col width="2%">
                      <col width="1%" nowrap>
                      <col width="1%">
                      <col width="10%" nowrap>
                      <col width="95%">
                  </colgroup>
                  <tr>
                      <th colspan="5" valign="top">
                         [{ oxmultilang ident="GENERAL_ARTICLE_PICTURES" }] ([{ oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{ oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
                         [{ oxinputhelp ident="HELP_ARTICLE_PICTURES_PIC1" }]
                      </th>
                  </tr>

                 [{ if $oxparentid }]
                  <tr>
                    <td class="index" colspan="5">
                          <b>[{ oxmultilang ident="GENERAL_VARIANTE" }]</b>
                          <a href="Javascript:editThis('[{ $parentarticle->oxarticles__oxid->value}]');" class="edittext"><b>"[{ $parentarticle->oxarticles__oxartnum->value }] [{ $parentarticle->oxarticles__oxtitle->value }]"</b></a>
                    </td>
                  </tr>
                 [{/if}]

                  [{section name=picRow start=1 loop=$iPicCount+1 step=1}]
                  [{assign var="iIndex" value=$smarty.section.picRow.index}]

                  <tr>
                    <td class="index">
                        #[{$iIndex}]
                    </td>
                    <td class="text">
                        [{assign var="sPicFile" value=$edit->getPictureFieldValue("oxpic", $iIndex) }]
                        [{assign var="blPicUplodaded" value=true}]

                        [{if $sPicFile == "nopic.jpg" || $sPicFile == ""}]
                        [{assign var="blPicUplodaded" value=false}]
                        <span class="notActive">-------</span>
                        [{else}]
                        <b>[{$sPicFile}]</b>
                        [{/if}]

                    </td>
                    <td class="edittext">
                        <input class="editinput" name="myfile[M[{$iIndex}]@oxarticles__oxpic[{$iIndex}]]" type="file">
                    </td>
                    <td nowrap="nowrap">
                        [{if $blPicUplodaded && !$readonly }]
                        <a href="Javascript:DeletePic('[{$iIndex}]');" class="deleteText"><span class="ico"></span><span class="float: left;>">[{ oxmultilang ident="GENERAL_DELETE" }]</span></a>
                        [{/if}]
                    </td>
                    <td>

                        [{if $blPicUplodaded && !$readonly }]
                            [{assign var="sPicUrl" value=$edit->getPictureUrl($iIndex)}]
                            <a href="[{$sPicUrl}]" class="zoomText" target="_blank"><span class="ico"></span><span class="float: left;>">[{ oxmultilang ident="ARTICLE_PICTURES_PREVIEW" }]</span></a>
                        [{/if}]
                    </td>
                  </tr>

                  [{/section}]
              [{/block}]
            </table>

            <!-- CUSTOM PICTURES -->
            <table cellspacing="0" cellpadding="0" width="100%" border="0" class="listTable">
              [{block name="admin_article_pictures_custom"}]
                  <colgroup>
                      <col width="1%" nowrap>
                      <col width="1%" nowrap>
                      <col width="1%" nowrap>
                      <col width="98%">
                  </colgroup>
                  <tr>
                      <th colspan="5" valign="top">
                         [{ oxmultilang ident="ARTICLE_PICTURES_CUSTOM_PICTURES" }]
                      </th>
                  </tr>

                  <tr>
                    <td class="index" nowrap>
                        [{ oxmultilang ident="GENERAL_THUMB" }] ([{ oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{ oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
                        [{ oxinputhelp ident="HELP_ARTICLE_PICTURES_THUMB" }]
                    </td>
                    <td class="text">
                        [{assign var="sThumbFile" value=$edit->getPictureFieldValue("oxthumb")}]
                        [{if $sThumbFile == "nopic.jpg"  || $sThumbFile == "" }]
                        -------
                        [{else}]
                        [{assign var="blThumbUplodaded" value=true}]
                        <b>[{$sThumbFile}]</b>
                        [{/if}]
                    </td>
                    <td class="edittext">
                        <input class="editinput" name="myfile[TH@oxarticles__oxthumb]" type="file">
                    </td>
                    <td nowrap="nowrap">
                        [{if $blThumbUplodaded && !$readonly }]
                        <a href="Javascript:DeletePic('TH');" class="deleteText"><span class="ico"></span><span class="float: left;>">[{ oxmultilang ident="GENERAL_DELETE" }]</span></a>
                        [{/if}]
                    </td>
                  </tr>

                  <tr>
                    <td class="index" nowrap>
                        [{ oxmultilang ident="ARTICLE_PICTURES_ICON" }] ([{ oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{ oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
                        [{ oxinputhelp ident="HELP_ARTICLE_PICTURES_ICON" }]
                    </td>
                    <td class="text">
                        [{assign var="sIconFile" value=$edit->getPictureFieldValue("oxicon")}]
                        [{if "nopic_ico.jpg" == $sIconFile || "nopic.jpg" == $sIconFile || "" == $sIconFile }]
                        -------
                        [{else}]
                        [{assign var="blIcoUplodaded" value=true}]
                        <b>[{$sIconFile}]</b>
                        [{/if}]
                    </td>
                    <td class="edittext">
                        <input class="editinput" name="myfile[ICO@oxarticles__oxicon]" type="file">
                    </td>
                    <td nowrap="nowrap">
                        [{if $blIcoUplodaded && !$readonly }]
                        <a href="Javascript:DeletePic('ICO');" class="deleteText"><span class="ico"></span><span class="float: left;>">[{ oxmultilang ident="GENERAL_DELETE" }]</span></a>
                        [{/if}]
                    </td>
                  </tr>
              [{/block}]

            </table>

            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="ARTICLE_PICTURES_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"><br>


        </td>
      </tr>
   </table>


</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
