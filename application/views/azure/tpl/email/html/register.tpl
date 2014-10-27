[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="user"     value=$oEmailView->getUser() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

    [{ oxcontent ident=$contentident|default:"oxregisteremail" }]

[{include file="email/html/footer.tpl"}]