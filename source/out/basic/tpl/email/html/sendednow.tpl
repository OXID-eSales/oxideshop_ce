[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
  <title>[{$subject}]</title>
  <meta http-equiv="Content-Type" content="text/html; charset=[{$oEmailView->getCharset()}]">
  </head>
  <body bgcolor="#FFFFFF" link="#355222" alink="#355222" vlink="#355222" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;">
    <br>
    [{ oxcontent ident="oxordersendemail" }]
    [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_ORDERSHIPPEDTO" }]<br><br>
    [{ if $order->oxorder__oxdellname->value }]
      [{ $order->oxorder__oxdelcompany->value }]<br>
      [{ $order->oxorder__oxdelfname->value }] [{ $order->oxorder__oxdellname->value }]<br>
      [{ $order->oxorder__oxdelstreet->value }] [{ $order->oxorder__oxdelstreetnr->value }]<br>
      [{ $order->oxorder__oxdelstateid->value }]
      [{ $order->oxorder__oxdelzip->value }] [{ $order->oxorder__oxdelcity->value }]<br>
    [{else}]
      [{ $order->oxorder__oxbillcompany->value }]<br>
      [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }]<br>
      [{ $order->oxorder__oxbillstreet->value }] [{ $order->oxorder__oxbillstreetnr->value }]<br>
      [{ $order->oxorder__oxbillstateid->value }]
      [{ $order->oxorder__oxbillzip->value }] [{ $order->oxorder__oxbillcity->value }]<br>
    [{/if}]
    <br>
    <b>[{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_ORDERNOMBER" }] [{ $order->oxorder__oxordernr->value }]</b><br><br>
    <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="70">
        <b>[{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_QUANTITY" }]</b>
      </td>
      <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" height="15" width="100">
        &nbsp;&nbsp;<b>[{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_PRODUCT" }]</b>
      </td>
      [{if $blShowReviewLink}]
      <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="150">
        [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_PRODUCTRATING" }]
      </td>
      [{/if}]
    </tr>
    [{foreach from=$order->getOrderArticles(true) item=oOrderArticle}]
      <tr>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
          [{ $oOrderArticle->oxorderarticles__oxamount->value }]
        </td>
        <td valign="top" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;">
          &nbsp;&nbsp;[{ $oOrderArticle->oxorderarticles__oxtitle->value }] [{ $oOrderArticle->oxorderarticles__oxselvariant->value }]
          <br>&nbsp;&nbsp;[{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_ARTNOMBER" }] [{ $oOrderArticle->oxorderarticles__oxartnum->value }]
        </td>
        [{if $blShowReviewLink}]
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
          <a href="[{ $oViewConf->getBaseDir() }]index.php?shp=[{$shop->oxshops__oxid->value}]&amp;anid=[{ $oOrderArticle->oxorderarticles__oxartid->value }]&amp;cl=review&amp;reviewuserhash=[{$reviewuserhash}]" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" target="_blank">[{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_REVIEW" }]</a>
        </td>
        [{/if}]
      </tr>
    [{/foreach}]
    </table>

    <br><br>
    [{if $order->getShipmentTrackingUrl()}]
        [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_SHIPMENTTRACKING" }]
        <a href="[{$order->getShipmentTrackingUrl()}]" target="_blank" title="[{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_SHIPMENTTRACKING" }]">[{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_SHIPMENTTRACKINGURL" }]</a>
        <br><br>
    [{/if}]
    [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_YUORTEAM1" }] [{ $shop->oxshops__oxname->value }] [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_YUORTEAM2" }]<br>
    [{if $oViewConf->showTs("ORDERCONFEMAIL") && $oViewConf->getTsId() }]
    <br><br>
    [{assign var="sTSRatingImg" value="https://www.trustedshops.com/bewertung/widget/img/bewerten_"|cat:$oViewConf->getActLanguageAbbr()|cat:".gif"}]
    [{ oxmultilang ident="EMAIL_SENDEDNOW_HTML_TS_RATINGS_RATEUS" }]<br><br>
    <a href="[{ $oViewConf->getTsRatingUrl() }]" target="_blank" title="[{ oxmultilang ident="TS_RATINGS_URL_TITLE" }]">
      <img src="[{$sTSRatingImg}]" border="0" alt="[{ oxmultilang ident="TS_RATINGS_BUTTON_ALT" }]" align="middle">
    </a>
    [{/if}]
    <br><br>
    [{ oxcontent ident="oxemailfooter" }]
  </body>
</html>
