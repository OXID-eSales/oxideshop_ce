[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="user"      value=$oEmailView->getUser() }]


[{ oxmultilang ident="EMAIL_WISHLIST_HTML_MYWISHLISTBY" }] [{ $shop->oxshops__oxname->getRawValue() }]

[{$userinfo->send_message}]

[{ oxmultilang ident="EMAIL_WISHLIST_HTML_TOMYWISHLISTCLICKHERE1" }] [{ oxmultilang ident="EMAIL_WISHLIST_HTML_TOMYWISHLISTCLICKHERE2" }]

[{ $oViewConf->getBaseDir() }]index.php?cl=wishlist&wishid=[{$userinfo->send_id}]

[{ oxmultilang ident="EMAIL_WISHLIST_HTML_WITHLOVE" }]

[{$userinfo->send_name}]

[{ oxcontent ident="oxemailfooterplain" }]