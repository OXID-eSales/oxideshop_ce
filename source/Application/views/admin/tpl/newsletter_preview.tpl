<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title></title>
  <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
  <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]preview.css">
</head>
<body>

<div id="box">

    <form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="oxid" value="[{$oxid}]">
        <input type="hidden" name="cl" value="newsletter_preview">
        <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
    </form>

    <b>[{oxmultilang ident="NEWSLETTER_PREVIEW_PLAINTEXT"}]</b>:<br>
    <hr>
    <pre>[{$previewtext}]</pre>

    <b>[{oxmultilang ident="NEWSLETTER_PREVIEW_HTML"}]</b>:<br>
    <hr>
    [{$previewhtml}]

</div>
</body>
</html>
