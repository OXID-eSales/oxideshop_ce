<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title>[{ oxmultilang ident="GENERAL_ADMIN_TITLE" }] </title>
</head>

<!-- frames -->
<frameset  rows="65,*" border="0">
    <frame src="[{$oViewConf->getSelfLink()}]&[{ $listurl }][{ if $oxid }]&oxid=[{$oxid}][{/if}]" name="list" id="list" frameborder="0" scrolling="auto" noresize marginwidth="0" marginheight="0">
    <frame src="[{ $editurl }]" name="edit" id="edit" frameborder="0" scrolling="Auto" noresize marginwidth="0" marginheight="0">
</frameset>

</html>