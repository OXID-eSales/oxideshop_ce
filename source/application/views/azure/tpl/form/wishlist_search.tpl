<div class="wishlistSearch clear">
    <form name="wishlist_searchbox" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="[{$searchClass}]">
        <input type="hidden" name="fnc" value="searchforwishlist">
        <ul class="form clear">
            <li class="formTitle"><label>[{ oxmultilang ident="FORM_WISHLIST_SEARCH_SEARCHWISHLIST" }]</label></li>
            <li>
                <label>[{ oxmultilang ident="FORM_WISHLIST_SEARCH_ENTEREMAILORNAME" }]&nbsp;</label>
                <input type="text" name="search" value="[{ $oView->getWishListSearchParam() }]" size="30">&nbsp;&nbsp;
                <button class="submitButton" type="submit">[{ oxmultilang ident="FORM_WISHLIST_SEARCH_SEARCH" }]</button>
            </li>
        </ul>
    </form>
    <div>
    [{if $oView->getWishListUsers() }]
    <dl class="wishlistResults">
        <dt>[{ oxmultilang ident="FORM_WISHLIST_SEARCH_RESULTS" }]</dt>
        [{foreach from=$oView->getWishListUsers() item=wishres }]
            <dd>
            <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" params="wishid=`$wishres->oxuser__oxid->value`" }]">
                [{ oxmultilang ident="FORM_WISHLIST_SEARCH_WISHLISTOFF" }] [{ $wishres->oxuser__oxfname->value }]&nbsp;[{ $wishres->oxuser__oxlname->value }]
            </a>
            </dd>
        [{/foreach }]
    </dl>    
    [{/if }]
    </div>
</div>