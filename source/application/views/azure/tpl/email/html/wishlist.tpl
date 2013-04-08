[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="userInfo"      value=$oEmailView->getUser() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
        [{ oxmultilang ident="EMAIL_WISHLIST_HTML_MYWISHLISTBY" }]
    </h3>

    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
          [{$userInfo->send_message|oxescape}]

    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
          [{ oxmultilang ident="EMAIL_WISHLIST_HTML_TOMYWISHLISTCLICKHERE1" }] <a href="[{ $oViewConf->getBaseDir() }]index.php?cl=wishlist&wishid=[{$userInfo->send_id}]"><b>[{ oxmultilang ident="EMAIL_WISHLIST_HTML_TOMYWISHLISTCLICKHERE2" }]</b></a>
    </p>
    
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
          [{ oxmultilang ident="EMAIL_WISHLIST_HTML_WITHLOVE" }]
    </p>
    
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
          [{$userInfo->send_name|oxescape}]
    </p>

[{include file="email/html/footer.tpl"}]