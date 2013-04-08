[{if $oView->getSearchTitle() }]
  [{ assign var="template_location" value=$oView->getSearchTitle()}]
[{else}]
  [{ assign var="template_location" value=""}]
  [{ assign var="blSep" value=""}]
  [{foreach from=$oView->getCatTreePath() item=oCatPath}]
    [{ if $blSep == "y"}]
      [{ assign var="template_location" value=$template_location|cat:" / "}]
    [{/if}]
    [{ assign var="template_location" value=$template_location|cat:"<a href=\""|cat:$oCatPath->getLink()|cat:"\">"|cat:$oCatPath->oxcategories__oxtitle->value|cat:"</a>"}]
    [{ assign var="blSep" value="y"}]
  [{/foreach}]
[{/if}]


[{include file="_header.tpl" location=$template_location }]

<!-- article locator -->
[{include file="inc/details_locator.tpl" where="Top" actCategory=$oView->getActiveCategory()}]

<!-- ox_mod01 details -->
[{assign var="currency" value=$oView->getActCurrency() }]
[{assign var="product" value=$oView->getProduct() }]

<div class="hreview-aggregate">
<div class="product item hproduct details head big">

    <strong id="test_detailsHeader" class="h4 big">[{oxmultilang ident="DETAILS_PRODUCTDETAILS"}]</strong>

    <h1 id="test_product_name" class="fn">[{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]</h1>
    <tt id="test_product_artnum" class="identifier">
        <span class="type" title="sku">[{ oxmultilang ident="INC_PRODUCTITEM_ARTNOMBER2" }]</span>
        <span class="value">[{ $product->oxarticles__oxartnum->value }]</span>
    </tt>

    <div class="picture">
      <img src="[{ $oView->getActPicture() }]" id="product_img" class="photo" alt="[{ $product->oxarticles__oxtitle->value|strip_tags }] [{ $product->oxarticles__oxvarselect->value|default:'' }]">
    </div>

    <div class="exturls">
    [{if $oView->showZoomPics() }]
        [{assign var="aZoomPics" value=$oView->getZoomPics() }]
        [{assign var="iZoomPic" value=$oView->getActZoomPic() }]
        [{assign var="sZoomPopup" value="inc/popup_zoom.tpl" }]
        <a id="test_zoom" rel="nofollow" href="[{$product->getMoreDetailLink()}]" onmouseover="" onclick="oxid.popup.zoom();oxid.image('zoomImg','[{$aZoomPics[$iZoomPic].file}]');return false;"><b>[{ oxmultilang ident="DETAILS_ZOOM" }]</b></a>
    [{/if}]

    [{if $product->oxarticles__oxfile->value}]
        <a id="productFile" href="[{$product->getFileUrl()}][{ $product->oxarticles__oxfile->value }]"><b>[>] [{ $product->oxarticles__oxfile->value }]</b></a>
        [{oxscript add="oxid.blank('productFile');"}]
    [{/if}]

    [{if $product->oxarticles__oxexturl->value}]
        <a id="product_exturl" class="details" href="http://[{ $product->oxarticles__oxexturl->value }]"><b>[>]
        [{if $product->oxarticles__oxurldesc->value }]
            [{$product->oxarticles__oxurldesc->value }]
        [{else}]
            [{$product->oxarticles__oxexturl->value }]
        [{/if}]
        </b></a>
        [{oxscript add="oxid.blank('product_exturl');"}]
    [{/if}]

    </div>


    [{oxhasrights ident="SHOWSHORTDESCRIPTION"}]
        <div id="test_product_shortdesc" class="desc description">[{ $product->oxarticles__oxshortdesc->value }]</div>
    [{/oxhasrights}]


[{if $oView->ratingIsActive()}]
    [{ if !$oxcmp_user}]
      [{assign var="_star_title" value="DETAILS_LOGGIN"|oxmultilangassign }]
    [{ elseif !$oView->canRate() }]
      [{assign var="_star_title" value="DETAILS_ALREADYRATED"|oxmultilangassign }]
    [{ else }]
      [{assign var="_star_title" value="DETAILS_RATETHISARTICLE"|oxmultilangassign }]
    [{/if}]
    [{math equation="x*y" x=20 y=$product->getArticleRatingAverage() assign="currentRate" }]
    <br>

    <ul id="star_rate_top" class="rating">
      <li class="current_rate" style="width: [{$currentRate}]%;"><a title="[{$_star_title}]"><b class="average">[{$product->oxarticles__oxrating->value}]</b></a></li>
      [{section name=star start=1 loop=6}]
      <li class="s[{$smarty.section.star.index}]"><a rel="nofollow" [{ if !$oxcmp_user}]href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$product->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]"[{ elseif $oView->canRate() }]href="#review" onclick="oxid.review.rate([{$smarty.section.star.index}]);"[{/if}] title="[{$_star_title}]"><b>[{$smarty.section.star.index}]</b></a></li>
      [{/section}]
    </ul>

    [{if $product->oxarticles__oxratingcnt->value}]
      <a id="star_rating_text" rel="nofollow" href="#review" onclick="oxid.review.show();" class="fs10 link2"><span class="count">[{$product->oxarticles__oxratingcnt->value}]</span> [{if $product->oxarticles__oxratingcnt->value == 1}][{ oxmultilang ident="DETAILS_RATINGREZULT" }][{else}][{ oxmultilang ident="DETAILS_RATINGREZULTS" }] [{/if}]</a>
    [{else}]
      <a id="star_rating_text" rel="nofollow" href="#review" onclick="oxid.review.show();" class="fs10 link2">[{ oxmultilang ident="DETAILS_NORATINGS" }]</a>
    [{/if}]
[{/if}]

    <div class="cats">
        [{ assign var="oManufacturer" value=$oView->getManufacturer()}]
        [{if ($oManufacturer && $oView->getListType()!='manufacturer') }]
          [{if $oManufacturer->oxmanufacturers__oxicon->value}]
              <img src="[{$oManufacturer->getIconUrl()}]" alt="[{ $oManufacturer->oxmanufacturers__oxtitle->value}]">
          [{/if}]
          <b>[{ oxmultilang ident="DETAILS_MANUFACTURER" }]</b>
          [{if !$oManufacturer->isReadOnly()}]
              <a id="test_manufacturer_[{$oManufacturer->oxmanufacturers__oxid->value}]" class="brand" href="[{ $oManufacturer->getLink() }]">[{ $oManufacturer->oxmanufacturers__oxtitle->value}]</a>
          [{else}]
              <span class="brand">[{ $oManufacturer->oxmanufacturers__oxtitle->value}]</span>
          [{/if}]
          <br>
        [{else}]
          [{ assign var="oVendor" value=$oView->getVendor()}]
          [{if ($oVendor && $oView->getListType()!='vendor') }]
            [{if $oVendor->oxvendor__oxicon->value}]
                <img src="[{$oVendor->getIconUrl()}]" alt="[{ $oVendor->oxvendor__oxtitle->value}]">
            [{/if}]
            <b>[{ oxmultilang ident="DETAILS_VENDOR" }]</b>
            [{if !$oVendor->isReadOnly()}]
                <a id="test_vendor_[{$oVendor->oxvendor__oxid->value}]" href="[{ $oVendor->getLink() }]">[{ $oVendor->oxvendor__oxtitle->value}]</a>
            [{else}]
                [{ $oVendor->oxvendor__oxtitle->value}]
            [{/if}]
            <br>
          [{/if}]
        [{/if}]
        [{ assign var="oCategory" value=$oView->getCategory()}]
        [{if $oCategory && $oView->getListType()!='list'}]
            <b>[{ oxmultilang ident="DETAILS_CATEGORY" }]</b>
            <a id="test_category_[{$oCategory->oxcategories__oxid->value }]" class="category" href="[{ $oCategory->getLink() }]">[{ $oCategory->oxcategories__oxtitle->value }]</a>
        [{/if}]
    </div>

    <div class="status">

      [{if $product->getStockStatus() == -1}]
      <div class="flag red"></div>
        [{ if $product->oxarticles__oxnostocktext->value  }]
            [{ $product->oxarticles__oxnostocktext->value  }]
        [{elseif $oViewConf->getStockOffDefaultMessage() }]
            [{ oxmultilang ident="DETAILS_NOTONSTOCK" }]
        [{/if}]

        [{ if $product->getDeliveryDate() }]
          <br>[{ oxmultilang ident="DETAILS_AVAILABLEON" }] [{ $product->getDeliveryDate() }]
        [{/if}]

      [{elseif $product->getStockStatus() == 1}]

      <div class="flag orange"></div>
      <b>[{ oxmultilang ident="DETAILS_LOWSTOCK" }]</b>

      [{elseif $product->getStockStatus() == 0}]

      <div class="flag green"></div>

      [{ if $product->oxarticles__oxstocktext->value  }]
        [{ $product->oxarticles__oxstocktext->value  }]
      [{elseif $oViewConf->getStockOnDefaultMessage() }]
        [{ oxmultilang ident="DETAILS_READYFORSHIPPING" }]
      [{/if}]

      [{/if}]

    </div>


    <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">

    <div>
    [{ $oViewConf->getHiddenSid() }]
    [{ $oViewConf->getNavFormParams() }]
    <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
    <input type="hidden" name="fnc" value="tobasket">
    <input type="hidden" name="aid" value="[{ $product->oxarticles__oxid->value }]">
    <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
    </div>

    [{if $oView->getSelectLists() }]
    [{foreach key=iSel from=$oView->getSelectLists() item=oList}]
     <div class="variants">
      <label>[{ $oList.name }]:</label>
        <select id="test_select_[{$product->oxarticles__oxid->value}]_[{$iSel}]" name="sel[[{$iSel}]]" onchange="oxid.sellist.set(this.name,this.value);">
          [{foreach key=iSelIdx from=$oList item=oSelItem}]
            [{ if $oSelItem->name }]<option value="[{$iSelIdx}]">[{ $oSelItem->name }]</option>[{/if}]
          [{/foreach}]
        </select>
    </div>
    [{/foreach}]
    [{/if}]

    [{oxhasrights ident="SHOWARTICLEPRICE"}]
        <div class="cost">
            [{if $product->getFTPrice() > $product->getFPrice()}]
                <b class="old">[{ oxmultilang ident="DETAILS_REDUCEDFROM" }] <del>[{ $product->getFTPrice()}] [{ $currency->sign}]</del></b>
                <span class="desc">[{ oxmultilang ident="DETAILS_REDUCEDTEXT" }]</span><br>
                <sub class="only">[{ oxmultilang ident="DETAILS_NOWONLY" }]</sub>
            [{/if}]
            [{if $product->getFPrice() }]
                <big class="price pricerange" id="test_product_price">[{ $product->getFPrice() }] [{ $currency->sign}]</big>
            [{/if}]
            [{oxifcontent ident="oxdeliveryinfo" object="oCont"}]
            <sup class="dinfo">
                [{assign var="_oPrice" value=$product->getPrice()}]
                [{if $_oPrice && $_oPrice->getVat() > 0 }]
                [{ oxmultilang ident="DETAILS_PLUSSHIPPING" }]
                [{else}]
                [{ oxmultilang ident="DETAILS_PLUSSHIPPING_PLUS" }]
                [{/if}]
                <a href="[{ $oCont->getLink() }]" rel="nofollow">[{ oxmultilang ident="DETAILS_PLUSSHIPPING2" }]</a></sup>
            [{/oxifcontent}]
        </div>
    [{/oxhasrights}]

    [{if $product->isBuyable() }]
        [{include file="inc/del_time.tpl"}]
    [{/if}]

    [{if $product->oxarticles__oxweight->value }]
    <div id="productWeight" class="pperunit">
        ([{ oxmultilang ident="DETAILS_ARTWEIGHT" }] [{$product->oxarticles__oxweight->value}] [{ oxmultilang ident="DETAILS_ARTWEIGHTUNIT" }])
    </div>
    [{/if}]

    [{if $product->getPricePerUnit()}]
    <div id="test_product_price_unit" class="pperunit">
        ([{$product->getPricePerUnit()}] [{ $currency->sign}]/[{$product->getUnitName()}])
    </div>
    [{/if}]


    [{oxhasrights ident="SHOWARTICLEPRICE"}]
     [{if $product->loadAmountPriceInfo()}]
       <table class="amprice">
         <tr>
            <th colspan="2">[{ oxmultilang ident="DETAILS_MOREYOUBUYMOREYOUSAVE" }]</th>
         </tr>
         [{foreach from=$product->loadAmountPriceInfo() item=priceItem}]
           <tr>
             <td class="am">[{ oxmultilang ident="DETAILS_FROM" }] [{$priceItem->oxprice2article__oxamount->value}] [{ oxmultilang ident="DETAILS_PCS" }]</td>
             <td id="test_amprice_[{$priceItem->oxprice2article__oxamount->value}]_[{$priceItem->oxprice2article__oxamountto->value}]" class="pr">
               [{if $priceItem->oxprice2article__oxaddperc->value}]
                 - [{$priceItem->oxprice2article__oxaddperc->value}] [{ oxmultilang ident="DETAILS_DISCOUNT" }]
               [{else}]
                 - [{$priceItem->fbrutprice}] [{ $currency->sign}]
               [{/if}]
             </td>
           </tr>
         [{/foreach}]
       </table>
    [{/if}]
    [{/oxhasrights}]

    [{if $size!='big'}] [{$smarty.capture.product_price}] [{/if}]

    [{oxhasrights ident="TOBASKET"}]
        [{ if $product->isBuyable() }]
            <div class="amount">
                <label>[{ oxmultilang ident="DETAILS_QUANTITY" }]</label><input id="test_AmountToBasket" type="text" name="am" value="1" size="3">
            </div>
            <div class="tocart"><input id="test_toBasket" type="submit" value="[{if $size=='small'}][{oxmultilang ident="INC_PRODUCTITEM_ADDTOCARD3" }][{else}][{oxmultilang ident="INC_PRODUCTITEM_ADDTOCARD2"}][{/if}]" onclick="oxid.popup.load();"></div>
            [{if $oView->isPriceAlarm()}]
            <div class="pricealarm">
                <a id="test_PriceAlarmLink" rel="nofollow" href="#preisalarm_link">[{ oxmultilang ident="DETAILS_PRICEALARM" }]</a>
            </div>
            [{/if}]
            [{if $oView->isPersParam()}]
            <div class="persparam">
                <label>[{ oxmultilang ident="DETAILS_LABEL" }]</label><input type="text" name="persparam[details]" value="[{ $product->aPersistParam.text }]" size="35">
            </div>
            [{/if}]
        [{else}]
            [{if $oView->isPriceAlarm() && !$product->isParentNotBuyable()}]
            <div class="pricealarm">
                <a rel="nofollow" href="#preisalarm_link">[{ oxmultilang ident="DETAILS_PRICEALARM2" }]</a>
            </div>
            [{/if}]
        [{/if}]
    [{/oxhasrights}]
    </form>

    <div class="actions">
        [{if $oViewConf->getShowCompareList() }]
            [{oxid_include_dynamic file="dyn/compare_links.tpl" testid="" type="compare" aid=$product->oxarticles__oxid->value anid=$product->oxarticles__oxnid->value in_list=$product->isOnComparisonList() page=$oView->getActPage() text_to_id="DETAILS_COMPARE" text_from_id="DETAILS_REMOVEFROMCOMPARELIST"}]
        [{/if}]

        <a id="test_suggest" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=suggest" params="anid=`$product->oxarticles__oxnid->value`"|cat:$oViewConf->getNavUrlParams() }]">[{ oxmultilang ident="DETAILS_RECOMMEND" }]</a>

        [{if $oViewConf->getShowListmania()}]
            [{ if $oxcmp_user }]
                <a id="test_Recommlist" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=recommadd" params="aid=`$product->oxarticles__oxnid->value`&amp;anid=`$product->oxarticles__oxnid->value`"|cat:$oViewConf->getNavUrlParams() }]" class="details">[{ oxmultilang ident="DETAILS_ADDTORECOMMLIST" }]</a>
            [{ else}]
                <a id="test_LoginToRecommlist" class="reqlogin" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$product->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]">[{ oxmultilang ident="DETAILS_LOGGINTOACCESSRECOMMLIST" }]</a>
            [{ /if}]
        [{ /if}]

        [{if $oxcmp_user }]
            <a id="linkToNoticeList" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$product->oxarticles__oxnid->value`&amp;anid=`$product->oxarticles__oxnid->value`&amp;fnc=tonoticelist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="DETAILS_ADDTONOTICELIST" }]</a>
        [{else}]
            <a id="test_LoginToNotice" class="reqlogin" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$product->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="DETAILS_LOGGINTOACCESSNOTICELIST" }]</a>
        [{/if}]

        [{if $oViewConf->getShowWishlist()}]
            [{if $oxcmp_user }]
                <a id="linkToWishList" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl="|cat:$oViewConf->getActiveClassName() params="aid=`$product->oxarticles__oxnid->value`&anid=`$product->oxarticles__oxnid->value`&amp;fnc=towishlist&amp;am=1"|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="DETAILS_ADDTOWISHLIST" }]</a>
            [{else}]
                <a id="test_LoginToWish" class="reqlogin" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$product->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]" rel="nofollow">[{ oxmultilang ident="DETAILS_LOGGINTOACCESSWISHLIST" }]</a>
            [{/if}]
        [{/if}]
    </div>

    [{include file="inc/bookmarks.tpl"}]


     [{if ( $oView->isActive('FbShare') || $oView->isActive('FbLike') && $oViewConf->getFbAppId() ) }]
                        [{ if $oView->isActive('FacebookConfirm') && !$oView->isFbWidgetVisible()  }]
                            <div class="socialButton" id="productFbShare">
                                [{include file="inc/facebook/fb_enable.tpl" source="inc/facebook/fb_share.tpl" ident="#productFbShare"}]
                                [{include file=inc/facebook/fb_like.tpl assign="fbfile"}]
                                [{assign var='fbfile' value=$fbfile|strip|escape:'url'}]
                                [{oxscript add="oxFacebook.buttons['#productFbLike']={html:'`$fbfile`',script:''};"}]
                            </div>
                            <div class="socialButton" id="productFbLike"></div>
                        [{else}]
                            <div class="socialButton" id="productFbShare">
                                [{include file="inc/facebook/fb_enable.tpl" source="inc/facebook/fb_share.tpl" ident="#productFbShare"}]
                            </div>
                            <div class="socialButton" id="productFbLike">
                                [{include file="inc/facebook/fb_enable.tpl" source="inc/facebook/fb_like.tpl" ident="#productFbLike"}]
                            </div>
                        [{/if}]
                    [{/if}]





</div>
</div>

<div class="product moredetails">
  [{if $oView->morePics() }]
    <div class="morepics">
    [{foreach from=$oView->getIcons() key=picnr item=ArtIcon name=MorePics}]
        <a id="test_MorePics_[{$smarty.foreach.MorePics.iteration}]" rel="nofollow" href="[{ $product->getLink()|oxaddparams:"actpicid=`$picnr`" }]" onclick="oxid.image('product_img','[{$product->getPictureUrl($picnr)}]');return false;"><img src="[{$product->getIconUrl($picnr)}]" alt=""></a>
    [{/foreach}]
    </div>
    [{/if}]

    <div class="longdesc">
        <strong class="h3" id="test_productFullTitle">[{ $product->oxarticles__oxtitle->value }][{if $product->oxarticles__oxvarselect->value}] [{ $product->oxarticles__oxvarselect->value }][{/if}]</strong>
        [{oxhasrights ident="SHOWLONGDESCRIPTION"}]
            <div id="test_product_longdesc">[{oxeval var=$product->getLongDescription()}]</div>
        [{/oxhasrights}]

        <div class="question">
            [{oxmailto extra='id="test_QuestionMail"' address=$product->oxarticles__oxquestionemail->value|default:$oxcmp_shop->oxshops__oxinfoemail->value subject='DETAILS_QUESTIONSSUBJECT'|oxmultilangassign|cat:" "|cat:$product->oxarticles__oxartnum->value text='DETAILS_QUESTIONS'|oxmultilangassign encode="javascript"}]
        </div>
    </div>

</div>


[{ if $oView->getAttributes() }]
<strong id="test_specsHeader" class="boxhead">[{ oxmultilang ident="DETAILS_SPECIFICATION" }]</strong>
<div class="box">
    <table width="100%" class="attributes">
      <colgroup><col width="50%" span="2"></colgroup>
      [{foreach from=$oView->getAttributes() item=oAttr name=attribute}]
          <tr [{if $smarty.foreach.attribute.last}]class="last"[{/if}]>
            <td id="test_attrTitle_[{$smarty.foreach.attribute.iteration}]"><b>[{ $oAttr->title }]</b></td>
            <td id="test_attrValue_[{$smarty.foreach.attribute.iteration}]">[{ $oAttr->value }]</td>
          </tr>
      [{/foreach}]
    </table>
</div>
[{/if}]

[{include file="inc/facebook/fb_comments.tpl"}]

[{include file="inc/facebook/fb_invite.tpl"}]

    [{if $oView->isActive('FbComments') && $oViewConf->getFbAppId()}]
        [{assign var='_fbScript' value="http://connect.facebook.net/en_US/all.js#appId="|cat:$oViewConf->getFbAppId()|cat:"&amp;xfbml=1"}]
        <strong id="test_facebookCommentsHead" class="boxhead">[{ oxmultilang ident="FACEBOOK_COMMENTS" }]</strong>
        <div class="box" id="productFbComments">
            [{include file="inc/facebook/fb_enable.tpl" source="inc/facebook/fb_comments.tpl" ident="#productFbComments" script=$_fbScript type="text"}]
        </div>
    [{/if}]

    [{if $oView->isActive('FbInvite') && $oViewConf->getFbAppId()}]
        <strong id="test_facebookInviteHead" class="boxhead">[{ oxmultilang ident="FACEBOOK_INVITE" }]</strong>
        <div class="box" id="productFbInvite">
            <fb:serverfbml width="560px" id="productFbInviteFbml">
                    [{include file="inc/facebook/fb_enable.tpl" source="inc/facebook/fb_invite.tpl" ident="#productFbInviteFbml" type="text"}]
            </fb:serverfbml>
        </div>
    [{/if}]

[{include file="inc/media.tpl"}]

[{include file="inc/tags.tpl"}]

[{if $oView->isPriceAlarm() && !$product->isParentNotBuyable()}]
<strong id="preisalarm_link" class="boxhead">[{ oxmultilang ident="DETAILS_PRICEALARM3" }]</strong>
<div class="box">
    <p>[{ oxmultilang ident="DETAILS_PRICEALARMMESSAGE" }]</p>
    <form name="pricealarm" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="cl" value="pricealarm">
        <input type="hidden" name="fnc" value="addme">
        <input type="hidden" name="pa[aid]" value="[{ $product->oxarticles__oxid->value }]">
        [{assign var="oCaptcha" value=$oView->getCaptcha() }]
        <input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]"/>
    </div>

    <table class="pricealarm" width="100%" summary="[{ oxmultilang ident="DETAILS_PRICEALARM3" }]">
        <colgroup>
            <col width="20%">
            <col width="10%">
            <col width="22%" span="2">
            <col width="6%">
            <col width="20%">
        </colgroup>
        <tr>
          <th colspan="2"><label class="nobold">[{ oxmultilang ident="CONTACT_VERIFICATIONCODE" }]</label></th>
          <th><label>[{ oxmultilang ident="DETAILS_EMAIL" }]</label></th>
          <th colspan="3"><label class="hl">[{ oxmultilang ident="DETAILS_YOURPRICE" }]</label></th>
        </tr>
        <tr>
            <td>
             [{if $oCaptcha->isImageVisible()}]
               <img src="[{$oCaptcha->getImageUrl()}]" alt="[{ oxmultilang ident="CONTACT_VERIFICATIONCODE" }]" width="80" height="18">
             [{else}]
               <div class="verification_code">[{$oCaptcha->getText()}]</div>
             [{/if}]
            </td>
            <td><input type="text" name="c_mac" value="" size="5"></td>
            <td><input type="text" name="pa[email]" value="[{ if $oxcmp_user }][{ $oxcmp_user->oxuser__oxusername->value }][{/if}]" size="20" maxlength="128"></td>
            <td><input type="text" name="pa[price]" value="[{oxhasrights ident="SHOWARTICLEPRICE"}][{ if $product }][{ $product->getFPrice() }][{/if}][{/oxhasrights}]" size="20" maxlength="32"></td>
            <td><b class="hl">[{ $currency->sign}]</b></td>
            <td>
                <span class="btn">
                    <input id="test_PriceAlarmSubmit" type="submit" name="submit" value="[{ oxmultilang ident="DETAILS_SEND" }]" class="btn">
                </span>
            </td>
        </tr>
      </table>

      </form>
</div>
[{/if}]

[{if $oView->getVariantList() || $oView->drawParentUrl()}]

    <strong id="test_variantHeader" class="boxhead">
        [{if $oView->drawParentUrl()}]
            <a id="test_backToParent" href="[{$oView->getParentUrl()}]">[{oxmultilang ident="INC_PRODUCT_VARIANTS_BACKTOMAINPRODUCT"|oxmultilangassign|cat:" "|cat:$oView->getParentName() }]</a>
        [{else}]
            [{oxmultilang ident="INC_PRODUCT_VARIANTS_VARIANTSELECTIONOF"|oxmultilangassign|cat:" `$product->oxarticles__oxtitle->value`" }]
        [{/if}]
    </strong>
    <div class="box variantslist">

    [{ if $oView->drawParentUrl() && count( $oView->getVariantList() ) }]
      <b id="test_variantHeader1">[{ oxmultilang ident="INC_PRODUCT_VARIANTS_OTHERVARIANTSOF" }] [{ $oView->getParentName() }]</b>
      <br>
      <div class="txtseparator inbox"></div>
    [{/if}]

    [{include file="inc/variant_selector.tpl"}]

    [{if $oView->isMdVariantView()}]
      <noscript>
    [{/if}]

    [{foreach from=$oView->getVariantListExceptCurrent() name=variants item=variant_product}]
        [{if $smarty.foreach.variants.first}]
          [{assign var="details_variants_class" value="firstinlist"}]
        [{elseif $smarty.foreach.variants.last}]
          [{assign var="details_variants_class" value="lastinlist"}]
          <div class="separator inbox"></div>
        [{else}]
          [{assign var="details_variants_class" value="inlist"}]
          <div class="separator inbox"></div>
        [{/if}]

        [{$variants_head}]
        [{include file="inc/product.tpl" product=$variant_product size="thinest" altproduct=$product->getId() class=$details_variants_class testid="Variant_"|cat:$variant_product->oxarticles__oxid->value}]
        [{assign var="details_variants_head" value=""}]
    [{/foreach}]

    [{if $oView->isMdVariantView()}]
      </noscript>
    [{/if}]

    </div>

[{/if}]

[{if $oView->isReviewActive() }]
<strong id="test_reviewHeader" class="boxhead">[{ oxmultilang ident="DETAILS_PRODUCTREVIEW" }]</strong>
<div id="review" class="box info">
  [{ if $oxcmp_user }]
    <form action="[{ $oViewConf->getSelfActionLink() }]" method="post" id="rating">
        <div id="write_review">
            [{ if $oView->canRate() }]
            <input type="hidden" name="artrating" value="0">
            <ul id="star_rate" class="rating">
                <li id="current_rate" class="current_rate" style="width: 0px;"><a title="[{$_star_title}]"><b>1</b></a></li>
                [{section name=star start=1 loop=6}]
                <li class="s[{$smarty.section.star.index}]"><a rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=review" params="anid=`$product->oxarticles__oxnid->value`&amp;"|cat:$oViewConf->getNavUrlParams() }]" onclick="oxid.review.rate([{$smarty.section.star.index}]);return false;" title="[{$smarty.section.star.index}] [{if $smarty.section.star.index==1}][{ oxmultilang ident="DETAILS_STAR" }][{else}][{ oxmultilang ident="DETAILS_STARS" }][{/if}]"><b>[{$smarty.section.star.index}]</b></a></li>
                [{/section}]
            </ul>
            [{/if}]
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            [{oxid_include_dynamic file="dyn/formparams.tpl" }]
            <input type="hidden" name="fnc" value="savereview">
            <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
            <input type="hidden" name="anid" value="[{ $product->oxarticles__oxid->value }]">
            <textarea cols="102" rows="15" name="rvw_txt" class="fullsize"></textarea><br>
            <span class="btn"><input id="test_reviewSave" type="submit" value="[{ oxmultilang ident="DETAILS_SAVEREVIEW" }]" class="btn"></span>
        </div>
    </form>
    <a id="write_new_review" rel="nofollow" class="fs10" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=review" params="anid=`$product->oxarticles__oxnid->value`&amp;"|cat:$oViewConf->getNavUrlParams() }]" onclick="oxid.review.show();return false;"><b>[{ oxmultilang ident="DETAILS_WRITEREVIEW" }]</b></a>
  [{else}]
    <a id="test_Reviews_login" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$product->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]" class="fs10"><b>[{ oxmultilang ident="DETAILS_LOGGINTOWRITEREVIEW" }]</b></a>
  [{/if}]

  [{if $oView->getReviews() }]
   [{foreach from=$oView->getReviews() item=review name=ReviewsCounter}]

    <dl class="review hreview">
        <dt>
            <span class="left"><b id="test_ReviewName_[{$smarty.foreach.ReviewsCounter.iteration}]" class="reviewer">[{ $review->oxuser__oxfname->value }]</b> [{ oxmultilang ident="DETAILS_WRITES" }]</span>
            <span class="right param"><b id="test_ReviewTime_[{$smarty.foreach.ReviewsCounter.iteration}]">[{ oxmultilang ident="DETAILS_TIME" }]</b>&nbsp;[{ $review->oxreviews__oxcreate->value|date_format:"%H:%M" }]</span>
            <span class="right param"><b id="test_ReviewDate_[{$smarty.foreach.ReviewsCounter.iteration}]">[{ oxmultilang ident="DETAILS_DATE" }]</b>&nbsp;[{ $review->oxreviews__oxcreate->value|date_format:"%d.%m.%Y" }]</span>
            <span class="right param">[{if $review->oxreviews__oxrating->value }]<b id="test_ReviewRating_[{$smarty.foreach.ReviewsCounter.iteration}]">[{ oxmultilang ident="DETAILS_RATING" }]</b>&nbsp;<span class="rating">[{ $review->oxreviews__oxrating->value }]</span>[{/if}]</span>
        </dt>
        <dd class="item">
            <span class="dtreviewed">[{ $review->oxreviews__oxcreate->value|date_format:"%Y-%m-%d %H:%M:%S" }]</span>
            <span class="fn">[{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]</span>
            [{if $product->getFPrice() }]
            <span class="pricerange">[{ $product->getFPrice() }] [{ $currency->sign}]</span>
            [{/if}]
        </dd>
        <dd id="test_ReviewText_[{$smarty.foreach.ReviewsCounter.iteration}]" class="summary">
            [{ $review->oxreviews__oxtext->value }]
        </dd>

    </dl>

   [{/foreach}]
  [{else}]
    <div class="dot_sep mid"></div>
    [{ oxmultilang ident="DETAILS_REVIEWNOTAVAILABLE" }]
  [{/if}]
</div>
[{/if}]


[{ include file="inc/product.tpl" product=$product size="thin" head="DETAILS_CURRENTPRODUCT"|oxmultilangassign testid="current"}]



[{oxid_include_dynamic file="dyn/last_seen_products.tpl" type="lastproducts" aid=$product->oxarticles__oxid->value aparentid=$product->oxarticles__oxparentid->value testid="LastSeen" }]

<!-- article locator -->
[{include file="inc/details_locator.tpl" where="Bottom"}]

[{ insert name="oxid_tracker" title="DETAILS_TITLE"|oxmultilangassign product=$product cpath=$oView->getCatTreePath() }]
[{include file="_footer.tpl" popup=$sZoomPopup }]
