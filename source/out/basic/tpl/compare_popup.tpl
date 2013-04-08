[{assign var="template_title" value="COMPARE_POPUP_TITLE"|oxmultilangassign}]
[{assign var="currency" value=$oView->getActCurrency()}]
[{include file="_header_plain.tpl" title=$template_title location=$template_title}]

   <div class="box compare">

      [{if $oView->getCompArtList()}]
          [{assign var="showFirstCol" value=$oView->getAttributeList()|@count}]

      <table class="cmp_tbl">

        [{assign var="isFirst" value=$showFirstCol}]

        <tr>
          [{foreach key=iProdNr from=$oView->getCompArtList() item=product name=comparelist}]
            [{if $isFirst}]
              <th class="no_left_brd" valign="bottom">[{ oxmultilang ident="COMPARE_PRODUCTATTRIBUTES" }]</th>
              [{assign var="isFirst" value=false}]
            [{/if}]
            <td valign="top">

            [{assign var="testid" value="cmp_`$smarty.foreach.comparelist.iteration`"}]
            <div class="product small">
                <a id="test_pic_[{$testid}]" class="picture">
                  <img src="[{ $product->getThumbnailUrl() }]" alt="[{ $product->oxarticles__oxtitle->value|strip_tags }] [{ $product->oxarticles__oxvarselect->value|default:'' }]">
                </a>

                <strong class="h3">
                    [{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]
                    <tt id="test_no_[{$testid}]">[{ oxmultilang ident="INC_PRODUCTITEM_ARTNOMBER2" }] [{ $product->oxarticles__oxartnum->value }]</tt>
                </strong>

                <form name="tobasket.[{$testid}]" action="[{ $oViewConf->getSelfActionLink() }]" method="post">

                [{capture name=product_price}]
                [{oxhasrights ident="SHOWARTICLEPRICE"}]
                    <div id="test_price_[{$testid}]" class="cost">
                        [{if $product->getFPrice()}]
                          <big class="price">[{ $product->getFPrice() }] [{ $currency->sign}]</big><sup class="dinfo"><a href="#delivery_link" rel="nofollow">*</a></sup>
                        [{else}]
                          <big>&nbsp;</big>
                        [{/if}]
                    </div>
                [{/oxhasrights}]
                [{/capture}]
                </form>
              </div>

            </td>
          [{/foreach}]
        </tr>

      [{foreach key=sAttrID from=$oView->getAttributeList() item=oAttrib name=CmpAttr}]
        <tr>
        [{assign var="isFirst" value=$showFirstCol}]
          [{foreach key=iProdNr from=$oView->getCompArtList() item=product}]
            [{if $isFirst}]
              <th id="test_cmpAttrTitle_[{$smarty.foreach.CmpAttr.iteration}]" class="no_left_brd">[{ $oAttrib->title }]:</th>
              [{assign var="isFirst" value=false}]
            [{/if}]
            <td valign="top">
              <div id="test_cmpAttr_[{$smarty.foreach.CmpAttr.iteration}]_[{ $product->oxarticles__oxid->value }]">
                [{ if $oAttrib->aProd.$iProdNr && $oAttrib->aProd.$iProdNr->value}]
                  [{ $oAttrib->aProd.$iProdNr->value }]
                [{else}]
                  -
                [{/if}]
              </div>
            </td>
          [{/foreach}]
        </tr>
      [{/foreach}]
      </table>
      [{else}]
          [{ oxmultilang ident="COMPARE_POPUP_SELECTATLEASTTWOART" }]
      [{/if}]

      <br><br>
      <form action="">
        <div>
           <span class="btn"><input type="button" onClick="self.print();" class="btn" value="[{ oxmultilang ident="COMPARE_POPUP_PRINT" }]"></span>
        </div>
      </form>

  </div>

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer_plain.tpl"}]
