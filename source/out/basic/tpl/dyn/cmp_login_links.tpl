[{if $oxcmp_user && $oxcmp_user->getNoticeListArtCnt() }]
<dl class="actionslist">
    <dt>[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_NOTICELIST" }]</dt>
    <dd>
        <tt>[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_PRODUCT" }]</tt>
        <span id="test_AccNoticeListAm">[{ if $oxcmp_user }][{ $oxcmp_user->getNoticeListArtCnt() }][{else}]0[{/if}]</span>
        <a id="test_AccNoticeList" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" class="link">[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_DETAILS" }]</a>
    </dd>
</dl>
[{/if}]

[{if $oxcmp_user && $oxcmp_user->getWishListArtCnt() && $oViewConf->getShowWishlist() }]
<dl class="actionslist">
    <dt>[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_WISHLISTE" }]</dt>
    <dd>
        <tt>[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_PRODUCT2" }]</tt>
        <span id="test_AccWishListAm">[{ if $oxcmp_user }][{ $oxcmp_user->getWishListArtCnt() }][{else}]0[{/if}]</span>
        <a id="test_AccWishList" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" class="link">[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_DETAILS2" }]</a>
    </dd>
</dl>
[{/if}]

[{ if $oViewConf->getShowCompareList() && $oView->getCompareItemsCnt() }]
<dl class="actionslist">
    <dt>[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_MYPRODUCTCOMPARISON" }]</dt>
    <dd>
        <tt>[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_PRODUCT3" }]</tt>
        <span id="test_AccComparisonAm">[{ if $oView->getCompareItemsCnt() }][{ $oView->getCompareItemsCnt() }][{else}]0[{/if}]</span>
        <a id="test_AccComparison" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=compare" }]" class="link">[{ oxmultilang ident="INC_CMP_LOGIN_RIGHT_DETAILS3" }]</a>
    </dd>
</dl>
[{/if}]
