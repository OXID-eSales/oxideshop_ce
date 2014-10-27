[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

    [{block name="email_html_ordershipped_sendemail"}]
       [{ oxcontent ident="oxordersendemail" }]
    [{/block}]

    [{block name="email_html_ordershipped_infoheader"}]
        <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
            [{ oxmultilang ident="ORDER_SHIPPED_TO" suffix="COLON" }]
        </h3>
    [{/block}]

    [{block name="email_html_ordershipped_address"}]
        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
            [{if $order->oxorder__oxdellname->value }]
              [{ $order->oxorder__oxdelcompany->value }]<br>
              [{ $order->oxorder__oxdelfname->value }] [{ $order->oxorder__oxdellname->value }]<br>
              [{ $order->oxorder__oxdelstreet->value }] [{ $order->oxorder__oxdelstreetnr->value }]<br>
              [{ $order->oxorder__oxdelstateid->value }]
              [{ $order->oxorder__oxdelzip->value }] [{ $order->oxorder__oxdelcity->value }]
            [{else}]
              [{ $order->oxorder__oxbillcompany->value }]<br>
              [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }]<br>
              [{ $order->oxorder__oxbillstreet->value }] [{ $order->oxorder__oxbillstreetnr->value }]<br>
              [{ $order->oxorder__oxbillstateid->value }]
              [{ $order->oxorder__oxbillzip->value }] [{ $order->oxorder__oxbillcity->value }]
            [{/if}]
        </p>
    [{/block}]

    [{block name="email_html_ordershipped_oxordernr"}]
        <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
            [{ oxmultilang ident="ORDER_NUMBER" suffix="COLON" }] [{ $order->oxorder__oxordernr->value }]
        </h3>
    [{/block}]

    <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
      <td style="padding: 5px; border-bottom: 4px solid #ddd;" width="70" align="right">
          <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
              <b>[{ oxmultilang ident="QUANTITY" }]</b>
          </p>
      </td>
      <td style="padding: 5px; border-bottom: 4px solid #ddd;">
          <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
              <b>[{ oxmultilang ident="PRODUCT" }]</b>
          </p>
      </td>
      [{if $blShowReviewLink}]
      <td style="padding: 5px; border-bottom: 4px solid #ddd;" width="150" align="right">
          <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
              [{ oxmultilang ident="PRODUCT_RATING" }]
          </p>
      </td>
      [{/if}]
    </tr>
    [{block name="email_html_ordershipped_orderarticles"}]
        [{foreach from=$order->getOrderArticles(true) item=oOrderArticle}]
          <tr valign="top">
            <td style="padding: 5px; border-bottom: 1px solid #ddd;" align="right">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{ $oOrderArticle->oxorderarticles__oxamount->value }]
                </p>
            </td>
            <td style="padding: 5px; border-bottom: 1px solid #ddd;">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{ $oOrderArticle->oxorderarticles__oxtitle->value }] [{ $oOrderArticle->oxorderarticles__oxselvariant->value }]
                    <br>[{ oxmultilang ident="PRODUCT_NO" suffix="COLON" }] [{ $oOrderArticle->oxorderarticles__oxartnum->value }]
                </p>
            </td>
            [{if $blShowReviewLink}]
            <td style="padding: 5px; border-bottom: 1px solid #ddd;" align="right">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    <a href="[{ $oViewConf->getBaseDir() }]index.php?shp=[{$shop->oxshops__oxid->value}]&amp;anid=[{ $oOrderArticle->oxorderarticles__oxartid->value }]&amp;cl=review&amp;reviewuserhash=[{$reviewuserhash}]" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" target="_blank">[{ oxmultilang ident="REVIEW" }]</a>
                </p>
            </td>
            [{/if}]
          </tr>
        [{/foreach}]
    [{/block}]
    </table>

    [{block name="email_html_ordershipped_infofooter"}]
        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
            [{ oxmultilang ident="YOUR_TEAM" args=$shop->oxshops__oxname->value}]
        </p>
    [{/block}]

    [{block name="email_html_ordershipped_shipmenttrackingurl"}]
        [{if $order->getShipmentTrackingUrl()}]
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                [{ oxmultilang ident="SHIPMENT_TRACKING" suffix="COLON" }] <a href="[{ $order->getShipmentTrackingUrl()}]" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;" target="_blank" title="[{ oxmultilang ident="CLICK_HERE" }]">[{ oxmultilang ident="CLICK_HERE" }]</a>
            </p>
        [{/if}]
    [{/block}]

    [{block name="email_html_ordershipped_ts"}]
        [{if $oViewConf->showTs("ORDERCONFEMAIL") && $oViewConf->getTsId() }]
            [{assign var="sTSRatingImg" value="https://www.trustedshops.com/bewertung/widget/img/bewerten_"|cat:$oViewConf->getActLanguageAbbr()|cat:".gif"}]
            <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                [{ oxmultilang ident="RATE_OUR_SHOP" }]
            </h3>

            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                <a href="[{ $oViewConf->getTsRatingUrl() }]" target="_blank" title="[{ oxmultilang ident="TRUSTED_SHOPS_RATINGS" }]">
                    <img src="[{$sTSRatingImg}]" border="0" alt="[{ oxmultilang ident="WRITE_REVIEW_2" }]" align="middle">
                </a>
            </p>
        [{/if}]
    [{/block}]

[{include file="email/html/footer.tpl"}]
