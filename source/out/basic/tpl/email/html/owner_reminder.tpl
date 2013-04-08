[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>[{ $shop->oxshops__oxordersubject->value }]</title>
<meta http-equiv="Content-Type" content="text/html; charset=[{$oEmailView->getCharset()}]">
</head>
<body bgcolor="#FFFFFF" link="#355222" alink="#355222" vlink="#355222" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;">
<img src="[{$oViewConf->getImageUrl('logo_white.gif', false)}]" border="0" hspace="0" vspace="0" alt="[{ $shop->oxshops__oxname->value }]" align="texttop"><br>
<br>
[{ oxmultilang ident="EMAIL_OWNER_REMINDER_HTML_STOCKLOW" }]
<br>
<br>
<table border="0" cellspacing="0" cellpadding="0" width="600">
<tr>
    <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" height="15" width="100">
    &nbsp;&nbsp;<b>[{ oxmultilang ident="EMAIL_OWNER_REMINDER_HTML_PRODUCT" }]</b>
    </td>
    <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" height="15">
    </td>
    <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; background-color: #494949; color: #FFFFFF;" align="right" width="70">
    <b>[{ oxmultilang ident="EMAIL_OWNER_REMINDER_HTML_QUANTITY" }]</b>
    </td>
</tr>
[{foreach from=$articles item=oProduct}]
<tr>
        <td valign="top" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;">
            <img src="[{$oProduct->getThumbnailUrl(false)}]" border="0" hspace="0" vspace="0" alt="[{ $oProduct->oxarticles__oxtitle->value|strip_tags }]" align="texttop">
        </td>
        <td valign="top" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;">
            <b>[{ $oProduct->oxarticles__oxtitle->value }][{ if $oProduct->oxarticles__oxvarselect->value}], [{ $oProduct->oxarticles__oxvarselect->value}][{/if}]</b>
            [{ if $chosen_selectlist }]
            ,[{foreach from=$chosen_selectlist item=oList}]
                    [{ $oList->name }] [{ $oList->value }]&nbsp;
             [{/foreach}]
            [{/if}]
            <br>[{ oxmultilang ident="EMAIL_OWNER_REMINDER_HTML_ARTNOMBER" }] [{ $oProduct->oxarticles__oxartnum->value }]
        </td>
        <td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; padding-top: 10px;" valign="top" align="right">
            [{$oProduct->oxarticles__oxstock->value}] ([{$oProduct->oxarticles__oxremindamount->value}])
        </td>
</tr>
[{/foreach}]
</table>

<br><br>
[{ oxcontent ident="oxemailfooter" }]

</body>
</html>