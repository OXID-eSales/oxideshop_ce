[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="user"     value=$oEmailView->getUser() }]

[{ oxmultilang ident="EMAIL_SUGGEST_HTML_PRODUCTPOSTCARDFROM" }] [{ $shop->oxshops__oxname->getRawValue() }]

[{ oxmultilang ident="EMAIL_SUGGEST_HTML_FROM" }] [{$userinfo->send_name}]
[{ oxmultilang ident="EMAIL_SUGGEST_HTML_EMAIL" }] [{$userinfo->send_email}]

[{ oxmultilang ident="EMAIL_SUGGEST_HTML_TO" }] [{$userinfo->rec_name}]
[{ oxmultilang ident="EMAIL_SUGGEST_HTML_EMAIL2" }] [{$userinfo->rec_email}]

[{$userinfo->send_message}]

[{ oxmultilang ident="EMAIL_SUGGEST_HTML_MENYGREETINGS" }] [{$userinfo->send_name}]

[{ oxmultilang ident="EMAIL_SUGGEST_PLAIN_RECOMMENDED" }]

[{ $product->oxarticles__oxtitle->getRawValue()|strip_tags }]
[{ $product->oxarticles__oxshortdesc->getRawValue() }]

[{ oxmultilang ident="EMAIL_SUGGEST_PLAIN_CHECK" }] [{ $sArticleUrl }]

[{ oxcontent ident="oxemailfooterplain" }]