[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="userinfo"  value=$oEmailView->getUser() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

<h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
    [{ oxmultilang ident="SELECTED_SHIPPING_CARRIER" suffix="COLON" }]
</h3>

<table border="0" width="100%"cellspacing="10" cellpadding="0" bgcolor="#FFFFFF">
    <tr>
        <td width="10%" style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                <b>[{ oxmultilang ident="FROM" suffix="COLON" }]</b>
            </p>
        </td>
        <td style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                [{$userinfo->send_name|oxescape}]
            </p>
        </td>
    </tr>
    <tr>
        <td width="10%" style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                <b>[{ oxmultilang ident="EMAIL" suffix="COLON"}]</b>
            </p>
        </td>
        <td style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                [{$userinfo->send_email|oxescape}]
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                [{$userinfo->send_message|oxescape|nl2br}]
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                <a href="[{ $sHomeUrl }]">[{ $shop->oxshops__oxname->value }]</a>
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
                [{ oxmultilang ident="MANY_GREETINGS" }] [{$userinfo->send_name|oxescape}]
            </p>
        </td>
    </tr>
</table>

[{include file="email/html/footer.tpl"}]
