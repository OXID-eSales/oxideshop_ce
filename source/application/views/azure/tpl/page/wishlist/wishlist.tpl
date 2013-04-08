[{capture append="oxidBlock_content"}]
[{assign var="wishuser" value=$oView->getWishUser()}]
    [{if !$oView->getWishListUsers() && $oView->getWishListSearchParam() }]
        [{assign var="_statusMessage" value="PAGE_ACCOUNT_WISHLIST_SORRYNOWISHLIST"|oxmultilangassign}]
        [{include file="message/error.tpl" statusMessage=$_statusMessage}]
    [{/if }]
    <h1 class="pageHead">[{if $wishuser}][{ oxmultilang ident="PAGE_WISHLIST_PRODUCTS_WELCOME" }] [{$wishuser->oxuser__oxfname->value}] [{$wishuser->oxuser__oxlname->value}][{else}][{ oxmultilang ident="PAGE_WISHLIST_PRODUCTS_TITLE" }][{/if}]</h1>
    <div class="wishlistView clear bottomRound">
        [{include file="form/wishlist_search.tpl" searchClass="wishlist"}]
        [{if $oView->getWishList()}]
            [{assign var="wishuser" value=$oView->getWishUser()}]
            <p class="wishlistUser">
            [{ oxmultilang ident="PAGE_WISHLIST_PRODUCTS_PRODUCTS1" }] [{$wishuser->oxuser__oxfname->value}] [{$wishuser->oxuser__oxlname->value}][{ oxmultilang ident="PAGE_WISHLIST_PRODUCTS_PRODUCTS2" }]
            </p>
        [{/if}]
    </div>
    [{if $oView->getWishList()}]
        [{include file="widget/product/list.tpl" type="line" title="" listId="wishlistProductList" products=$oView->getWishList() owishid=$wishuser->oxuser__oxid->value}]
    [{else }]
        [{ oxmultilang ident="PAGE_WISHLIST_PRODUCTS_WISHLISTEMPTY" }]
    [{/if }]
    [{ insert name="oxid_tracker"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left" }]
