<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title>[{ oxmultilang ident="GENERAL_ADMIN_TITLE" }]</title>
</head>

<!-- frames -->
<frameset  rows="5%,*" border="0">
    <frame src="[{$oViewConf->getSelfLink()}]&[{ $listurl }][{ if $oxid }]&oxid=[{$oxid}][{/if}]" name="list" marginwidth="0" marginheight="0" scrolling="off" frameborder="0">
    <frame src="[{$oViewConf->getSelfLink()}]&[{ $editurl }][{ if $oxid }]&oxid=[{$oxid}][{/if}]" name="edit" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
</frameset>


</html>