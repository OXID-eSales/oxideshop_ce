<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title>[{ oxmultilang ident="GENERAL_ADMIN_TITLE" }]</title>
</head>

<!-- frames -->
[{assign var="blLoadDynContents" value=0}]
[{if $oViewConf->blLoadDynContents && $oViewConf->sShopCountry}]
    [{assign var="blLoadDynContents" value=1}]
[{/if}]
<frameset [{if $blLoadDynContents}]rows="*,150"[{else}]rows="*"[{/if}] border="0">
    <frame src="[{$oViewConf->getSelfLink()}]&cl=navigation&item=navigation.tpl" name="adminnav" id="adminnav" frameborder="0" scrolling="auto" noresize marginwidth="0" marginheight="0">

    [{if $blLoadDynContents}]
    <frame src="[{ $oViewConf->getServiceUrl() }]banners/navigation.html"  name="adminfrm" id="adminfrm" frameborder="0" scrolling="auto" noresize marginwidth="0" marginheight="0">
    [{/if}]
</frameset>

</html>
