[{assign var=trustedShopId value=$oViewConf->getTsId()}]
[{oxscript include='js/widgets/oxtsbadge.js'}]
[{oxscript add="$( 'body' ).oxTsBadge({trustedShopId:'`$trustedShopId`'});"}]
<noscript>
    <a href="https://www.trustedshops.co.uk/shop/certificate.php?shop_id=[{$trustedShopId}]">
        <img title="Trusted Shops Seal of Approval - click to verify." src="//widgets.trustedshops.com/images/badge.png" style="position:fixed;bottom:100;right:100;" />
    </a>
</noscript>
