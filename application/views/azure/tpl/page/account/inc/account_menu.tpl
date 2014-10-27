[{oxscript include="js/widgets/oxequalizer.js" priority=10 }]
[{oxscript add="$(function(){oxEqualizer.equalHeight($('.sidebarMenu'), $('#content'));});"}]
<ul class="tree sidebarMenu corners">
    [{block name="account_menu"}]
        <li [{if $active_link == "password"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_password" }]" rel="nofollow">[{ oxmultilang ident="CHANGE_PASSWORD" }]</a></li>
        <li [{if $active_link == "newsletter"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_newsletter" }]" rel="nofollow">[{ oxmultilang ident="NEWSLETTER_SETTINGS" }]</a></li>
        <li [{if $active_link == "billship"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_user" }]" rel="nofollow">[{ oxmultilang ident="BILLING_SHIPPING_SETTINGS" }]</a></li>
        <li [{if $active_link == "orderhistory"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" }]" rel="nofollow">[{ oxmultilang ident="ORDER_HISTORY" }]</a></li>
        [{if $oViewConf->getShowCompareList() }]
            <li [{if $active_link == "compare"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=compare" }]" rel="nofollow">[{ oxmultilang ident="MY_PRODUCT_COMPARISON" }]</a></li>
        [{/if}]
        <li [{if $active_link == "noticelist"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" rel="nofollow">[{ oxmultilang ident="MY_WISH_LIST" }]</a></li>
        [{if $oViewConf->getShowWishlist()}]
            <li [{if $active_link == "wishlist"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" rel="nofollow">[{ oxmultilang ident="MY_GIFT_REGISTRY" }]</a></li>
        [{/if}]
        [{if $oViewConf->getShowListmania()}]
            <li [{if $active_link == "recommendationlist"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_recommlist" }]" rel="nofollow">[{ oxmultilang ident="MY_LISTMANIA" }]</a></li>
        [{/if}]
        [{if $oView->isEnabledDownloadableFiles()}]
        <li [{if $active_link == "downloads"}]class="active"[{/if}]><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads" }]" rel="nofollow">[{ oxmultilang ident="MY_DOWNLOADS" }]</a></li>
        [{/if}]
    [{/block}]
</ul>