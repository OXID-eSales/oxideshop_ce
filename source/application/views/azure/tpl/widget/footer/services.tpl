[{block name="footer_services"}]
    <dl class="services" id="footerServices">
        <dt>[{oxmultilang ident="FOOTER_SERVICES" }]</dt>
        <dd>
            <ul class="list services">
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=contact" }]">[{ oxmultilang ident="CONTACT" }]</a></li>
                <li><a href="[{ $oViewConf->getHelpPageLink() }]">[{ oxmultilang ident="WIDGET_SERVICES_HELP" }]</a></li>
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=links" }]">[{ oxmultilang ident="WIDGET_SERVICES_LINKS" }]</a></li>
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=guestbook" }]">[{ oxmultilang ident="WIDGET_SERVICES_GUESTBOOK" }]</a></li>
                [{if $oView->isActive('Invitations') }]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=invite" }]" rel="nofollow">[{ oxmultilang ident="WIDGET_SERVICES_INVITEFRIENDS" }]</a></li>
                [{/if}]
                [{oxhasrights ident="TOBASKET"}]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getBasketLink() }]" rel="nofollow">[{ oxmultilang ident="WIDGET_SERVICES_BASKET" }]</a></li>
                [{/oxhasrights}]
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" }]" rel="nofollow">[{ oxmultilang ident="WIDGET_SERVICES_ACCOUNT" }]</a></li>
                <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" rel="nofollow">[{ oxmultilang ident="WIDGET_SERVICES_NOTICELIST" }]</a></li>
                [{if $oViewConf->getShowWishlist()}]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" rel="nofollow">[{ oxmultilang ident="WIDGET_SERVICES_MYWISHLIST" }]</a></li>
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" params="wishid="|cat:$oView->getWishlistUserId() }]" rel="nofollow">[{ oxmultilang ident="WIDGET_SERVICES_PUBLICWISHLIST" }]</a></li>
                [{/if}]
                [{if $oView->isEnabledDownloadableFiles()}]
                    <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads" }]" rel="nofollow">[{ oxmultilang ident="MY_DOWNLOADS" }]</a></li>
                [{/if}]
            </ul>
        </dd>
    </dl>
[{/block}]