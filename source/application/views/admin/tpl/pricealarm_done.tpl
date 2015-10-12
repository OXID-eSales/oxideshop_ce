[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
<script type="text/javascript">
<!--
window.onload = function ()
{
    var oTransfer = parent.edit.document.getElementById("transfer");
    oTransfer.cl.value="pricealarm_mail";
    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();
    window.onload = top.reloadEditFrame();
}
//-->
</script>

<form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
[{include file="_formparams.tpl" cl="pricealarm_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
</form>

<div id="liste">
<center>
<h1>[{$iAllCnt}] [{oxmultilang ident="PRICEALARM_DONE_SENDEMAIL"}]</h1>
<a href="JavaScript:var oSearch = document.getElementById('search');oSearch.submit();"><b>[{oxmultilang ident="PRICEALARM_DONE_GOTOPRICEALARM"}]</b></a>
</center>
</div>

[{include file="pagetabsnippet.tpl" noOXIDCheck="true"}]
</body>
</html>
