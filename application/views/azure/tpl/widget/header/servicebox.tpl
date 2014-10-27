<div class="topPopList">
    [{if $oxcmp_user}]
        [{assign var="noticeListCount" value=$oxcmp_user->getNoticeListArtCnt()}]
        [{assign var="wishListCount" value=$oxcmp_user->getWishListArtCnt()}]
        [{assign var="recommListCount" value=$oxcmp_user->getRecommListsCount()}]
    [{else}]
        [{assign var="noticeListCount" value="0"}]
        [{assign var="wishListCount" value="0"}]
        [{assign var="recommListCount" value="0"}]
    [{/if}]
    [{math equation="a+b+c+d" a=$oView->getCompareItemsCnt() b=$noticeListCount c=$wishListCount d=$recommListCount assign=notificationsCounter}]
    [{oxscript include="js/widgets/oxflyoutbox.js" priority=10 }]
    [{oxscript add="$( '#servicesTrigger' ).oxFlyOutBox();"}]
    [{block name="widget_header_servicebox_flyoutbox"}]
        <p id="servicesTrigger" class="selectedValue [{if $notificationsCounter > 0}]hasNotifications[{/if}]">
            <a href="#" rel="nofollow"><span>[{oxmultilang ident="ACCOUNT"}]</span></a>[{if $notificationsCounter > 0}]<span class="counter FXgradOrange">[{$notificationsCounter}]</span>[{/if}]
        </p>
        <div class="flyoutBox">
            <ul id="services" class="corners flyoutMenu">
                [{block name="widget_header_servicebox_items"}]
                    <li class="active"><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" }]"><span>[{oxmultilang ident="ACCOUNT"}]</span></a></li>
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account" }]" rel="nofollow"><span>[{oxmultilang ident="MY_ACCOUNT"}]</span></a></li>
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" }]" rel="nofollow"><span>[{oxmultilang ident="MY_ORDER_HISTORY"}]</span></a></li>
                    [{if $oViewConf->getShowCompareList() }]
                        <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=compare" }]" rel="nofollow"><span>[{oxmultilang ident="MY_PRODUCT_COMPARISON"}]</span>[{ if $oView->getCompareItemsCnt() }]<span class="counter FXgradOrange">[{ $oView->getCompareItemsCnt() }]</span>[{/if}]</a></li>
                        [{if $oView->getCompareItemsCnt()}]
                            [{oxscript include='js/widgets/oxcomparelinks.js'}]
                            [{oxscript add="$(function(){oxCompareLinks.updateLinks("|cat:$oView->getCompareItems(true)|cat:");});"}]
                        [{/if}]
                    [{/if}]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" rel="nofollow"><span>[{oxmultilang ident="MY_WISH_LIST"}]</span>[{ if $oxcmp_user && $oxcmp_user->getNoticeListArtCnt()}]<span class="counter FXgradOrange">[{ $oxcmp_user->getNoticeListArtCnt() }]</span>[{/if}]</a></li>
                    [{if $oViewConf->getShowWishlist()}]
                        <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" rel="nofollow"><span>[{oxmultilang ident="MY_GIFT_REGISTRY"}]</span>[{ if $oxcmp_user && $oxcmp_user->getWishListArtCnt()}]<span class="counter FXgradOrange">[{ $oxcmp_user->getWishListArtCnt() }]</span>[{/if}]</a></li>
                    [{/if}]
                    [{if $oViewConf->getShowListmania()}]
                        <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_recommlist" }]" rel="nofollow"><span>[{oxmultilang ident="MY_LISTMANIA"}]</span>[{ if $oxcmp_user && $oxcmp_user->getRecommListsCount()}]<span class="counter FXgradOrange">[{ $oxcmp_user->getRecommListsCount() }]</span>[{/if}]</a></li>
                    [{/if}]
                    [{if $oViewConf->isFunctionalityEnabled( "blEnableDownloads" )}]
                        <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads" }]" rel="nofollow"><span>[{oxmultilang ident="MY_DOWNLOADS"}]</span></a></li>
                    [{/if}]
                [{/block}]
            </ul>
        </div>
    [{/block}]
 </div>