[{if $oView->getWishList() }]
    <form name="wishlist_wishlist_status" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        <div class="wishlistPublish clear">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="fnc" value="togglepublic">
            <input type="hidden" name="cl" value="account_wishlist">
            <ul class="form clear">
                <li class="formTitle"><label>[{ oxmultilang ident="PUBLISH" }]</label></li>
                <li>
                    <label>[{ oxmultilang ident="MESSAGE_MAKE_GIFT_REGISTRY_PUBLISH" suffix="COLON" }]</label>
                    <select name="blpublic">
                        <option value="0">[{ oxmultilang ident="NO" }]</option>
                        [{assign var="wishlist" value=$oView->getWishList() }]
                        <option value="1"  [{if $wishlist->oxuserbaskets__oxpublic->value }]selected [{/if }] >[{ oxmultilang ident="YES" }]</option>
                    </select>
                </li>
                <li>
                    <button class="submitButton" type="submit">[{ oxmultilang ident="SAVE" }]</button>
                </li>
            </ul>
            <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" params="blshowsuggest=1" }]">
                [{ oxmultilang ident="MESSAGE_SEND_GIFT_REGISTRY" }]
            </a>
        </div>
    </form>
[{/if}]