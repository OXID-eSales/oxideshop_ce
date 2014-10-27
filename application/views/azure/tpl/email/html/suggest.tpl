[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="userInfo"      value=$oEmailView->getUser() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
        [{ oxmultilang ident="POST_CARD_FROM" }]
    </h3>

    <table border="0" width="100%"cellspacing="10" cellpadding="0">
        <tr>
            <td width="10%" style="padding: 5px; border-bottom: 1px solid #ddd;">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    <b>[{ oxmultilang ident="FROM" suffix="COLON" }]</b>
                </p>
            </td>
            <td>
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{$userInfo->send_name|oxescape}]<br>
                </p>
            </td>
        </tr>
        <tr>
            <td width="10%" style="padding: 5px; border-bottom: 1px solid #ddd;">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    <b>[{ oxmultilang ident="EMAIL" suffix="COLON" }]</b>
                </p>
            </td>
            <td>
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{$userInfo->send_email|oxescape}]
                </p>
            </td>
        </tr>
        <tr>
            <td width="10%" style="padding: 5px; border-bottom: 1px solid #ddd;">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    <b>[{ oxmultilang ident="TO" suffix="COLON" }]</b>
                </p>
            </td>
            <td>
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{$userInfo->rec_name|oxescape}]
                </p>
            </td>
        </tr>
        <tr>
            <td width="10%" style="padding: 5px; border-bottom: 1px solid #ddd;">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    <b>[{ oxmultilang ident="EMAIL" suffix="COLON" }]</b>
                </p>
            </td>
            <td>
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{$userInfo->rec_email|oxescape}]
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 5px; border-bottom: 1px solid #ddd;">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{$userInfo->send_message|oxescape|nl2br}]
                </p>
            </td>
        </tr>
    </table>

    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
        [{ oxmultilang ident="MANY_GREETINGS" }] [{$userInfo->send_name|oxescape}]
    </p>

    <br>

    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
        <a href="[{ $sArticleUrl }]"><img src="[{$product->getPictureUrl()}]" border="0" hspace="0" vspace="0" alt="[{ $product->oxarticles__oxtitle->value|strip_tags }]"></a>
    </p>

    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
        <a href="[{ $sArticleUrl }]"><b>[{ $product->oxarticles__oxtitle->value }]</b></a>
    </p>

    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
        [{ $product->oxarticles__oxshortdesc->value }]
    </p>

[{include file="email/html/footer.tpl"}]