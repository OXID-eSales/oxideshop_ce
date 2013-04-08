[{assign var="template_title" value="PAGE_ACCOUNT_DASHBOARD_MYACCOUNT"|oxmultilangassign }]
[{capture append="oxidBlock_content"}]
    <div class="accountDashboardView">
        <h1 id="accountMain" class="pageHead">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_MYACCOUNT" }]"[{ $oxcmp_user->oxuser__oxusername->value }]"</h1>
        <div class="col">
            [{block name="account_dashboard_col1"}]
                <dl>
                    <dt><a id="linkAccountPassword" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_password" }]" rel="nofollow">[{ oxmultilang ident="CHANGE_PASSWORD_2" }]</a></dt>
                </dl>
                <dl>
                    <dt><a id="linkAccountNewsletter" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_newsletter" }]" rel="nofollow">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_NEWSLETTERSETTINGS" }]</a></dt>
                    <dd>[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_NEWSLETTERSUBSCRIBE" }]</dd>
                </dl>
                <dl>
                    <dt><a id="linkAccountBillship" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=account_user" }]" rel="nofollow">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_BILLINGSHIPPINGSET" }]</a></dt>
                    <dd>[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_UPDATEYOURBILLINGSHIPPINGSET" }]</dd>
                </dl>
                <dl>
                    <dt><a id="linkAccountOrder" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" }]" rel="nofollow">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_ORDERHISTORY" }]</a></dt>
                    <dd>[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_ORDERS" }] [{ $oView->getOrderCnt() }]</dd>
                </dl>
                [{if $oView->isEnabledDownloadableFiles()}]
                  <dl>
                      <dt><a id="linkAccountDownloads" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads" }]" rel="nofollow">[{ oxmultilang ident="MY_DOWNLOADS" }]</a></dt>
                      <dd>[{ oxmultilang ident="MY_DOWNLOADS_DESC" }]</dd>
                  </dl>
                [{/if}]
            [{/block}]
        </div>
        <div class="col">
            [{block name="account_dashboard_col2"}]
                <dl>
                    <dt><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" rel="nofollow">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_MYNOTICELIST" }]</a></dt>
                    <dd>[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_PRODUCT3" }] [{ if $oxcmp_user }][{ $oxcmp_user->getNoticeListArtCnt() }][{else}]0[{/if}]</dd>
                </dl>
                [{if $oViewConf->getShowWishlist()}]
                    <dl>
                        <dt><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" rel="nofollow">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_MYWISHLIST" }]</a></dt>
                        <dd>[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_PRODUCT3" }] [{ if $oxcmp_user }][{ $oxcmp_user->getWishListArtCnt() }][{else}]0[{/if}]</dd>
                    </dl>
                [{/if}]
                [{if $oViewConf->getShowCompareList()}]
                    <dl>
                        <dt><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=compare" }]" rel="nofollow">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_MYPRODUCTCOMPARISON" }]</a></dt>
                        <dd>[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_PRODUCT3" }] [{ if $oView->getCompareItemsCnt() }][{ $oView->getCompareItemsCnt() }][{else}]0[{/if}]</dd>
                    </dl>
                [{/if}]
                [{if $oViewConf->getShowListmania()}]
                    <dl>
                        <dt><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_recommlist" }]">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_MYRECOMMLIST" }]</a></dt>
                        <dd>[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_LISTS" }] [{ if $oxcmp_user->getRecommListsCount() }][{ $oxcmp_user->getRecommListsCount() }][{else}]0[{/if}]</dd>
                    </dl>
                [{/if}]
            [{/block}]
        </div>
        <div class="clear"></div>
    </div>
        
            <a href="[{ $oViewConf->getLogoutLink() }]" class="submitButton largeButton">[{ oxmultilang ident="PAGE_ACCOUNT_DASHBOARD_LOGOUT" }]</a>
        
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]


[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]