[{block name="footer_services"}]
    <dl class="list services" id="footerServices">
        <dt class="list-header">[{oxmultilang ident="SERVICES"}]</dt>
        [{block name="footer_services_items"}]
            <dd>
                <a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=contact"}]">[{oxmultilang ident="CONTACT"}]</a>
            </dd>
            [{if $oViewConf->getHelpPageLink()}]
                <dd><a href="[{$oViewConf->getHelpPageLink()}]">[{oxmultilang ident="HELP"}]</a></dd>
            [{/if}]
            <dd>
                <a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=links"}]">[{oxmultilang ident="LINKS"}]</a>
            </dd>
            [{if $oView->isActive('Invitations')}]
                <dd><a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=invite"}]"
                       rel="nofollow">[{oxmultilang ident="INVITE_YOUR_FRIENDS"}]</a></dd>
            [{/if}]
            [{oxhasrights ident="TOBASKET"}]
                <dd><a href="[{oxgetseourl ident=$oViewConf->getBasketLink()}]"
                       rel="nofollow">[{oxmultilang ident="CART"}]</a></dd>
            [{/oxhasrights}]
            <dd><a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account"}]"
                   rel="nofollow">[{oxmultilang ident="ACCOUNT"}]</a></dd>
            <dd><a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist"}]"
                   rel="nofollow">[{oxmultilang ident="WISH_LIST"}]</a></dd>
            [{if $oViewConf->getShowWishlist()}]
                <dd><a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist"}]"
                       rel="nofollow">[{oxmultilang ident="MY_GIFT_REGISTRY"}]</a></dd>
                <dd>
                    <a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" params="wishid="|cat:$oView->getWishlistUserId()}]"
                       rel="nofollow">[{oxmultilang ident="PUBLIC_GIFT_REGISTRIES"}]</a></dd>
            [{/if}]
            [{if $oView->isEnabledDownloadableFiles()}]
                <dd><a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads"}]"
                       rel="nofollow">[{oxmultilang ident="MY_DOWNLOADS"}]</a></dd>
            [{/if}]
        [{/block}]
    </dl>
[{/block}]