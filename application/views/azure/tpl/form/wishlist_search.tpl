<div class="wishlistSearch clear">
    <form name="wishlist_searchbox" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="[{$searchClass}]">
        <input type="hidden" name="fnc" value="searchforwishlist">
        <ul class="form clear">
            <li class="formTitle"><label>[{ oxmultilang ident="SEARCH_GIFT_REGISTRY" }]</label></li>
            <li>
                <label>[{ oxmultilang ident="ENTER_EMAIL_OR_NAME" suffix="COLON"}]&nbsp;</label>
                <input type="text" name="search" value="[{ $oView->getWishListSearchParam() }]" size="30">&nbsp;&nbsp;
                <button class="submitButton" type="submit">[{ oxmultilang ident="SEARCH" }]</button>
            </li>
        </ul>
    </form>
    <div>
    [{if $oView->getWishListUsers() }]
    <dl class="wishlistResults">
        <dt>[{ oxmultilang ident="GIFT_REGISTRY_SEARCH_RESULTS" suffix="COLON" }]</dt>
        [{foreach from=$oView->getWishListUsers() item=wishres }]
            <dd>
            <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" params="wishid=`$wishres->oxuser__oxid->value`" }]">
                [{ oxmultilang ident="GIFT_REGISTRY_OF" }] [{ $wishres->oxuser__oxfname->value }]&nbsp;[{ $wishres->oxuser__oxlname->value }]
            </a>
            </dd>
        [{/foreach }]
    </dl>    
    [{/if }]
    </div>
</div>