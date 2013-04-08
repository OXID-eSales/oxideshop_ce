[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="sOrderId"   value=$order->getId() }]
[{ assign var="oOrderFileList"   value=$oEmailView->getOrderFileList($sOrderId) }]


[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]
    [{block name="email_html_senddownloadlinks_infoheader"}]
        [{ oxmultilang ident="EMAIL_SENDDOWNLOADS_GREETING" }], [{ $order->oxorder__oxbillsal->value|oxmultilangsal }] [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }],<br>
        <br>
    [{/block}]

    [{block name="email_html_senddownloadlinks_oxordernr"}]
        <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
            [{ oxmultilang ident="ORDER_NUMBER" suffix="COLON" }] [{ $order->oxorder__oxordernr->value }]
        </h3>
    [{/block}]

    [{ if $oOrderFileList and $oOrderFileList|count }]
        [{block name="email_html_senddownloadlinks_download_header"}]
            <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
                [{ oxmultilang ident="MY_DOWNLOADS_DESC" }]
            </h3>
        [{/block}]
        [{block name="email_html_senddownloadlinks_download_link"}]
            [{foreach from=$oOrderFileList item="oOrderFile"}]
              <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
              [{if $order->oxorder__oxpaid->value != "0000-00-00 00:00:00" || !$oOrderFile->oxorderfiles__oxpurchasedonly->value}]
                <a href="[{ oxgetseourl ident=$oViewConf->getBaseDir()|cat:"index.php?cl=download" params="sorderfileid="|cat:$oOrderFile->getId() }]" rel="nofollow">[{$oOrderFile->oxorderfiles__oxfilename->value}]</a> [{$oOrderFile->getFileSize()|oxfilesize}]
              [{else}]
                <span>[{$oOrderFile->oxorderfiles__oxfilename->value}]</span>
                <strong>[{ oxmultilang ident="DOWNLOADS_PAYMENT_PENDING" }]</strong>
              [{/if}]
              </p>
            [{/foreach}]
        [{/block}]
    [{/if}]

    [{block name="email_html_senddownloadlinks_infofooter"}]
        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
            [{ oxmultilang ident="YOUR_TEAM" args=$shop->oxshops__oxname->value }]
        </p>
    [{/block}]

    [{block name="email_html_senddownloadlinks_ts"}]
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
