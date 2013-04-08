[{assign var="currency" value=$oView->getActCurrency()}]
<div [{if $test_Cntr}]id="test_cntr_[{$test_Cntr}]_[{$product->oxarticles__oxartnum->value}]"[{/if}] class="product hproduct[{if $head}] head[{/if}] [{$size|default:''}] [{$class|default:''}]">
    [{if $showMainLink}]
        [{assign var='_productLink' value=$product->getMainLink()}]
    [{else}]
        [{assign var='_productLink' value=$product->getLink()}]
    [{/if}]

    [{if $head}]
        <strong id="test_smallHeader[{if $testHeader}]_[{$testHeader}][{/if}]" class="h4 [{$size|default:''}]">
            [{if $head_link}]<a id="test_headerTitleLink_[{$testid}]" href="[{$head_link}]"[{if $oView->noIndex() }] rel="nofollow"[{/if}]>[{/if}]
            [{$head}]
            [{if $head_link}]</a>[{/if}]
            [{if $head_desc}] <small id="test_headerDesc_[{$testid}]">[{ "$head_desc"|strip_tags}]</small>[{/if}]
        </strong>
    [{/if}]

    <a id="test_pic_[{$testid}]" href="[{ $_productLink }]" class="picture url" rel="product[{if $oView->noIndex() }] nofollow[{/if}]">
      <img class="photo" src="[{if $size=='big'}][{$product->getPictureUrl(1) }][{elseif $size=='thinest'}][{$product->getIconUrl() }][{else}][{ $product->getThumbnailUrl() }][{/if}]" alt="[{ $product->oxarticles__oxtitle->value|strip_tags }] [{ $product->oxarticles__oxvarselect->value|default:'' }]">
    </a>

    <strong class="h3">
        <a id="test_title_[{$testid}]" class="fn" href="[{ $_productLink }]" rel="product[{if $oView->noIndex() }] nofollow[{/if}]">[{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]</a>
        <br>
        <tt class="identifier" id="test_no_[{$testid}]">
            [{if $product->getPricePerUnit()}]
                <div id="test_product_price_unit_[{$testid}]" class="pperunit">
                    [{$product->oxarticles__oxunitquantity->value}] [{$product->getUnitName()}] | [{$product->getPricePerUnit()}] [{ $currency->sign}]/[{$product->getUnitName()}]
                </div>
            [{elseif $product->oxarticles__oxweight->value  }]
                <span class="type" title="weight">[{ oxmultilang ident="INC_PRODUCTITEM_ARTWEIGHT" }]</span>
                <span class="value">[{ $product->oxarticles__oxweight->value }] [{ oxmultilang ident="INC_PRODUCTITEM_ARTWEIGHT2" }]</span>
            [{else}]
                <span class="type" title="sku">[{ oxmultilang ident="INC_PRODUCTITEM_ARTNOMBER2" }]</span>
                <span class="value">[{ $product->oxarticles__oxartnum->value }]</span>
            [{/if}]
        </tt>

        [{if $size=='thin' || $size=='thinest'}]
        <span class="flag [{if $product->getStockStatus() == -1}]red[{elseif $product->getStockStatus() == 1}]orange[{elseif $product->getStockStatus() == 0}]green[{/if}]">&nbsp;</span>
        [{/if}]
    </strong>

    [{if $recommid }]
      <div id="test_text_[{$testid}]" class="desc">[{ $product->text }]</div>
    [{/if}]
    [{oxhasrights ident="SHOWSHORTDESCRIPTION"}]
      [{if $size=='big' || $size=='thin'}]
        <div id="test_shortDesc_[{$testid}]" class="desc description">[{ $product->oxarticles__oxshortdesc->value }]</div>
      [{/if}]
    [{/oxhasrights}]

    <div [{if $test_Cntr}]id="test_cntr_[{$test_Cntr}]"[{/if}] class="actions">
        <a id="test_details_[{$testid}]" href="[{ $_productLink }]"[{if $oView->noIndex() }] rel="nofollow"[{/if}]>[{ oxmultilang ident="INC_PRODUCTITEM_MOREINFO2" }]</a>
        [{if $oViewConf->getShowCompareList()}]
            [{oxid_include_dynamic file="dyn/compare_links.tpl" testid="_`$testid`" type="compare" aid=$product->oxarticles__oxid->value anid=$altproduct in_list=$product->isOnComparisonList() page=$oView->getActPage() text_to_id="INC_PRODUCTITEM_COMPARE2" text_from_id="INC_PRODUCTITEM_REMOVEFROMCOMPARELIST2"}]
        [{/if}]
    </div>

    <form name="tobasket.[{$testid}]" action="[{ $oViewConf->getSelfActionLink() }]" method="post">

    [{capture name=product_price}]
    [{oxhasrights ident="SHOWARTICLEPRICE"}]
        <div id="test_price_[{$testid}]" class="cost">
            [{if $product->getFTPrice() > $product->getFPrice() && $size=='big' }]
                <b class="old">[{ oxmultilang ident="DETAILS_REDUCEDFROM" }] <del>[{ $product->getFTPrice()}] [{ $currency->sign}]</del></b>
                <span class="desc">[{ oxmultilang ident="DETAILS_REDUCEDTEXT" }]</span><br>
                <sub class="only">[{ oxmultilang ident="DETAILS_NOWONLY" }]</sub>
            [{/if}]
            [{if $product->getFPrice()}]
              <big class="price">[{ $product->getFPrice() }] [{ $currency->sign}]</big><sup class="dinfo"><a href="#delivery_link" rel="nofollow">[{ if !$product->isNotBuyable() && !$product->hasMdVariants() && !$blDisableToCart }]*[{/if}]</a></sup>
            [{else}]
              <big>&nbsp;</big>
            [{/if}]

        </div>
    [{/oxhasrights}]
    [{/capture}]

    [{if $size=='big'}][{$smarty.capture.product_price}][{/if}]

    <div class="variants">
    [{ $oViewConf->getHiddenSid() }]
    [{ $oViewConf->getNavFormParams() }]
    <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
    [{if $owishid}]
      <input type="hidden" name="owishid" value="[{$owishid}]">
    [{/if}]
    [{if $toBasketFunction}]
      <input type="hidden" name="fnc" value="[{$toBasketFunction}]">
    [{else}]
      <input type="hidden" name="fnc" value="tobasket">
    [{/if}]

    <input type="hidden" name="aid" value="[{ $product->oxarticles__oxid->value }]">
    [{if $altproduct}]
        <input type="hidden" name="anid" value="[{ $altproduct }]">
    [{else}]
        <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
    [{/if}]

    [{if $recommid}]
        <input type="hidden" name="recommid" value="[{ $recommid }]">
    [{/if}]
    <input type="hidden" name="pgNr" value="[{ $oView->getActPage() }]">

    [{if $size!='thin' && $size!='thinest'}]
    <input id="test_am_[{$testid}]" type="hidden" name="am" value="1">
    [{/if}]

    [{if $product->getVariantList() && ($size!='thinest') }]
        <label>[{ $product->oxarticles__oxvarname->value }]:</label>

        [{if $product->hasMdVariants() }]
            <select id="mdVariant_[{$testid}]" name="mdVariant_[{$testid}]">
            [{if !$product->isParentNotBuyable() && $product->getFPrice() }]
              <option value="[{$product->getLink()}]">[{ $product->oxarticles__oxvarselect->value }] [{oxhasrights ident="SHOWARTICLEPRICE"}] [{ $product->getFPrice() }] [{ $currency->sign|strip_tags}]* [{/oxhasrights}]</option>
            [{/if}]

            [{foreach from=$product->getMdSubvariants() item=mdVariant}]
              <option value="[{$mdVariant->getLink()}]?[{$oViewConf->getNavUrlParams()}]">[{ $mdVariant->getName() }] [{oxhasrights ident="SHOWARTICLEPRICE"}] [{if $mdVariant->getFPrice() }] [{ $mdVariant->getFPrice()|strip_tags }]* [{/if }] [{/oxhasrights}]</option>
            [{/foreach}]
            </select>
        [{else}]
            <select id="varSelect_[{$testid}]" name="aid">
            [{ if !$product->isParentNotBuyable() && $product->getFPrice() }]
                <option value="[{$product->getId()}]">[{ $product->oxarticles__oxvarselect->value }] [{oxhasrights ident="SHOWARTICLEPRICE"}] [{ $product->getFPrice() }] [{ $currency->sign|strip_tags}]* [{/oxhasrights}]</option>
            [{/if}]
            [{foreach from=$product->getVariantList() item=variant}]
                <option value="[{$variant->getId()}]">[{ $variant->oxarticles__oxvarselect->value }] [{oxhasrights ident="SHOWARTICLEPRICE"}] [{if $variant->getFPrice() }] [{ $variant->getFPrice() }] [{ $currency->sign|strip_tags}]* [{/if }] [{/oxhasrights}]</option>
            [{/foreach}]
            </select>
        [{/if}]
    [{elseif $product->getDispSelList()}]
        [{foreach key=iSel from=$product->getDispSelList() item=oList}]
        <label>[{ $oList.name }] :</label>
        <select id="selectList_[{$testid}]_[{$iSel}]" name="sel[[{$iSel}]]" onchange="oxid.sellist.set(this.name,this.value);">
          [{foreach key=iSelIdx from=$oList item=oSelItem}]
            [{ if $oSelItem->name }]
              <option value="[{$iSelIdx}]"[{if $oSelItem->selected }]SELECTED[{/if }]>[{ $oSelItem->name }]</option>
            [{/if}]
          [{/foreach}]
        </select>
        [{/foreach}]
    [{/if}]
    </div>

    [{if $size!='big'}] [{$smarty.capture.product_price}] [{/if}]

    [{oxhasrights ident="TOBASKET"}]
        [{ if !$product->isNotBuyable() && !$product->hasMdVariants() && !$blDisableToCart }]

        [{if $size=='thin' || $size=='thinest'}]
        <div class="amount">
            <label>[{ oxmultilang ident="DETAILS_QUANTITY" }]</label><input id="test_am_[{$testid}]" type="text" name="am" value="1" size="3">
        </div>
        [{/if}]
        <div class="tocart"><input id="test_toBasket_[{$testid}]" type="submit" value="[{if $size=='small'}][{oxmultilang ident="INC_PRODUCTITEM_ADDTOCARD3" }][{else}][{oxmultilang ident="INC_PRODUCTITEM_ADDTOCARD2"}][{/if}]" onclick="oxid.popup.load();"></div>
        [{/if}]
    [{/oxhasrights}]

    [{if $product->hasMdVariants() || $blDisableToCart }]
    <span class="btn moreinfo">
        <a id="test_variantMoreInfo_[{$testid}]" class="" href="[{ $_productLink }]" onclick="oxid.mdVariants.getMdVariantUrl('mdVariant_[{$testid}]'); return false;">[{ oxmultilang ident="INC_PRODUCT_VARIANTS_MOREINFO" }]</a>
    </span>
    [{/if}]

    </form>

    [{if $removeFunction && (($owishid && ($owishid==$oxcmp_user->oxuser__oxid->value)) || (($oView->getWishlistUserId()==$oxcmp_user->oxuser__oxid->value))) }]
    <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
          <input type="hidden" name="fnc" value="[{$removeFunction}]">
          <input type="hidden" name="aid" value="[{$product->oxarticles__oxid->value}]">
          <input type="hidden" name="am" value="0">
          <input type="hidden" name="itmid" value="[{$product->getItemKey()}]">
      </div>
      <div class="fromlist">
          <input id="test_remove_[{$testid}]" type="submit" value="[{ oxmultilang ident="INC_NOTICE_PRODUCT_ITEM_REMOVE" }]">
      </div>
    </form>
    [{/if}]



    [{if $removeFunction && $recommid }]
    <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
          <input type="hidden" name="fnc" value="[{$removeFunction}]">
          <input type="hidden" name="aid" value="[{$product->oxarticles__oxid->value}]">
          <input type="hidden" name="recommid" value="[{$recommid}]">
      </div>
      <div class="fromlist">
          <input id="test_remove_[{$testid}]" type="submit" value="[{ oxmultilang ident="INC_RECOMM_PRODUCT_ITEM_REMOVE" }]">
      </div>
    </form>
    [{/if}]

</div>
