[{assign var="template_title" value="WISHLIST_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

<strong id="test_giftRegistryHeader" class="boxhead">[{$template_title}]</strong>
<div class="box info">
    <form name="wishlist_searchbox" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        <div>
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="cl" value="wishlist">
            <input type="hidden" name="fnc" value="searchforwishlist">
            <table class="form">
              <tr>
                <td><label>[{ oxmultilang ident="ACCOUNT_WISHLIST_ENTEREMAILORNAME" }]&nbsp;</label></td>
                <td><input type="text" name="search" value="[{ $oView->getWishListSearchParam() }]" size="30"></td>
                <td>&nbsp;</td>
                <td><span class="btn"><input id="test_WishlistSearch" type="submit" value="[{ oxmultilang ident="ACCOUNT_WISHLIST_SEARCH" }]" class="btn"></span></td>
              </tr>
            </table>
        </div>
    </form>
    <div class="wishsearchresults">
    [{if $oView->getWishListUsers() }]
        [{foreach from=$oView->getWishListUsers() item=wishres }]
            <div class="searchitem">
                <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" params="wishid=`$wishres->oxuser__oxid->value`" }]">
                 [{ oxmultilang ident="ACCOUNT_WISHLIST_WISHLISTOFF" }] [{ $wishres->oxuser__oxfname->value }]&nbsp;[{ $wishres->oxuser__oxlname->value }]
               </a>
            </div>
        [{/foreach }]
    [{else }]
        [{if $oView->getWishListSearchParam() }]
          <div class="errorbox inbox">[{ oxmultilang ident="ACCOUNT_WISHLIST_SORRYNOWISHLIST" }]</div>
        [{/if }]
    [{/if }]
    </div>
  [{if $oView->getWishUser()}]
      [{assign var="wishuser" value=$oView->getWishUser()}]
      <div class="dot_sep"></div><br>
      [{ oxmultilang ident="WISHLIST_WELCOME" }] [{$wishuser->oxuser__oxfname->value}] [{$wishuser->oxuser__oxlname->value}]
      <div class="dot_sep"></div><br>
      [{if $oView->getWishList()}]
         <div class="wishlist">
            [{foreach from=$oView->getWishList() name=wishlist item=product}]

              [{if $smarty.foreach.wishlist.first}]
                [{assign var="wishlist_class" value="firstinlist"}]
              [{elseif $smarty.foreach.wishlist.last}]
                [{assign var="wishlist_class" value="lastinlist"}]
              [{else}]
                [{assign var="wishlist_class" value="inlist"}]
              [{/if}]

              [{include file="inc/product.tpl" product=$product size="thin" class=$wishlist_class toBasketFunction="wl_tobasket" testid="WishList_`$smarty.foreach.wishlist.iteration`"}]

              [{if !$smarty.foreach.wishlist.last }]
                <div class="separator"></div>
              [{/if}]

            [{/foreach}]

            <br>
            <div class="dot_sep"></div>
            <br>
            [{assign var="wishuser" value=$oView->getWishUser()}]
            [{ oxmultilang ident="WISHLIST_PRODUCTS1" }] [{$wishuser->oxuser__oxfname->value}] [{$wishuser->oxuser__oxlname->value}][{ oxmultilang ident="WISHLIST_PRODUCTS2" }]

         </div>
      [{else }]
         [{ oxmultilang ident="WISHLIST_WISHLISTEMPTY" }]
      [{/if }]
    [{/if}]
</div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
