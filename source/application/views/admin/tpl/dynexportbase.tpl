<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title>[{ oxmultilang ident="GENERAL_ADMIN_TITLE" }]</title>
</head>

<!-- frames -->
<frameset  rows="62,*" border="0">
    <frame name="dynexport_do" src="[{$oViewConf->getSelfLink()}]&cl=[{$sClassDo}]" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
    <frame name="dynexport_main" src="[{$oViewConf->getSelfLink()}]&cl=[{$editclass|default:$sClassMain}]" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
</frameset>


</html>