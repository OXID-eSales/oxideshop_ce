  [{assign var="currency" value=$oView->getActCurrency()}]
  [{foreach from=$oView->getTop5ArticleList() item=_product}]
  <div class="listitem">
      [{ assign var="sTop5ArtTitle" value="`$_product->oxarticles__oxtitle->value` `$_product->oxarticles__oxvarselect->value`" }]
      <a id="test_Top5Pic_[{$_product->oxarticles__oxid->value}]" href="[{$_product->getMainLink()}]" class="picture">
          <img src="[{$_product->getIconUrl()}]" alt="[{ $sTop5ArtTitle|strip_tags }]">
      </a>
      <a id="test_Top5Title_[{$_product->oxarticles__oxid->value}]" href="[{$_product->getMainLink()}]" class="title">[{ $sTop5ArtTitle|strip_tags}]</a>
      [{oxhasrights ident="SHOWARTICLEPRICE"}]
      [{if $_product->getFPrice()}]
          [{assign var="currency" value=$oView->getActCurrency() }]
          <b id="test_Top5Price_[{$_product->oxarticles__oxid->value}]">[{ $_product->getFPrice() }] [{ $currency->sign}]<a href="#delivery_link" rel="nofollow">*</a></b>
      [{/if}]
      [{/oxhasrights}]
   </div>
  [{/foreach}]
