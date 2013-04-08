  [{foreach from=$oView->getBargainArticleList() item=_product name=bargainList}]
  <div class="listitem bargain">
      [{ assign var="sBargainArtTitle" value="`$_product->oxarticles__oxtitle->value` `$_product->oxarticles__oxvarselect->value`" }]
      <a id="test_picBargain_[{$smarty.foreach.bargainList.iteration}]" href="[{$_product->getMainLink()}]" class="picture">
          <img src="[{$_product->getIconUrl()}]" alt="[{ $sBargainArtTitle|strip_tags }]">
      </a>
      <a id="test_titleBargain_[{$smarty.foreach.bargainList.iteration}]" href="[{$_product->getMainLink()}]" class="title">[{ $sBargainArtTitle|strip_tags }]</a>
      [{oxhasrights ident="SHOWARTICLEPRICE"}]
      [{if $_product->getFPrice()}]
          [{assign var="currency" value=$oView->getActCurrency() }]
          <b id="test_priceBargain_[{$smarty.foreach.bargainList.iteration}]">[{ $_product->getFPrice() }] [{ $currency->sign}]<a href="#delivery_link" rel="nofollow">*</a></b>
      [{/if}]
      [{/oxhasrights}]
   </div>
  [{/foreach}]
