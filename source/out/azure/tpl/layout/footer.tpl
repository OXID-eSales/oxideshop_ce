[{block name="footer_main"}]
    [{oxscript include="js/widgets/oxequalizer.js" priority=10 }]
    [{oxscript add="$(function(){oxEqualizer.equalHeight($( '#panel dl' ));});"}]
    <div id="footer">
        <div id="panel" class="corners">
                <div class="bar">
                    [{block name="footer_fblike"}]
                        [{if $oView->isActive('FbLike') && $oViewConf->getFbAppId()}]
                            <div class="facebook" id="footerFbLike">
                                [{include file="widget/facebook/enable.tpl" source="widget/facebook/like.tpl" ident="#footerFbLike" parent="footer"}]
                            </div>
                        [{/if}]
                    [{/block}]
                    [{include file="widget/footer/newsletter.tpl"}]
                    [{block name="footer_deliveryinfo"}]
                        <div class="deliveryinfo">
                            [{oxifcontent ident="oxdeliveryinfo" object="oCont"}]
                                [{if $oView->getUser()}]
                                    [{assign var="oUser" value=$oView->getUser() }]
                                    [{ if $oUser->oxuser__oxustid->value && $oUser->oxuser__oxcompany->value }] <a href="[{ $oCont->getLink() }]" rel="nofollow"> [{ oxmultilang ident="PLUS_SHIPPING3" }]</a> [{else}] <a href="[{ $oCont->getLink() }]" rel="nofollow">[{ oxmultilang ident="FOOTER_INCLTAXANDPLUSSHIPPING" }]</a> [{/if}]
                                [{else}]
                                    <a href="[{ $oCont->getLink() }]" rel="nofollow">[{ oxmultilang ident="FOOTER_INCLTAXANDPLUSSHIPPING" }]</a>
                                [{/if}]
                            [{/oxifcontent}]
                            
                        </div>
                    [{/block}]
                </div>

                [{block name="footer_services"}]
                    <dl class="services" id="footerServices">
                        <dt>[{oxmultilang ident="FOOTER_SERVICES" }]</dt>
                        <dd>[{include file="widget/footer/services.tpl"}]</dd>
                    </dl>
                [{/block}]

                [{block name="footer_information"}]
                    <dl id="footerInformation">
                        <dt>[{oxmultilang ident="FOOTER_INFORMATION" }]</dt>
                        <dd>[{include file="widget/footer/info.tpl"}]</dd>
                    </dl>
                [{/block}]

            [{if $oView->getManufacturerlist()|count}]
                    [{block name="footer_manufacturers"}]
                        <dl id="footerManufacturers">
                            <dt>[{oxmultilang ident="FOOTER_MANUFACTURERS" }]</dt>
                          <dd>[{include file="widget/footer/manufacturers.tpl" manufacturers=$oView->getManufacturerlist()}]</dd>
                        </dl>
                    [{/block}]
            [{/if}]

            [{if $oView->getVendorlist()|count}]
                    [{block name="footer_vendors"}]
                        <dl id="footerVendors">
                            <dt>[{oxmultilang ident="FOOTER_DISTRIBUTORS" }]</dt>
                            <dd>[{include file="widget/footer/vendors.tpl" vendors=$oView->getVendorlist()}]</dd>
                        </dl>
                    [{/block}]
            [{/if}]

            [{if $oxcmp_categories }]
                    [{block name="footer_categories"}]
                        <dl class="categories" id="footerCategories">
                            <dt>[{oxmultilang ident="FOOTER_CATEGORIES" }]</dt>
                            <dd>[{include file="widget/footer/categorieslist.tpl" categories=$oxcmp_categories}]</dd>
                        </dl>
                    [{/block}]
            [{/if}]
        </div>
        <div class="copyright">
            <img src="[{$oViewConf->getImageUrl('logo_small.png')}]" alt="[{oxmultilang ident="OXID_ESALES_URL_TITLE"}]">
        </div>
        <div class="text">
            [{oxifcontent ident="oxstdfooter" object="oCont"}]
                [{$oCont->oxcontents__oxcontent->value}]
            [{/oxifcontent}]
        </div>
    </div>
[{/block}]
[{if $oView->isRootCatChanged()}]
    [{oxscript include="js/widgets/oxmodalpopup.js" priority=10 }]
    [{oxscript add="$( '#scRootCatChanged' ).oxModalPopup({ target: '#scRootCatChanged', openDialog: true});"}]
    <div id="scRootCatChanged" class="popupBox corners FXgradGreyLight glowShadow">
        [{include file="form/privatesales/basketexcl.tpl"}]
    </div>
[{/if}]