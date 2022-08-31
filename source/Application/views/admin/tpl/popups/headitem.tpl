<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>[{$title}]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />

    <link rel="stylesheet" type="text/css" href="[{$oViewConf->getResourceUrl() nofilter}]yui/build/reset-fonts/reset-fonts.css">
    <link rel="stylesheet" type="text/css" href="[{$oViewConf->getResourceUrl() nofilter}]yui/build/base/base-min.css">
    <link rel="stylesheet" type="text/css" href="[{$oViewConf->getResourceUrl() nofilter}]yui/build/assets/skins/sam/skin.css">

    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/build/utilities/utilities.js"></script>
    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/build/container/container-min.js"></script>
    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/build/menu/menu-min.js"></script>
    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/build/button/button-min.js"></script>
    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/build/datasource/datasource-min.js"></script>
    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/build/datatable/datatable-min.js"></script>
    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/build/json/json-min.js"></script>

    <link rel="stylesheet" type="text/css" href="[{$oViewConf->getResourceUrl() nofilter}]aoc.css">
    <!--[if IE 8]><link rel="stylesheet" type="text/css" href="[{$oViewConf->getResourceUrl() nofilter}]aoc_ie8.css"><![endif]-->
    <script type="text/javascript" src="[{$oViewConf->getResourceUrl() nofilter}]yui/oxid-aoc.js"></script>

    [{if $readonly}]
        [{assign var="readonly" value="readonly disabled"}]
    [{else}]
        [{assign var="readonly" value=""}]
    [{/if}]

</head>
<body class="yui-skin-sam">
[{include file="inc_error.tpl" Errorlist=$Errors.default}]
