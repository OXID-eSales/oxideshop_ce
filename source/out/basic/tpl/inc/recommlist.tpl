[{foreach from=$oView->getArticleList() name=recommlist item=product}]

  [{if $smarty.foreach.recommlist.first && $smarty.foreach.recommlist.last }]
    [{assign var="recommlist_class" value=""}]
  [{elseif $smarty.foreach.recommlist.first && !$smarty.foreach.recommlist.last }]
    [{assign var="recommlist_class" value="firstinlist"}]
  [{elseif $smarty.foreach.recommlist.last}]
    [{assign var="recommlist_class" value="lastinlist"}]
  [{else}]
    [{assign var="recommlist_class" value="inlist"}]
  [{/if}]

  [{include file="inc/product.tpl" product=$product size="thin" class=$recommlist_class removeFunction=$removeFunction recommid=$recommid testid=$smarty.foreach.recommlist.iteration }]

  [{if !$smarty.foreach.recommlist.last }]
    <div class="separator"></div>
  [{/if}]

[{/foreach}]
