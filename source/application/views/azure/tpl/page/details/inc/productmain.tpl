[{assign var="aVariantSelections" value=$oView->getVariantSelections()}]

[{if $aVariantSelections && $aVariantSelections.rawselections}]
    [{assign var="_sSelectionHashCollection" value=""}]
    [{foreach from=$aVariantSelections.rawselections item=oSelectionList key=iKey}]
        [{assign var="_sSelectionHash" value=""}]
        [{foreach from=$oSelectionList item=oListItem key=iPos}]
            [{assign var="_sSelectionHash" value=$_sSelectionHash|cat:$iPos|cat:":"|cat:$oListItem.hash|cat:"|"}]
        [{/foreach}]
        [{if $_sSelectionHash}]
            [{if $_sSelectionHashCollection}][{assign var="_sSelectionHashCollection" value=$_sSelectionHashCollection|cat:","}][{/if}]
            [{assign var="_sSelectionHashCollection" value=$_sSelectionHashCollection|cat:"'`$_sSelectionHash`'"}]
        [{/if}]
    [{/foreach}]
    [{oxscript add="var oxVariantSelections  = [`$_sSelectionHashCollection`];"}]
[{/if}]

[{oxhasrights ident="TOBASKET"}]
    <form class="js-oxProductForm" action="[{$oViewConf->getSelfActionLink()}]" method="post">
        <div>
            [{$oViewConf->getHiddenSid()}]
            [{$oViewConf->getNavFormParams()}]
            <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
            <input type="hidden" name="aid" value="[{$oDetailsProduct->oxarticles__oxid->value}]">
            <input type="hidden" name="anid" value="[{$oDetailsProduct->oxarticles__oxnid->value}]">
            <input type="hidden" name="parentid" value="[{if !$oDetailsProduct->oxarticles__oxparentid->value}][{$oDetailsProduct->oxarticles__oxid->value}][{else}][{$oDetailsProduct->oxarticles__oxparentid->value}][{/if}]">
            <input type="hidden" name="panid" value="">
            [{if !$oDetailsProduct->isNotBuyable()}]
                <input type="hidden" name="fnc" value="tobasket">
            [{/if}]
        </div>
[{/oxhasrights}]

<div class="detailsInfo clear">
    [{* article picture with zoom *}]
    [{block name="details_productmain_zoom"}]
        [{oxscript include="js/libs/cloudzoom.js" priority=10}]
        [{if $oView->showZoomPics()}]
            [{oxscript include="js/widgets/oxmodalpopup.js" priority=10 }]
            [{oxscript add="$('#zoomTrigger').oxModalPopup({target:'#zoomModal'});"}]
            <a id="zoomTrigger" rel="nofollow" href="#">Zoom</a>
            [{oxscript add="$('#zoom1').attr( 'rel', $('#zoom1').attr('data-zoomparams'));"}]
            [{oxscript add="$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();"}]
            <div class="picture">
                <a href="[{$oPictureProduct->getMasterZoomPictureUrl(1)}]" class="cloud-zoom" id="zoom1" rel='' data-zoomparams="adjustY:-2, zoomWidth:'354', fixZoomWindow:'390', trImg:'[{$oViewConf->getImageUrl('dot.png')}]', loadingText:'[{oxmultilang ident="PAGE_DETAILS_ZOOM_LOADING"}]'">
                    <img src="[{$oView->getActPicture()}]" alt="[{$oPictureProduct->oxarticles__oxtitle->value|strip_tags}] [{$oPictureProduct->oxarticles__oxvarselect->value|strip_tags}]">
                </a>
            </div>
        [{else}]
            <div class="picture">
                <img src="[{$oView->getActPicture()}]" alt="[{$oPictureProduct->oxarticles__oxtitle->value|strip_tags}] [{$oPictureProduct->oxarticles__oxvarselect->value|strip_tags}]">
            </div>
        [{/if}]
    [{/block}]

    [{* article main info block *}]
    <div class="information">

        [{ assign var="oManufacturer" value=$oView->getManufacturer()}]
        <div class="productMainInfo[{if $oManufacturer->oxmanufacturers__oxicon->value}] hasBrand[{/if}]">

            [{oxscript include="js/widgets/oxarticleactionlinksselect.js" priority=10 }]
            [{oxscript add="$( '#productTitle' ).oxArticleActionLinksSelect();"}]

            [{* Product title *}]
            [{block name="details_productmain_title"}]
                <h1 id="productTitle"><span>[{$oDetailsProduct->oxarticles__oxtitle->value}] [{$oDetailsProduct->oxarticles__oxvarselect->value}]</span></h1>
            [{/block}]

            [{* Actions select list: to listmania and etc. *}]
            <div id="showLinksOnce"></div>

            [{block name="details_productmain_productlinksselector"}]
                <a class="selector corners FXgradBlueDark" href="#" id="productLinks"><img src="[{$oViewConf->getImageUrl('selectbutton.png')}]" alt="Select"></a>
                <ul class="actionLinks corners shadow">
                    [{block name="details_productmain_productlinks"}]
                        [{if $oViewConf->getShowCompareList() }]
                            <li><span>[{oxid_include_dynamic file="page/details/inc/compare_links.tpl" testid="" type="compare" aid=$oDetailsProduct->oxarticles__oxid->value anid=$oDetailsProduct->oxarticles__oxnid->value in_list=$oDetailsProduct->isOnComparisonList() page=$oView->getActPage() text_to_id="PAGE_DETAILS_COMPARE" text_from_id="PAGE_DETAILS_REMOVEFROMCOMPARELIST"}]</span></li>
                        [{/if}]
                        <li>
                            <span><a id="suggest" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=suggest" params="anid=`$oDetailsProduct->oxarticles__oxnid->value`"|cat:$oViewConf->getNavUrlParams() }]">[{ oxmultilang ident="PAGE_DETAILS_RECOMMEND" }]</a></span>
                        </li>
                        [{if $oViewConf->getShowListmania()}]
                            <li>
                                <span>
                                    [{if $oxcmp_user }]
                                        <a id="recommList" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=recommadd" params="aid=`$oDetailsProduct->oxarticles__oxnid->value`&amp;anid=`$oDetailsProduct->oxarticles__oxnid->value`"|cat:$oViewConf->getNavUrlParams() }]">[{ oxmultilang ident="PAGE_DETAILS_ADDTORECOMMLIST" }]</a>
                                    [{ else}]
                                        <a id="loginToRecommlist" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$oDetailsProduct->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]">[{ oxmultilang ident="PAGE_DETAILS_LOGGINTOACCESSRECOMMLIST" }]</a>
                                    [{/if}]
                                </span>
                            </li>
                        [{ /if}]
                        [{if $oxcmp_user }]
                            <li><span><a id="linkToNoticeList" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$oDetailsProduct->oxarticles__oxnid->value`&amp;anid=`$oDetailsProduct->oxarticles__oxnid->value`&amp;fnc=tonoticelist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="PAGE_DETAILS_ADDTONOTICELIST" }]</a></span></li>
                        [{else}]
                            <li><span><a id="loginToNotice" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$oDetailsProduct->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="PAGE_DETAILS_LOGGINTOACCESSNOTICELIST" }]</a></span></li>
                        [{/if}]
                        [{if $oViewConf->getShowWishlist()}]
                            [{if $oxcmp_user }]
                                <li><span><a id="linkToWishList" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$oDetailsProduct->oxarticles__oxnid->value`&anid=`$oDetailsProduct->oxarticles__oxnid->value`&amp;fnc=towishlist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="PAGE_DETAILS_ADDTOWISHLIST" }]</a></span></li>
                            [{else}]
                                <li><span><a id="loginToWish" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$oDetailsProduct->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="PAGE_DETAILS_LOGGINTOACCESSWISHLIST" }]</a></span></li>
                            [{/if}]
                        [{/if}]

                        [{oxhasrights ident="TOBASKET"}]
                            [{if $oView->isPriceAlarm() && $oDetailsProduct->isBuyable()}]
                                <li><a id="priceAlarmLink" rel="nofollow" href="[{ $oDetailsProduct->getLink()|cat:'#itemTabs'}]">[{oxmultilang ident="DETAILS_PRICEALARM"}]</a></li>
                            [{/if}]
                        [{/oxhasrights}]
                        <li>
                           <span>[{mailto extra='id="questionMail"' address=$oDetailsProduct->oxarticles__oxquestionemail->value|default:$oxcmp_shop->oxshops__oxinfoemail->value subject='DETAILS_QUESTIONSSUBJECT'|oxmultilangassign|cat:" "|cat:$oDetailsProduct->oxarticles__oxartnum->value text='DETAILS_QUESTIONS'|oxmultilangassign }]</span>
                        </li>
                    [{/block}]
                </ul>
            [{/block}]

            [{* artickle number *}]
            [{block name="details_productmain_artnumber"}]
                <span id="productArtnum" class="itemCode">[{oxmultilang ident="DETAILS_ARTNUMBER"}] [{$oDetailsProduct->oxarticles__oxartnum->value}]</span>
            [{/block}]

            [{* ratings *}]
            [{ if $oView->ratingIsActive()}]
            [{block name="details_productmain_ratings"}]
                <div class="rating clear">
                    [{include file="widget/reviews/rating.tpl" itemid="anid=`$oDetailsProduct->oxarticles__oxnid->value`" sRateUrl=$oDetailsProduct->getLink() }]
                </div>
            [{/block}]
            [{/if}]
        </div>

        [{block name="details_productmain_manufacturersicon"}]
            [{if $oManufacturer->oxmanufacturers__oxicon->value}]
                <img src="[{$oManufacturer->getIconUrl()}]" alt="[{ $oManufacturer->oxmanufacturers__oxtitle->value}]">
            [{/if}]
        [{/block}]

        [{* short description *}]
        [{block name="details_productmain_shortdesc"}]
            [{oxhasrights ident="SHOWSHORTDESCRIPTION"}]
                [{if $oDetailsProduct->oxarticles__oxshortdesc->value}]
                    <div class="shortDescription description" id="productShortdesc">[{$oDetailsProduct->oxarticles__oxshortdesc->value}]</div>
                [{/if}]
            [{/oxhasrights}]
        [{/block}]

        [{assign var="blCanBuy" value=true}]
        [{* variants | md variants *}]
        [{block name="details_productmain_variantselections"}]
            [{if $aVariantSelections && $aVariantSelections.selections }]
                [{oxscript include="js/widgets/oxajax.js" priority=10 }]
                [{oxscript include="js/widgets/oxarticlevariant.js" priority=10 }]
                [{oxscript add="$( '#variants' ).oxArticleVariant();"}]
                [{assign var="blCanBuy" value=$aVariantSelections.blPerfectFit}]
                <div id="variants" class="selectorsBox js-fnSubmit clear">
                    [{assign var="blHasActiveSelections" value=false}]
                    [{foreach from=$aVariantSelections.selections item=oList key=iKey}]
                        [{if $oList->getActiveSelection()}]
                            [{assign var="blHasActiveSelections" value=true}]
                        [{/if}]
                        [{include file="widget/product/selectbox.tpl" oSelectionList=$oList iKey=$iKey blInDetails=true}]
                    [{/foreach}]
                </div>

                [{if $blHasActiveSelections}]
                    <div class="variantReset">
                        [{* Reset link *}]
                        <a href="" class="reset">[{ oxmultilang ident="DETAILS_VARIANTS_RESETSELECTION" }]</a>

                        [{* Active selections *}]
                        <label>[{ oxmultilang ident="DETAILS_VARIANTS_SELECTEDCOMBINATION" }]</label>
                        [{assign var="sSelectionSep" value=""}]
                        [{strip}]
                            [{foreach from=$aVariantSelections.selections item=oSelectionList name=variantselections}]
                                [{assign var="oActiveSelection" value=$oSelectionList->getActiveSelection()}]
                                [{if $oActiveSelection }]
                                    [{$sSelectionSep}][{$oActiveSelection->getName()}]
                                    [{assign var="sSelectionSep" value=", "}]
                                [{/if}]
                            [{/foreach}]
                        [{/strip}]
                    </div>
                [{else}]
                    [{if !$blCanBuy && !$oDetailsProduct->isParentNotBuyable()}]
                        [{assign var="blCanBuy" value=true}]
                    [{/if}]
                [{/if}]
                    [{if !$blCanBuy }]
                        <div class="variantMessage">[{oxmultilang ident="DETAILS_CHOOSEVARIANT"}]</div>
                    [{/if}]

            [{/if}]
        [{/block}]

        [{* selection lists *}]
        [{block name="details_productmain_selectlists"}]
            [{if $oViewConf->showSelectLists()}]
                [{assign var="oSelections" value=$oDetailsProduct->getSelections()}]
                [{if $oSelections}]
                    <div class="selectorsBox js-fnSubmit clear" id="productSelections">
                        [{foreach from=$oSelections item=oList name=selections}]
                            [{include file="widget/product/selectbox.tpl" oSelectionList=$oList sFieldName="sel" iKey=$smarty.foreach.selections.index blHideDefault=true sSelType="seldrop"}]
                        [{/foreach}]
                    </div>
                [{/if}]
            [{/if}]
        [{/block}]

        <div class="tobasket">

            [{* pers params *}]
            [{block name="details_productmain_persparams"}]
                [{if $oView->isPersParam()}]
                    <div class="persparamBox clear">
                        <label for="persistentParam">[{ oxmultilang ident="LABEL" }]</label><input type="text" id="persistentParam" name="persparam[details]" value="[{ $oDetailsProduct->aPersistParam.text }]" size="35">
                    </div>
                [{/if}]
            [{/block}]

            [{block name="details_productmain_tprice"}]
                [{oxhasrights ident="SHOWARTICLEPRICE"}]
                    [{assign var=tprice value=$oDetailsProduct->getTPrice()}]
                    [{assign var=price  value=$oDetailsProduct->getPrice()}]
                    [{if $tprice && $tprice->getBruttoPrice() > $price->getBruttoPrice()}]
                        <p class="oldPrice">
                            <strong>[{oxmultilang ident="DETAILS_REDUCEDFROM"}] <del>[{$oDetailsProduct->getFTPrice()}] [{$currency->sign}]</del></strong>
                        </p>
                    [{/if}]
                [{/oxhasrights}]
            [{/block}]

            <div class="tobasketFunction clear">
                [{block name="details_productmain_price"}]
                    [{oxhasrights ident="SHOWARTICLEPRICE"}]
                        [{if $oDetailsProduct->getFPrice()}]
                            <label id="productPrice" class="price">
                                [{assign var="sFrom" value=""}]
                                [{assign var="fPrice" value=$oDetailsProduct->getFPrice()}]
                                [{if $oDetailsProduct->isParentNotBuyable() }]
                                    [{assign var="fPrice" value=$oDetailsProduct->getFVarMinPrice()}]
                                    [{if $oDetailsProduct->isRangePrice() }]
                                        [{assign var="sFrom" value="PRICE_FROM"|oxmultilangassign}]
                                    [{/if}]
                                [{/if}]
                                <strong >
                                    <span>[{$sFrom}] [{$fPrice}]</span>
                                    <span>[{ $currency->sign}]</span>
                                    [{if $oView->isVatIncluded() }]
                                    <span>*</>
                                    [{/if}]
                                </strong>
                            </label>
                        [{/if}]
                        [{oxscript include="js/widgets/oxamountpriceselect.js" priority=10 }]
                        [{if $oDetailsProduct->loadAmountPriceInfo()}]
                            [{include file="page/details/inc/priceinfo.tpl"}]
                        [{/if}]
                    [{/oxhasrights}]
                [{/block}]

                [{block name="details_productmain_tobasket"}]
                    [{oxhasrights ident="TOBASKET"}]
                        [{if !$oDetailsProduct->isNotBuyable()}]
                            <input id="amountToBasket" type="text" name="am" value="1" size="3" autocomplete="off" class="textbox">
                            <button id="toBasket" type="submit" [{if !$blCanBuy}]disabled="disabled"[{/if}] class="submitButton largeButton">[{oxmultilang ident="DETAILS_ADDTOCART"}]</button>
                        [{/if}]
                    [{/oxhasrights}]
                [{/block}]
            </div>

            [{* additional info *}]
            <div class="additionalInfo clear">
                [{block name="details_productmain_priceperunit"}]
                    [{if $oDetailsProduct->getPricePerUnit()}]
                        <span id="productPriceUnit">[{$oDetailsProduct->getPricePerUnit()}] [{$currency->sign}]/[{$oDetailsProduct->getUnitName()}]</span>
                    [{/if}]
                [{/block}]

                [{block name="details_productmain_stockstatus"}]
                    [{if $oDetailsProduct->getStockStatus() == -1}]
                        <span class="stockFlag notOnStock">
                            [{if $oDetailsProduct->oxarticles__oxnostocktext->value}]
                                [{$oDetailsProduct->oxarticles__oxnostocktext->value}]
                            [{elseif $oViewConf->getStockOffDefaultMessage()}]
                                [{oxmultilang ident="DETAILS_NOTONSTOCK"}]
                            [{/if}]
                            [{if $oDetailsProduct->getDeliveryDate()}]
                                [{oxmultilang ident="DETAILS_AVAILABLEON"}] [{$oDetailsProduct->getDeliveryDate()}]
                            [{/if}]
                        </span>
                    [{elseif $oDetailsProduct->getStockStatus() == 1}]
                        <span class="stockFlag lowStock">
                            [{oxmultilang ident="DETAILS_LOWSTOCK"}]
                        </span>
                    [{elseif $oDetailsProduct->getStockStatus() == 0}]
                        <span class="stockFlag">
                            [{if $oDetailsProduct->oxarticles__oxstocktext->value}]
                                [{$oDetailsProduct->oxarticles__oxstocktext->value}]
                            [{elseif $oViewConf->getStockOnDefaultMessage()}]
                                [{oxmultilang ident="DETAILS_READYFORSHIPPING"}]
                            [{/if}]
                        </span>
                    [{/if}]
                [{/block}]

                [{block name="details_productmain_deliverytime"}]
                    [{oxhasrights ident="TOBASKET"}]
                        [{if $oDetailsProduct->isBuyable() }]
                            [{include file="page/details/inc/deliverytime.tpl"}]
                        [{/if}]
                    [{/oxhasrights}]
                [{/block}]

                [{block name="details_productmain_weight"}]
                    [{if $oDetailsProduct->oxarticles__oxweight->value}]
                        <span id="productWeight">[{oxmultilang ident="DETAILS_ARTWEIGHT"}] [{$oDetailsProduct->oxarticles__oxweight->value}] [{oxmultilang ident="DETAILS_ARTWEIGHTUNIT"}]</span>
                    [{/if}]
                [{/block}]

            </div>

            [{block name="details_productmain_social"}]
                <div class="social">
                    [{if ( $oView->isActive('FbShare') || $oView->isActive('FbLike') && $oViewConf->getFbAppId() ) }]
                        [{ if $oView->isActive('FacebookConfirm') && !$oView->isFbWidgetVisible()  }]
                            <div class="socialButton" id="productFbShare">
                                [{include file="widget/facebook/enable.tpl" source="widget/facebook/share.tpl" ident="#productFbShare"}]
                                [{include file=widget/facebook/like.tpl assign="fbfile"}]
                                [{assign var='fbfile' value=$fbfile|strip|escape:'url'}]
                                [{oxscript add="oxFacebook.buttons['#productFbLike']={html:'`$fbfile`',script:''};"}]
                            </div>
                            <div class="socialButton" id="productFbLike"></div>
                        [{else}]
                            <div class="socialButton" id="productFbShare">
                                [{include file="widget/facebook/enable.tpl" source="widget/facebook/share.tpl" ident="#productFbShare"}]
                            </div>
                            <div class="socialButton" id="productFbLike">
                                [{include file="widget/facebook/enable.tpl" source="widget/facebook/like.tpl" ident="#productFbLike"}]
                            </div>
                        [{/if}]
                    [{/if}]
                </div>
            [{/block}]
        </div>
    </div>
</div>

[{oxhasrights ident="TOBASKET"}]
    </form>

[{/oxhasrights}]
[{block name="details_productmain_morepics"}]
    [{include file="page/details/inc/morepics.tpl"}]
[{/block}]
<div>
  [{include file="page/details/inc/zoompopup.tpl"}]
</div>
