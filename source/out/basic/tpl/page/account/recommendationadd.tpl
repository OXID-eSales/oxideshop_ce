[{assign var="product" value=$oView->getProduct() }]
[{assign var="template_title" value=$product->oxarticles__oxtitle->value|cat:" "|cat:$product->oxarticles__oxvarselect->value}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

[{oxid_include_dynamic file="dyn/add_recomm.tpl" testid=""}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
