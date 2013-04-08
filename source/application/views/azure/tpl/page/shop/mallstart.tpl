[{capture append="oxidBlock_pageBody"}]

<div id="page">
    <div id="header" class="clear">
       [{include file="widget/header/languages.tpl"}]
       <div><a id="logo" href="[{$oViewConf->getHomeLink()}]" title="[{$oxcmp_shop->oxshops__oxtitleprefix->value}]"><img src="[{$oViewConf->getImageUrl('logo.png')}]" alt="[{$oxcmp_shop->oxshops__oxtitleprefix->value}]"></a></div>
    </div>
    <div>
        [{oxifcontent ident="oxstartwelcome" object="oCont"}]
            [{$oCont->oxcontents__oxcontent->value}]
        [{/oxifcontent}]
        <div>
            [{ oxmultilang ident="PLEASE_CHOOSE" suffix="COLON" }]
            <br>
            <br>
            [{assign var="shoplinks" value=$oView->getShopLinks()}]
            [{assign var="shoplangs" value=$oView->getShopDefaultLangs()}]
            [{foreach from=$oView->getShopList() item=mallshop key=shopid}]
                [{if $shoplinks.$shopid}]
                    <a href="[{$shoplinks.$shopid}]">[{ $mallshop->oxshops__oxname->value }]</a><br>
                [{else}]
                    <a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=mallstart" params="fnc=chshp&amp;shp=`$mallshop->oxshops__oxid->value`&amp;lang=`$shoplangs.$shopid`" }]" >[{ $mallshop->oxshops__oxname->value }]</a><br>
                [{/if}]
            [{/foreach}]
         </div>
        [{if $oView->isDemoShop()}]
            <a id="demoAdminLink" href="[{ $oViewConf->getBaseDir() }]admin/" rel="nofollow"><img src="[{ $oViewConf->getImageUrl('admin_start.jpg') }]"></a>
        [{/if}]
    </div>


    <div id="footer">
        <div class="copyright">
            <img src="[{$oViewConf->getImageUrl('logo_small.png')}]" alt="[{oxmultilang ident="OXID_ESALES_URL_TITLE"}]">
        </div>
        <div class="text">
            [{oxifcontent ident="oxstdfooter" object="oCont"}]
            [{$oCont->oxcontents__oxcontent->value}]
            [{/oxifcontent}]
        </div>
    </div>
</div>
[{/capture}]
[{include file="layout/base.tpl"}]
