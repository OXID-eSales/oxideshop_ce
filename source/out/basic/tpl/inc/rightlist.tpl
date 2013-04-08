[{assign var="currency" value=$oView->getActCurrency() }]
  [{foreach from=$list name=rightlist item=_product}]
  <div class="listitem rightlist[{if $smarty.foreach.rightlist.last}] lastinlist[{/if}]">
      [{ assign var="sRightListArtTitle" value="`$_product->oxarticles__oxtitle->value` `$_product->oxarticles__oxvarselect->value`" }]
      <a id="test_[{$test_Type}]_pic_[{$_product->oxarticles__oxid->value}]" href="[{$_product->getMainLink()}]" class="picture">
          <img src="[{$_product->getIconUrl()}]" alt="[{ $sRightListArtTitle|strip_tags }]">
      </a>
      <b><a id="test_[{$test_Type}]_Title_[{$_product->oxarticles__oxid->value}]" href="[{$_product->getMainLink()}]" class="arttitle">[{ $sRightListArtTitle|strip_tags }]</a></b>
      [{if $_product->oxarticles__oxweight->value }]
            <tt id="test_[{$test_Type}]_No_[{$_product->oxarticles__oxid->value}]">[{ oxmultilang ident="INC_RIGHTLIST_ARTWEIGHT" }] [{ $_product->oxarticles__oxweight->value }] [{ oxmultilang ident="INC_RIGHTLIST_ARTWEIGHT2" }]</tt>
      [{else}]
            <tt id="test_[{$test_Type}]_No_[{$_product->oxarticles__oxid->value}]">[{ oxmultilang ident="INC_RIGHTLIST_ARTNOMBER" }] [{ $_product->oxarticles__oxartnum->value }]</tt>
      [{/if}]

      <div class="actions">
          <a id="test_[{$test_Type}]_details_[{$_product->oxarticles__oxid->value}]" href="[{ $_product->getMainLink()}]" class="link">[{ oxmultilang ident="INC_PRODUCTITEM_MOREINFO" }]</a>
          [{ if $oViewConf->getShowCompareList()}]
              [{oxid_include_dynamic file="dyn/compare_links.tpl" testid="_`$test_Type`_`$_product->oxarticles__oxid->value`" type="compare" aid=$_product->oxarticles__oxid->value anid=$altproduct->oxarticles__oxnid->value in_list=$_product->isOnComparisonList() class="link" page=$oView->getActPage() text_from_id="INC_RIGHTLIST_REMOVEFROMCOMPARELIST" text_to_id="INC_RIGHTLIST_COMPARE"}]
          [{ /if }]
      </div>

      [{oxhasrights ident="SHOWARTICLEPRICE"}]

          <div class="price">
              [{if $_product->getFPrice()}]
                <span id="test_[{$test_Type}]_Price_[{$_product->oxarticles__oxid->value}]">[{ $_product->getFPrice() }] [{ $currency->sign}]</span><a href="#delivery_link" rel="nofollow">*</a>
              [{/if}]

              [{ if $_product->isBuyable()}]
                <div class="tocart">
                    <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
                      <div>
                          [{ $oViewConf->getHiddenSid() }]
                          [{ $oViewConf->getNavFormParams() }]
                          <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
                          <input type="hidden" name="fnc" value="tobasket">
                          <input type="hidden" name="aid" value="[{ $_product->oxarticles__oxid->value }]">
                          [{if $altproduct}]
                            <input type="hidden" name="anid" value="[{ $altproduct->oxarticles__oxnid->value }]">
                          [{else}]
                            <input type="hidden" name="anid" value="[{ $_product->oxarticles__oxnid->value }]">
                          [{/if}]
                          <input type="hidden" name="am" value="1">
                          [{oxhasrights ident="TOBASKET"}]
                          <span class="btn"><input id="test_[{$test_Type}]_toBasket_[{$_product->oxarticles__oxid->value}]" type="submit" class="btn" onclick="oxid.popup.load();" value=""></span>
                          [{/oxhasrights}]
                      </div>
                    </form>
                </div>
              [{/if}]
         </div>

      [{/oxhasrights}]
   </div>
  [{/foreach}]
