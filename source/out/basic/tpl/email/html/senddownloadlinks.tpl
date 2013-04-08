[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="sOrderId"   value=$order->getId() }]
[{ assign var="oOrderFileList"   value=$oEmailView->getOrderFileList($sOrderId) }]

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
  <title>[{$subject}]</title>
  <meta http-equiv="Content-Type" content="text/html; charset=[{$oEmailView->getCharset()}]">
  </head>
  <body bgcolor="#FFFFFF" link="#355222" alink="#355222" vlink="#355222" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;">
    <br>
    [{ oxmultilang ident="EMAIL_SENDDOWNLOADS_GREETING" }], [{ $order->oxorder__oxbillsal->value|oxmultilangsal }] [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }],<br>
        <br>

        <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
            [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_ORDERNOMBER" }] [{ $order->oxorder__oxordernr->value }]
        </h3>

    [{ if $oOrderFileList }]
            <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                [{ oxmultilang ident="EMAIL_SENDDOWNLOADS_DOWNLOADS_DESC" }]
            </h3>
            [{foreach from=$oOrderFileList item="oOrderFile"}]
              <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
              [{if $order->oxorder__oxpaid->value != "0000-00-00 00:00:00" || !$oOrderFile->oxorderfiles__oxpurchasedonly->value}]
                <a href="[{ oxgetseourl ident=$oViewConf->getBaseDir()|cat:"index.php?cl=download" params="sorderfileid="|cat:$oOrderFile->getId() }]" rel="nofollow">[{$oOrderFile->oxorderfiles__oxfilename->value}]</a> [{$oOrderFile->getFileSize()|oxfilesize}]
              [{else}]
                <span>[{$oOrderFile->oxorderfiles__oxfilename->value}]</span>
                <strong>[{ oxmultilang ident="EMAIL_SENDDOWNLOADS_PAYMENT_PENDING" }]</strong>
              [{/if}]
              </p>
            [{/foreach}]
    [{/if}]

        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
            [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_YUORTEAM1" }] [{ $shop->oxshops__oxname->value }] [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_YUORTEAM2" }]
        </p>

        [{if $oViewConf->showTs("ORDERCONFEMAIL") && $oViewConf->getTsId() }]
            [{assign var="sTSRatingImg" value="https://www.trustedshops.com/bewertung/widget/img/bewerten_"|cat:$oViewConf->getActLanguageAbbr()|cat:".gif"}]
            <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_TS_RATINGS_RATEUS" }]
            </h3>

            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                <a href="[{ $oViewConf->getTsRatingUrl() }]" target="_blank" title="[{ oxmultilang ident="TS_RATINGS_URL_TITLE" }]">
                    <img src="[{$sTSRatingImg}]" border="0" alt="[{ oxmultilang ident="TS_RATINGS_BUTTON_ALT" }]" align="middle">
                </a>
            </p>
        [{/if}]
    <br><br>
    [{ oxcontent ident="oxemailfooter" }]
  </body>
</html>
