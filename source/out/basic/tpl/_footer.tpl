        <div class="clear"></div>
        </div>

        <div id="footer">
        <div class="bar copy">
            <div class="left" id="delivery_link">
                [{if $oView->isPriceCalculated() }]
                    [{oxifcontent ident="oxdeliveryinfo" object="oCont"}]
                        [{if $oView->isVatIncluded()}]
                            <a href="[{ $oCont->getLink() }]" rel="nofollow">[{ oxmultilang ident="INC_FOOTER_INCLTAXANDPLUSSHIPPING" }]</a>
                        [{else}]
                            <a href="[{ $oCont->getLink() }]" rel="nofollow"> [{ oxmultilang ident="PLUS_SHIPPING3" }]</a>
                        [{/if}]
                    [{/oxifcontent}]
                [{/if}]
            </div>
            <div class="right">
                &copy; <a href="[{ oxmultilang ident="OXID_ESALES_URL" }]" title="[{ oxmultilang ident="OXID_ESALES_URL_TITLE" }]">[{ oxmultilang ident="INC_FOOTER_SOFTWAREFROMOXIDESALES" }]</a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="bar shop">
            <a id="test_link_footer_home" href="[{ $oViewConf->getHomeLink() }]">[{ oxmultilang ident="INC_FOOTER_HOME" }]</a> |
            <a id="test_link_footer_contact" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=contact" }]">[{ oxmultilang ident="INC_FOOTER_CONTACT" }]</a> |
            <a id="test_link_footer_help" href="[{ $oViewConf->getHelpPageLink() }]">[{ oxmultilang ident="INC_FOOTER_HELP" }]</a> |
            <a id="test_link_footer_guestbook" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=guestbook" }]">[{ oxmultilang ident="INC_FOOTER_GUESTBOOK" }]</a> |
            <a id="test_link_footer_links" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=links" }]">[{ oxmultilang ident="INC_FOOTER_LINKS" }]</a> |
            [{oxifcontent ident="oximpressum" object="oCont"}]
            <a id="test_link_footer_impressum" href="[{ $oCont->getLink() }]">[{ $oCont->oxcontents__oxtitle->value }]</a> |
            [{/oxifcontent}]
            [{oxifcontent ident="oxagb" object="oCont"}]
            <a id="test_link_footer_terms" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a> |
            [{/oxifcontent}]
            <br>
            [{oxhasrights ident="TOBASKET"}]
            <a id="test_link_footer_basket" href="[{ oxgetseourl ident=$oViewConf->getBasketLink() }]" rel="nofollow">[{ oxmultilang ident="INC_FOOTER_CART" }]</a> |
            [{/oxhasrights}]
            <a id="test_link_footer_account" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" }]" rel="nofollow">[{ oxmultilang ident="INC_FOOTER_MYACCOUNT" }]</a> |
            <a id="test_link_footer_noticelist" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_noticelist" }]" rel="nofollow"> [{ oxmultilang ident="INC_FOOTER_MYNOTICELIST" }]</a>
            [{if $oViewConf->getShowWishlist()}]
              | <a id="test_link_footer_wishlist" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_wishlist" }]" rel="nofollow"> [{ oxmultilang ident="INC_FOOTER_MYWISHLIST" }]</a>
              | <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" params="wishid="|cat:$oView->getWishlistUserId() }]" rel="nofollow">[{ oxmultilang ident="INC_FOOTER_PUBLICWISHLIST" }]</a>
            [{/if}]
            [{if $oView->isEnabledDownloadableFiles()}]
              | <a id="test_link_footer_downloads" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_downloads" }]" rel="nofollow"> [{ oxmultilang ident="MY_DOWNLOADS" }]</a>
            [{/if}]
        </div>
        <div class="bar icons">
            [{*
            <a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-html401" alt="Valid HTML 4.01 Strict" height="31" width="88"></a>
            <a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="http://www.w3.org/Icons/valid-css2" alt="Valid CSS!" /></a>
            *}]
        </div>

        <div class="shopicons">
            <div class="left"><img src="[{$oViewConf->getImageUrl()}]cc.jpg" alt=""></div>
            <div class="right"><a href="[{ oxmultilang ident="OXID_ESALES_URL" }]" title="[{ oxmultilang ident="OXID_ESALES_URL_TITLE" }]"><img src="[{$oViewConf->getImageUrl()}]oxid_powered.jpg" alt="[{ oxmultilang ident="INC_FOOTER_SOFTWAREANDSYSTEMBYOXID" }]" height="30" width="80"></a></div>
        </div>

        [{oxifcontent ident="oxstdfooter" object="oCont"}]
        <div class="footertext">[{$oCont->oxcontents__oxcontent->value}]</div>
        [{/oxifcontent}]
    </div>
</div>
</div>
<div id="mask"></div>

[{if $popup}][{include file=$popup}][{/if}]
[{oxid_include_dynamic file="dyn/newbasketitem_popup.tpl"}]

[{if $oView->showFbConnectToAccountMsg()}]
[{ insert name="oxid_fblogin"}]
[{/if}]

[{oxid_include_dynamic file="dyn/popup_scbasketexcl.tpl"}]
[{oxscript include="oxid.js"}]

[{ include file="inc/facebook/fb_init.tpl" }]

[{oxscript}][{oxid_include_dynamic file="dyn/oxscript.tpl" }]
<!--[if lt IE 7]><script type="text/javascript">oxid.popup.addShim();</script><![endif]-->
</body>
</html>
