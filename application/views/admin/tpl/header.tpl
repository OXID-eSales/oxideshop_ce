<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html id="top">
<head>
    <title>[{ oxmultilang ident="NAVIGATION_TITLE" }]</title>
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]nav.css">
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]colors.css">
    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
</head>
<body>
    [{assign var="oConfig" value=$oViewConf->getConfig()}]
    <ul>
      <li class="act">
          <a href="[{$oViewConf->getSelfLink()}]&cl=navigation&amp;item=home.tpl" id="homelink" target="basefrm" class="rc"><b>[{ oxmultilang ident="NAVIGATION_HOME" }]</b></a>
      </li>
      <li class="sep">
          <a href="[{$oConfig->getShopURL()}]" id="shopfrontlink" target="_blank" class="rc"><b>[{ oxmultilang ident="NAVIGATION_SHOPFRONT" }]</b></a>
      </li>
      <li class="sep">
          <a href="[{$oViewConf->getSelfLink()}]&cl=navigation&amp;fnc=logout" id="logoutlink" target="_parent" class="rc"><b>[{ oxmultilang ident="NAVIGATION_LOGOUT" }]</b></a>
      </li>
    </ul>

    <div class="version">
        <b>
            [{$oView->getShopFullEdition()}]
            [{$oView->getShopVersion()}]
        </b>
    </div>

</body>
</html>