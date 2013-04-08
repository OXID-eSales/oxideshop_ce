[{block name="footer_services"}]
    <dl class="services" id="footerServices">
        <dt>[{oxmultilang ident="SERVICES" }]</dt>
        <dd>
            <ul class="list services">
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=contact" }]">[{ oxmultilang ident="CONTACT" }]</a></li>
                <li><a href="[{ $oViewConf->getHelpPageLink() }]">[{ oxmultilang ident="HELP" }]</a></li>
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=links" }]">[{ oxmultilang ident="LINKS" }]</a></li>
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=guestbook" }]">[{ oxmultilang ident="GUESTBOOK" }]</a></li>
                [{if $oView->isActive('Invitations') }]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=invite" }]" rel="nofollow">[{ oxmultilang ident="INVITE_YOUR_FRIENDS" }]</a></li>
                [{/if}]
                [{oxhasrights ident="TOBASKET"}]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getBasketLink() }]" rel="nofollow">[{ oxmultilang ident="CART" }]</a></li>
                [{/oxhasrights}]
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" }]" rel="nofollow">[{ oxmultilang ident="ACCOUNT" }]</a></li>
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" rel="nofollow">[{ oxmultilang ident="WISH_LIST" }]</a></li>
                [{if $oViewConf->getShowWishlist()}]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" rel="nofollow">[{ oxmultilang ident="MY_GIFT_REGISTRY" }]</a></li>
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" params="wishid="|cat:$oView->getWishlistUserId() }]" rel="nofollow">[{ oxmultilang ident="PUBLIC_GIFT_REGISTRIES" }]</a></li>
                [{/if}]
                [{if $oView->isEnabledDownloadableFiles()}]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads" }]" rel="nofollow">[{ oxmultilang ident="MY_DOWNLOADS" }]</a></li>
                [{/if}]
            </ul>
        </dd>
    </dl>
[{/block}]