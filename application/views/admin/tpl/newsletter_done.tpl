[{include file="headitem.tpl" title="NEWSLETTER_DONE_TITLE"|oxmultilangassign box="list"}]
<script type="text/javascript">
<!--
window.onload = function ()
{
    var oTransfer = parent.edit.document.getElementById("transfer");
    oTransfer.cl.value="newsletter_selection";
    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();
    top.reloadEditFrame();
}
//-->
</script>

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="newsletter_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
</form>

<div id="liste">
[{foreach from=$oView->getMailErrors() item=sError}]
  [{ $sError }]<br>
[{/foreach}]
<center>
<h1>[{ oxmultilang ident="NEWSLETTER_DONE_NEWSSEND" }]</h1>
<a href="JavaScript:var oSearch = document.getElementById('search');oSearch.submit();"><b>[{ oxmultilang ident="NEWSLETTER_DONE_GOTONEWSLETTER" }]</b></a>
</center>
</div>

[{include file="pagetabsnippet.tpl" noOXIDCheck="true"}]
</body>
</html>
