[{ assign var="shop"     value=$oEmailView->getShop() }]
[{ assign var="oViewConf" value=$oEmailView->getViewConfig() }]

[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

<h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
    [{ oxmultilang ident="MESSAGE_STOCK_LOW" }]
</h3>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
            <b>[{ oxmultilang ident="PRODUCT" }]</b>
        </td>
        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
            &nbsp;
        </td>
        <td style="padding: 5px; border-bottom: 2px solid #ccc;">
            <b>[{ oxmultilang ident="TOTAL_QUANTITY" }]</b>
        </td>
    </tr>
    [{foreach from=$articles item=oProduct}]
    <tr valign="top">
        <td style="padding: 5px; border-bottom: 1px solid #ddd;">
            <img src="[{$oProduct->getThumbnailUrl(false)}]" border="0" hspace="0" vspace="0" alt="[{ $oProduct->oxarticles__oxtitle->value|strip_tags }]" align="texttop">
        </td>
        <td style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                <b>[{ $oProduct->oxarticles__oxtitle->value }][{ if $oProduct->oxarticles__oxvarselect->value}], [{ $oProduct->oxarticles__oxvarselect->value}][{/if}]</b>
                [{ if $chosen_selectlist }]
                    ,
                    [{foreach from=$chosen_selectlist item=oList}]
                        [{ $oList->name }] [{ $oList->value }]&nbsp;
                    [{/foreach}]
                [{/if}]
                <br>
                [{ oxmultilang ident="PRODUCT_NO" suffix="COLON" }] [{ $oProduct->oxarticles__oxartnum->value }]
            </p>
        </td>
        <td style="padding: 5px; border-bottom: 1px solid #ddd;">
            <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                [{$oProduct->oxarticles__oxstock->value}] ([{$oProduct->oxarticles__oxremindamount->value}])
            </p>
        </td>
    </tr>
    [{/foreach}]
</table>

[{include file="email/html/footer.tpl"}]