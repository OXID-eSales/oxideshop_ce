<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title>[{ oxmultilang ident="GENERAL_ADMIN_TITLE" }]</title>
</head>

<!-- frames -->
<frameset  rows="40%,*" border="0" onload="document.getElementById('edit').src='[{$oViewConf->getSelfLink()}]&[{ $editurl }]&oxid=[{$currentadminshop}]';">
    <frame src="[{$oViewConf->getSelfLink()}]&[{ $listurl }]&oxid=[{$currentadminshop}]" name="list" id="list" frameborder="0" scrolling="auto" noresize marginwidth="0" marginheight="0">
    <frame src="" name="edit" id="edit" frameborder="0" scrolling="auto" noresize marginwidth="0" marginheight="0">
</frameset>


</html>