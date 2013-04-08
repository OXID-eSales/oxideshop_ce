[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]
[{assign var="edit" value=$oView->getArticle()}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
    top.reloadEditFrame();
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
function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="article_files">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

[{assign var="oFiles" value=$edit->getArticleFiles()}]
<table cellspacing="0" cellpadding="0" border="0" width="98%">
    [{if count( $oFiles ) > 0 }]
        <colgroup>
            <col width="100%">
        </colgroup>
    [{/if}]
     <tr>
        <td valign="top" class="edittext" [{if count( $oFiles ) > 0 }]align="left"[{/if}]>
          <form name="newFileUpload" id="newFileUpload" action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post">
          <input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
          [{$oViewConf->getHiddenSid()}]
          <input type="hidden" name="cl" value="article_files">
          <input type="hidden" name="fnc" value="">
          <input type="hidden" name="oxid" value="[{ $oxid }]">
          <input type="hidden" name="voxid" value="[{ $oxid }]">
          <input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
          <input type="hidden" name="editval[article__oxid]" value="[{ $oxid }]">
          <fieldset title="New file upload" style="padding-left: 5px;">
            <table cellspacing="0" cellpadding="0" border="0" width="98%">
              [{block name="admin_article_downloads_newform"}]
                  <tr>
                   <td class="edittext">
                      [{ oxmultilang ident="ARTICLE_FILES_ENTER_FILENAME" }] [{ oxinputhelp ident="HELP_ARTICLE_FILES_NEW" }] <input class="edittext" type="text" name="newfile[oxfiles__oxfilename]" class="edittext" [{$readonly}]> [{ oxmultilang ident="ARTICLE_FILES_OR" }] ([{ oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}]) <input type="file" name="newArticleFile" class="edittext" [{$readonly}]>
                    </td>
                  </tr>
                  <tr>
                    <td class="edittext">[{ oxmultilang ident="ARTICLE_FILES_NEW_PURCHASEDONLY" }]
                        <input class="edittext" type="hidden" name="newfile[oxfiles__oxpurchasedonly]" value='0'>
                        <input class="edittext" type="checkbox" checked name="newfile[oxfiles__oxpurchasedonly]" value='1' [{ $readonly }]>
                    </td>
                  </tr>
              [{/block}]
              [{block name="admin_article_downloads_newform_options"}]
              <tr>
                <td>
                    <div class="groupExp">
                        <div>
                            <a href="#" onclick="_groupExp(this);return false;" class="rc" style="line-height: 30px;"><b>[{ oxmultilang ident="ARTICLE_OTHER_OPTIONS" }]</b></a>
                             <dl style="padding-top: 5px;">
                                <table cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="edittext">[{ oxmultilang ident="GENERAL_MAX_DOWNLOADS_COUNT" }]</td>
                                        <td class="edittext">
                                            <input type=text class="txt" name="newfile[oxfiles__oxmaxdownloads]">
                                            [{ oxinputhelp ident="HELP_ARTICLE_FILES_MAX_DOWNLOADS_COUNT" }]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="edittext">[{ oxmultilang ident="GENERAL_LINK_EXPIRATION_TIME_UNREGISTERED" }]</td>
                                        <td class="edittext">
                                            <input type=text class="txt" name="newfile[oxfiles__oxmaxunregdownloads]">
                                            [{ oxinputhelp ident="HELP_ARTICLE_FILES_LINK_EXPIRATION_TIME_UNREGISTERED" }]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="edittext">[{ oxmultilang ident="GENERAL_LINK_EXPIRATION_TIME" }]</td>
                                        <td class="edittext">
                                            <input type=text class="txt" name="newfile[oxfiles__oxlinkexptime]">
                                            [{ oxinputhelp ident="HELP_ARTICLE_FILES_LINK_EXPIRATION_TIME" }]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="edittext">[{ oxmultilang ident="GENERAL_DOWNLOAD_EXPIRATION_TIME" }]</td>
                                        <td class="edittext">
                                            <input type=text class="txt" name="newfile[oxfiles__oxdownloadexptime]">
                                            [{ oxinputhelp ident="HELP_ARTICLE_FILES_DOWNLOAD_EXPIRATION_TIME" }]
                                        </td>
                                    </tr>
                                </table>
                            </dl>
                         </div>
                    </div>
                  </td>
                </tr>
              [{/block}]
                <tr>
                  <td>
                    <input type="submit" class="saveButton" value="[{ oxmultilang ident="ARTICLE_FILES_NEW_UPLOAD" }]" onclick="Javascript:document.newFileUpload.fnc.value='upload'">
                  </td>
                </tr>
            </table>
          </fieldset>
        </form>
    </td>
  </tr>
  <tr>
      <td><hr/></td>
  </tr>
  <tr>
      <td>
          <table cellspacing="0" cellpadding="0" border="0" width="98%">
              <tr>
                  <td valign="top" class="edittext">
                      <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
                          [{$oViewConf->getHiddenSid()}]
                          <input type="hidden" name="cl" value="article_files">
                          <input type="hidden" name="fnc" value="">
                          <input type="hidden" name="oxid" value="[{ $oxid }]">
                          <input type="hidden" name="editval[article__oxid]" value="[{ $oxid }]">
                          <input type="hidden" name="voxid" value="[{ $oxid }]">
                          <input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
                          <table cellspacing="0" cellpadding="0" border="0" width="98%">
                              <tr>
                                  <td class="edittext" width="120">
                                      [{ oxmultilang ident="ARTICLE_FILES_ISDOWNLOADABLE" }]
                                  </td>
                                  <td class="edittext">
                                      <input class="edittext" type="hidden" name="editval[oxarticles__oxisdownloadable]" value='0'>
                                      <input class="edittext" type="checkbox" name="editval[oxarticles__oxisdownloadable]" value='1' [{if $edit->oxarticles__oxisdownloadable->value == 1}]checked[{/if}] [{if $oxparentid }]readonly disabled[{/if}]>
                                      [{ oxinputhelp ident="HELP_ARTICLE_IS_DOWNLOADABLE" }]
                                  </td>
                              </tr>
                          </table>
                          [{if count( $oFiles ) > 0 }]
                                <p><b>[{ oxmultilang ident="ARTICLE_FILES_TABLE_UPLOADEDFILES" }]</b></p>
                                    [{foreach from=$oFiles item=oArticleFile}]
                                        [{ if $readonly || !$oArticleFile->isUploaded() }]
                                            [{assign var="readonlyRename" value="readonly disabled"}]
                                        [{else}]
                                            [{assign var="readonlyRename" value=""}]
                                        [{/if}]

                                        [{block name="admin_article_downloads_filelist"}]
                                            <div class="groupExp">
                                                <div>
                                                    <a class="delete" href="[{$oViewConf->getSelfLink()}]&cl=article_files&amp;fileid=[{$oArticleFile->getId()}]&amp;fnc=deletefile&amp;oxid=[{$oxid}]&amp;editlanguage=[{ $editlanguage }]" onClick='return confirm("[{ oxmultilang ident="GENERAL_YOUWANTTODELETE" }]")'></a>
                                                    <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{$oArticleFile->oxfiles__oxfilename->value}]</b></a>
                                                    <dl style="padding-top:5px;">
                                                        <table cellspacing="0" cellpadding="0" border="0">
                                                            <tr>
                                                                <td class="edittext">[{ oxmultilang ident="ARTICLE_FILES_TABLE_FILENAME" }]</td>
                                                                <td class="edittext">
                                                                    <input type="text" class="editinput" size="40" maxlength="[{$oArticleFile->oxfiles__oxfilename->fldmax_length}]" name="article_files[[{$oArticleFile->getId()}]][oxfiles__oxfilename]" value="[{$oArticleFile->oxfiles__oxfilename->value}]" [{ $readonlyRename }]>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="edittext">[{ oxmultilang ident="ARTICLE_FILES_NEW_PURCHASEDONLY" }]</td>
                                                                <td class="edittext">
                                                                    <input class="edittext" type="hidden" name="article_files[[{$oArticleFile->getId()}]][oxfiles__oxpurchasedonly]" value='0'>
                                                                    <input class="edittext" type="checkbox" name="article_files[[{$oArticleFile->getId()}]][oxfiles__oxpurchasedonly]" value='1' [{if $oArticleFile->oxfiles__oxpurchasedonly->value == 1}]checked[{/if}] [{ $readonly }]>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="edittext">[{ oxmultilang ident="GENERAL_MAX_DOWNLOADS_COUNT" }]</td>
                                                                <td class="edittext">
                                                                    <input type=text class="txt" name="article_files[[{$oArticleFile->getId()}]][oxfiles__oxmaxdownloads]" value="[{$oView->getConfigOptionValue($oArticleFile->oxfiles__oxmaxdownloads->value)}]">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="edittext">[{ oxmultilang ident="GENERAL_LINK_EXPIRATION_TIME_UNREGISTERED" }]</td>
                                                                <td class="edittext">
                                                                    <input type=text class="txt" name="article_files[[{$oArticleFile->getId()}]][oxfiles__oxmaxunregdownloads]"  value="[{$oView->getConfigOptionValue($oArticleFile->oxfiles__oxmaxunregdownloads->value)}]">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="edittext">[{ oxmultilang ident="GENERAL_LINK_EXPIRATION_TIME" }]</td>
                                                                <td class="edittext">
                                                                    <input type=text class="txt" name="article_files[[{$oArticleFile->getId()}]][oxfiles__oxlinkexptime]"  value="[{$oView->getConfigOptionValue($oArticleFile->oxfiles__oxlinkexptime->value)}]">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="edittext">[{ oxmultilang ident="GENERAL_DOWNLOAD_EXPIRATION_TIME" }]</td>
                                                                <td class="edittext">
                                                                    <input type=text class="txt" name="article_files[[{$oArticleFile->getId()}]][oxfiles__oxdownloadexptime]"  value="[{$oView->getConfigOptionValue($oArticleFile->oxfiles__oxdownloadexptime->value)}]">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </dl>
                                                </div>
                                            </div>
                                        [{/block}]
                                    [{/foreach}]
                              [{/if}]
                          <input type="submit" class="saveButton" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'">
                      </form>
                  </td>
              </tr>
          </table>
      </td>
  </tr>

</table>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
