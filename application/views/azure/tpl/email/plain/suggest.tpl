[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="user"     value=$oEmailView->getUser() }]

[{ oxmultilang ident="PRODUCT_POST_CARD_FROM" }] [{ $shop->oxshops__oxname->getRawValue() }]

[{ oxmultilang ident="FROM" suffix="COLON" }] [{$userinfo->send_name}]
[{ oxmultilang ident="EMAIL" suffix="COLON" }] [{$userinfo->send_email}]

[{ oxmultilang ident="TO" suffix="COLON" }] [{$userinfo->rec_name}]
[{ oxmultilang ident="EMAIL" suffix="COLON" }] [{$userinfo->rec_email}]

[{$userinfo->send_message}]

[{ oxmultilang ident="MANY_GREETINGS" }] [{$userinfo->send_name}]

[{ oxmultilang ident="RECOMMENDED_PRODUCTS" suffix="COLON" }]

[{ $product->oxarticles__oxtitle->getRawValue()|strip_tags }]
[{ $product->oxarticles__oxshortdesc->getRawValue() }]

[{ oxmultilang ident="CHECK" }] [{ $sArticleUrl }]

[{ oxcontent ident="oxemailfooterplain" }]