[{include file="headitem.tpl" box="list"
    title="NEWSLETTER_SEND_TITLE"|oxmultilangassign box="list"
    meta_refresh_sec="2"
    meta_refresh_url=$oViewConf->getSelfLink()|cat:"&cl=newsletter_send&iStart=`$iStart`&actedit=`$actedit`&oxid="|cat:$oView->getEditObjectId()
}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oView->getEditObjectId()}]');
    [{ /if}]
}
//-->
</script>
    <body>

        <form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{include file="_formparams.tpl" cl="pricealarm_list" lstrt=$lstrt actedit=$actedit oxid=$oView->getEditObjectId() fnc="" language=$actlang editlanguage=$actlang}]
        </form>

        <div class="liste">
            [{foreach from=$oView->getMailErrors() item=sError}]
                [{ $sError }]<br>
            [{/foreach}]
            <center>
                <h1>[{ oxmultilang ident="NEWSLETTER_SEND_SEND1" }] : [{$iSend}] [{ oxmultilang ident="NEWSLETTER_SEND_SEND2" }] [{$oView->getUserCount()}].</h1>
            </center>
        </div>

        [{include file="pagetabsnippet.tpl" noOXIDCheck="true"}]
    </body>
</html>

