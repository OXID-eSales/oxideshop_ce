[{ assign var="shop"      value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]
[{ assign var="user"      value=$oEmailView->getUser() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
        [{ oxcontent ident="oxnewsletteremail" }]
    </p>

[{include file="email/html/footer.tpl"}]