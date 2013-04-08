<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>[{ oxmultilang ident="GENERAL_ADMIN_TITLE" }]</title>
    <link rel="shortcut icon" href="[{$oViewConf->getImageUrl()}]favicon.ico">
</head>
<script type="text/javascript">
<!--//
sInitValue = "[OXID Administrator]";
sShopTitle = "";
sMenuItem  = "";
sMenuSubItem  = "";
sWorkArea  = "";
//
function setTitle()
{
    if (sShopTitle.length)
        document.title = "[" + sShopTitle + "]";
    else
        document.title = sInitValue;

    if (sMenuItem.length)
        document.title += " - " + sMenuItem;

    if (sMenuSubItem.length)
        document.title += " - " + sMenuSubItem;

    if (sWorkArea.length)
        document.title += " - " + sWorkArea;
}

function forceReloadingEditFrame()
{
    //forcing edit frame to reload after submit
    top.basefrm.edit.document.reloadFrame = true;
}

function forceReloadingListFrame( oxId )
{
    //forcing list frame to reload after submit
    top.basefrm.list.document.reloadFrame = true;
}

function reloadEditFrame()
{
    if (top.basefrm.edit) {
      if (top.basefrm.edit.document.reloadFrame) {
          var oTransfer = top.basefrm.edit.document.getElementById("transfer");
          oTransfer.submit();
      }
    }
}

function reloadListFrame()
{
  if (top.basefrm.list) {
      if (top.basefrm.list.document.reloadFrame) {
          top.oxid.admin.updateList();
      }
  }
}

function reloadListEditFrames()
{
  reloadListFrame();
  reloadEditFrame();
}

function loadEditFrame(sUrl)
{
    top.basefrm.edit.document.location = sUrl;
}
//-->
</script>
<script type="text/javascript" src="[{$oViewConf->getResourceUrl()}]oxid.js"></script>
</html>

<frameset rows="54,*" border="0">
    <frame src="[{$oViewConf->getSelfLink()}]&cl=navigation&item=header.tpl" name="header" id="header" frameborder="0" scrolling="No" noresize marginwidth="0" marginheight="0">
    <frameset  cols="200,*" border="0">
        <frame src="[{$oViewConf->getSelfLink()}]&cl=navigation" name="navigation" id="navigation" frameborder="0" scrolling="Auto" noresize marginwidth="0" marginheight="0">
        <frame src="[{$oViewConf->getSelfLink()}]&cl=navigation&item=home.tpl" name="basefrm" id="basefrm" frameborder="0" scrolling="Auto" noresize marginwidth="0" marginheight="0">
    </frameset>
</frameset>
