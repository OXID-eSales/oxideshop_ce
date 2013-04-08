[{assign var="aLastProducts" value=$oView->getLastProducts() }]
[{if $aLastProducts && $aLastProducts->count() > 0 }]
  <strong id="test_LastSeenHeader" class="head2">[{ oxmultilang ident="DETAILS_LASTSEENPRODUCTS"}]</strong>
  [{foreach from=$aLastProducts item=lastproduct}]
    [{include file="inc/product.tpl" size="small" product=$lastproduct altproduct=$_lastproducts_aid sListType='' testid="LastSeen_"|cat:$lastproduct->oxarticles__oxid->value}]
  [{/foreach}]
[{/if}]