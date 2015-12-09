[{assign var="shop"      value=$oEmailView->getShop()}]
[{assign var="oViewConf" value=$oEmailView->getViewConfig()}]
[{assign var="currency"  value=$oEmailView->getCurrency()}]
[{assign var="user"      value=$oEmailView->getUser()}]

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>[{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_PRICEALARMIN"}][{$shop->oxshops__oxname->value}]</title>
<meta http-equiv="Content-Type" content="text/html; charset=[{$oEmailView->getCharset()}]">
</head>
<body bgcolor="#FFFFFF" link="#355222" alink="#355222" vlink="#355222" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;">
<br>
<img src="[{$oViewConf->getImageUrl()}]logo_white.gif" border="0" hspace="0" vspace="0" alt="[{$shop->oxshops__oxname->value}]" align="texttop"><br>
<br>
[{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_HY"}]<br>
<br>
[{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_HAVEPRICEALARM"}] [{$shop->oxshops__oxname->value}]!<br>
<br>
[{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_ITEM1"}] [{$oPriceAlarm->getTitle()}] [{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_ITEM2"}] [{$oPriceAlarm->getFProposedPrice()}] [{$currency->sign}]
[{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_ITEM3"}] [{$oPriceAlarm->getFPrice()}] [{$currency->sign}] [{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_ITEM4"}]<br>
<br>

[{include file="include/email/pricealarm_customer_button.tpl"}]

<br>
<br>
[{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_TEAM1"}] [{$shop->oxshops__oxname->value}] [{oxmultilang ident="EMAIL_PRICEALARM_CUSTOMER_TEAM2"}]<br>

[{include file="include/email/pricealarm_footer.tpl"}]

</body>
</html>

