  [{foreach from=$list item=listitem name="testRecommlist"}]
  [{assign var="product" value=$listitem->getFirstArticle()}]
  <div class="listitem">
      [{ assign var="sBargainArtTitle" value="`$product->oxarticles__oxtitle->value` `$product->oxarticles__oxvarselect->value`" }]
      <a id="test_RightSideRecommlistPic_[{$smarty.foreach.testRecommlist.iteration}]" href="[{$product->getMainLink()}]" class="picture">
          <img src="[{$product->getIconUrl()}]" alt="[{ $sBargainArtTitle|strip_tags }]">
      </a>
      
      <a id="test_RightSideRecommlistTitle_[{$smarty.foreach.testRecommlist.iteration}]" href="[{ $listitem->getLink() }]"><b>[{ $listitem->oxrecommlists__oxtitle->value|strip_tags }]</b></a><br>
      <div id="test_RightSideRecommlistNo_[{$smarty.foreach.testRecommlist.iteration}]">[{ oxmultilang ident="INC_RIGHT_RECOMMLIST_LISTBY" }]: [{ $listitem->oxrecommlists__oxauthor->value|strip_tags }]</div>
   </div>
  [{/foreach}]