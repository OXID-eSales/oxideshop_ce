[{assign var="template_title" value="WRAPPING_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location="WRAPPING_LOCATION"|oxmultilangassign}]
[{assign var="currency" value=$oView->getActCurrency() }]

<!-- ordering steps -->
[{include file="inc/steps_item.tpl" highlight=4}]



  <strong class="boxhead wrapptop">[{ oxmultilang ident="WRAPPING_ADDWRAPPING" }]</strong>
  <div class="box info">
      <table>
        <tr>
          <td>
            <img src="[{$oViewConf->getImageUrl()}]/giftwrapping_h.jpg" class="giftbigimg" alt="[{ oxmultilang ident="WRAPPING_PACKASGIFT" }]">
          </td>
          <td>
              <span class="fs12"><b>[{ oxmultilang ident="WRAPPING_ADDWRAPORCARD" }]</b></span><br>
              [{ oxmultilang ident="WRAPPING_PERSONALMESSAGE" }]
          </td>
        </tr>
      </table>
  </div>

  [{ if !$oxcmp_basket->getProductsCount()  }]
    <div class="msg">[{ oxmultilang ident="WRAPPING_BASKETEMPTY" }]</div>
  [{else}]
    <form name="basket" action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="wrapping">
          <input type="hidden" name="fnc" value="changewrapping">
        </div>

        [{ assign var="oWrapList" value=$oView->getWrappingList() }]
        [{if $oWrapList->count() }]

        <table class="wrapping">

          <colgroup>
            <col width="7">
            <col width="75">
            <col width="117">
            <col width="127">
            <col width="233">
            <col width="7">
          </colgroup>

          <!-- basket header -->
          <thead>
            <tr>
                <th class="brd"><div class="brd_line">&nbsp;</div></th>
                <th>[{ oxmultilang ident="WRAPPING_PRODUCT" }]</th>
                <th></th>
                <th>[{ oxmultilang ident="WRAPPING_GIFTOPTION" }]</th>
                <th><div class="ta_right">[{ oxmultilang ident="WRAPPING_PRICEPERPACKAGE" }]</div></th>
                <th></th>
            </tr>
          </thead>

          <!-- basket items -->
          [{assign var="icounter" value="0"}]
          [{assign var="basketitemlist" value=$oView->getBasketItems()}]
          [{foreach key=basketindex from=$oxcmp_basket->getContents() item=basketitem name=testArt}]
          [{assign var="basketproduct" value=$basketitemlist.$basketindex }]

            [{if $icounter > 0}]
              <tr class="wrp_sep">
                <td class="brd"></td>
                <td colspan="4" class="line"></td>
                <td></td>
              </tr>
            [{/if}]

            <tr valign="top">
              <!-- product image -->
              <td class="brd"></td>
              <td>
                  <a id="test_pic_[{$basketitem->getProductId()}]_[{$smarty.foreach.testArt.iteration}]" class="picture" href="[{$basketitem->getLink()}]">
                    <img src="[{$basketitem->getIconUrl()}]" alt="[{$basketitem->getTitle()|strip_tags }]">
                  </a>
              </td>

              <!-- product title & number -->
              <td>
                <div class="art_title"><a id="test_title_[{$basketitem->getProductId()}]_[{$smarty.foreach.testArt.iteration}]" rel="nofollow" href="[{$basketitem->getLink()}]">[{$basketitem->getTitle()}]</a></div>
                <div class="art_num">[{ oxmultilang ident="WRAPPING_ARTNUMBER" }] [{ $basketproduct->oxarticles__oxartnum->value }]</div>
              </td>

              <!-- product wrapping manager -->
              <td colspan="2">
                <table width="100%" class="wrapping_items">
                  <tr onclick="JavaScript:document.getElementsByName('wrapping[[{$basketindex}]]')[0].checked=true;">
                    <td><input id="test_WrapItem_[{$basketitem->getProductId()}]_NONE" type="radio" name="wrapping[[{$basketindex}]]" value="0" [{ if !$basketitem->getWrappingId()}]CHECKED[{/if}]></td>
                    <td>[{ oxmultilang ident="WRAPPING_NONE" }]</td>
                    <td align="right">0,00 [{ $currency->sign}]</td>
                  </tr>
                    [{assign var="ictr" value="1"}]
                    [{foreach from=$oView->getWrappingList() item=wrapping name=Wraps}]
                      <tr onclick="JavaScript:document.getElementsByName('wrapping[[{$basketindex}]]')['[{$ictr}]'].checked=true;">
                        <td><input id="test_WrapItem_[{$basketitem->getProductId()}]_[{$smarty.foreach.Wraps.iteration}]" type="radio" name="wrapping[[{$basketindex}]]" value="[{$wrapping->oxwrapping__oxid->value}]" [{ if $basketitem->getWrappingId() == $wrapping->oxwrapping__oxid->value}]CHECKED[{/if}]></td>
                        <td id="test_WrapItemName_[{ $basketitem->getProductId()}]_[{$smarty.foreach.Wraps.iteration}]">[{$wrapping->oxwrapping__oxname->value}]</td>
                        <td id="test_WrapItemPrice_[{ $basketitem->getProductId()}]_[{$smarty.foreach.Wraps.iteration}]" align="right">[{$wrapping->getFPrice()}] [{ $currency->sign}]</td>
                      </tr>
                      <tr onclick="JavaScript:document.getElementsByName('wrapping[[{$basketindex}]]')['[{$ictr}]'].checked=true;">
                        <td></td>
                        <td colspan="2">
                        [{if $wrapping->oxwrapping__oxpic->value}]
                            <img src="[{$wrapping->getPictureUrl()}]" alt="[{$wrapping->oxwrapping__oxname->value}]">
                        [{/if}]
                        </td>
                      </tr>
                      [{assign var="ictr" value="`$ictr+1`"}]
                    [{/foreach}]
                  </table>
                </td>
              <td></td>
            </tr>
            [{assign var="icounter" value="`$icounter+1`"}]
          [{/foreach}]

            <tr>
              <td class="brd"></td>
              <td colspan="4"></td>
              <td></td>
            </tr>

        </table>
        [{/if}]

     [{ assign var="oCardList" value=$oView->getCardList() }]
     [{if $oCardList->count() }]
     <strong class="boxhead">[{ oxmultilang ident="WRAPPING_GREETINGCARD" }]</strong>
     <div class="box info">


      <dl class="orderinfocol greetingcard">
        <dt>[{ oxmultilang ident="WRAPPING_SELECTCARD" }]</dt>
        <dd>
            <table width="100%">
                <tr>
                  <td>
                      <div class="cardbox" onclick="JavaScript:document.getElementsByName('chosencard')[0].checked=true;">
                          <div class="card_title">
                              <input id="test_CardItem_NONE" type="radio" class="chbox" name="chosencard" value="0" [{ if !$oxcmp_basket->getCardId() }]CHECKED[{/if}]>
                              [{ oxmultilang ident="WRAPPING_NOGREETINGCARD" }]
                          </div>
                      </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    [{assign var="icounter" value="0"}]
                    [{counter start=0 print=false}]
                    [{assign var="icounter" value="0"}]

                    [{foreach from=$oCardList item=card name=GreetCards}]

                    [{if $icounter == 2}]
                     <div class="card_sep"></div>
                    [{/if}]

                     <div class="cardbox" onclick="JavaScript:document.getElementsByName('chosencard')[[{counter}]].checked=true;">
                        <div class="card_title">
                            <input id="test_CardItem_[{$smarty.foreach.GreetCards.iteration}]" type="radio" class="chbox" name="chosencard" value="[{$card->oxwrapping__oxid->value}]" [{ if $oxcmp_basket->getCardId() == $card->oxwrapping__oxid->value}]CHECKED[{/if}]>
                            <span id="test_CardItemNamePrice_[{$smarty.foreach.GreetCards.iteration}]">[{$card->oxwrapping__oxname->value}] ([{$card->getFPrice() }] [{ $currency->sign}])</span>
                        </div>
                        [{if $card->oxwrapping__oxpic->value}]
                        <div class="card_body">
                            <img src="[{$card->getPictureUrl()}]" alt="[{$card->oxwrapping__oxname->value}]">
                        </div>
                        [{/if}]
                     </div>

                     [{assign var="icounter" value="`$icounter+1`"}]

                    [{/foreach}]
                  </td>
                </tr>
              </table>
        </dd>
      </dl>

      <div class="card_sep"></div>
      <div class="dot_sep"></div>
      <br>
      <dl class="orderinfocol greetingcard">
        <dt>[{ oxmultilang ident="WRAPPING_GREETINGMESSAGE" }]</dt>
        <dd>
          <b>[{ oxmultilang ident="WRAPPING_OPTIONALMESSAGE" }]</b><br>
          [{ oxmultilang ident="WRAPPING_ADDFORANDFROM" }]<br>
          <textarea cols="102" rows="5" name="giftmessage" class="fullsize">[{$oxcmp_basket->getCardMessage()}]</textarea>
        </dd>
      </dl>
     </div>
     [{/if}]

      <div class="bar prevnext">
          <div class="right">
              <input id="test_BackToOrder" type="submit" value="[{ oxmultilang ident="WRAPPING_BACKTOORDER" }]">
          </div>
      </div>

    </form>

    &nbsp;


  [{/if}]


[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
