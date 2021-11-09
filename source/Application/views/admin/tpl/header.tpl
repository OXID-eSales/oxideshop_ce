<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html id="top">
<head>
    [{block name="admin_header_head"}]
        <title>[{oxmultilang ident="NAVIGATION_TITLE"}]</title>
        <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]nav.css">
        <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]colors_[{$oViewConf->getEdition()|lower}].css">
        <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
    [{/block}]
</head>
<body>
    [{include file='include/header_links.tpl'}]
    <div class="version">
        <b>
            [{$oView->getShopFullEdition()}]
            [{$oView->getShopVersion()}]
        </b>
    </div>
</body>
</html>
