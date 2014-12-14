[{include file="headitem.tpl" box="list"
    title="PRICEALARM_SEND_TITLE"|oxmultilangassign box="list"
    meta_refresh_sec="2"
    meta_refresh_url=$oViewConf->getSelfLink()|cat:"&cl=pricealarm_send&iStart=`$iStart`&iAllCnt=`$iAllCnt`"
}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
}
//-->
</script>
<body>

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="pricealarm_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
</form>

<div class="liste">
<center>
<h1>[{ oxmultilang ident="PRICEALARM_MESSAGE_SENT" }] [{ $iStart}] [{ oxmultilang ident="PRICEALARM_SEND_FROM" }] [{$iAllCnt}].</h1>
</center>
</div>

[{include file="pagetabsnippet.tpl" noOXIDCheck="true"}]
</body>
</html>
