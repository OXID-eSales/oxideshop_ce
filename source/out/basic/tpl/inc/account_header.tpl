  <strong class="boxhead">[{ oxmultilang ident="INC_ACCOUNT_HEADER_MYACCOUNT" }]"[{ $oxcmp_user->oxuser__oxusername->value }]"</strong>
  <div class="box account_header">

    <table width="100%">
      <colgroup>
        <col width="50%">
        <col width="50%">
      </colgroup>
      <tr>
        <td valign="top">
            <dl>
                <dt><a id="test_link_account_password" class="[{if $active_link == 1}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_password" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_PERSONALSETTINGS" }]</a></dt>
                <dd id="test_link_account_passwordDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_CHANGEPWD" }]</dd>
            </dl>

            <dl>
                <dt><a id="test_link_account_newsletter" class="[{if $active_link == 2}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_newsletter" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_NEWSLETTERSETTINGS" }]</a></dt>
                <dd id="test_link_account_newsletterDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_NEWSLETTERSUBSCRIBE" }]</dd>
            </dl>

            <dl>
                <dt><a id="test_link_account_billship" class="[{if $active_link == 3}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_user" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_BILLINGSHIPPINGSET" }]</a></dt>
                <dd id="test_link_account_billshipDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_UPDATEYOURBILLINGSHIPPINGSET" }]</dd>
            </dl>

            <dl>
                <dt><a id="test_link_account_order" class="[{if $active_link == 4}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_ORDERHISTORY" }]</a></dt>
                <dd id="test_link_account_orderDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_ORDERS" }] [{ $oView->getOrderCnt() }]</dd>
            </dl>
            [{if $oView->isEnabledDownloadableFiles()}]
            <dl class="lastInCol">
                <dt><a id="test_link_account_downloads" class="[{if $active_link == 10}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads" }]" rel="nofollow">[{ oxmultilang ident="MY_DOWNLOADS" }]</a></dt>
                <dd id="test_link_account_downloadsDesc">[{ oxmultilang ident="MY_DOWNLOADS_DESC" }]</dd>
            </dl>
            [{/if}]
        </td>
        <td valign="top">
            <dl>
                <dt><a id="test_link_account_noticelist" class="[{if $active_link == 5}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_MYNOTICELIST" }]</a></dt>
                <dd id="test_link_account_noticelistDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_PRODUCT3" }] [{ if $oxcmp_user }][{ $oxcmp_user->getNoticeListArtCnt() }][{else}]0[{/if}]</dd>
            </dl>
            [{if $oViewConf->getShowWishlist()}]
                <dl>
                    <dt><a id="test_link_account_wishlist" class="[{if $active_link == 6}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_MYWISHLIST" }]</a></dt>
                    <dd id="test_link_account_wishlistDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_PRODUCT3" }] [{ if $oxcmp_user }][{ $oxcmp_user->getWishListArtCnt() }][{else}]0[{/if}]</dd>
                </dl>
            [{/if}]
            [{if $oViewConf->getShowCompareList()}]
                <dl>
                    <dt><a id="test_link_account_comparelist" class="[{if $active_link == 7}]active[{/if}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=compare" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_MYPRODUCTCOMPARISON" }]</a></dt>
                    <dd id="test_link_account_comparelistDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_PRODUCT3" }] [{ if $oView->getCompareItemsCnt() }][{ $oView->getCompareItemsCnt() }][{else}]0[{/if}]</dd>
                </dl>
            [{/if}]
            [{if $oViewConf->getShowListmania()}]
                <dl>
                    <dt><a id="test_link_account_recommlist" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_recommlist" }]" class="[{if $active_link == 8}]active[{/if}]">[{ oxmultilang ident="INC_ACCOUNT_HEADER_MYRECOMMLIST" }]</a></dt>
                    <dd id="test_link_account_recommlistDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_LISTS" }] [{ if $oxcmp_user->getRecommListsCount() }][{ $oxcmp_user->getRecommListsCount() }][{else}]0[{/if}]</dd>
                </dl>
            [{/if}]
            <dl class="lastInCol">
                <dt><a id="test_link_account_logout" href="[{ $oViewConf->getLogoutLink() }]" class="[{if $active_link == 9}]active[{/if}]">[{ oxmultilang ident="INC_ACCOUNT_HEADER_LOGOUT" }]</a></dt>
                <dd id="test_link_account_logoutDesc">[{ oxmultilang ident="INC_ACCOUNT_HEADER_LOGOUTFROMSHOP" }]&nbsp;</dd>
            </dl>
        </td>
      </tr>
    </table>

  </div>
