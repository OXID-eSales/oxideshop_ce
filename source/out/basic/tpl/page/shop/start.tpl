[{include file="_header.tpl" title=$template_title location="START_TITLE"|oxmultilangassign isStart=true}]

[{oxifcontent ident="oxstartwelcome" object="oCont"}]
<div class="welcome">[{$oCont->oxcontents__oxcontent->value}]</div>
[{/oxifcontent}]

[{if $oView->getTopArticleList() }]
  [{foreach from=$oView->getTopArticleList() item=actionproduct name=WeekArt}]
    [{include file="inc/product.tpl" product=$actionproduct showMainLink=true head="START_WEEKSPECIAL"|oxmultilangassign testid="WeekSpecial_"|cat:$actionproduct->oxarticles__oxid->value testHeader="WeekSpecial_`$smarty.foreach.WeekArt.iteration`"}]
  [{/foreach}]
[{/if}]

[{if $oView->getFirstArticle() }]
  [{oxifcontent ident="oxfirststart" object="oCont"}]
    [{assign var="oxfirststart_title" value=$oCont->oxcontents__oxtitle->value}]
    [{assign var="oxfirststart_text" value=$oCont->oxcontents__oxcontent->value}]
  [{/oxifcontent}]
  [{assign var="firstarticle" value=$oView->getFirstArticle()}]
  [{include file="inc/product.tpl" size='big' showMainLink=true class='topshop' head=$oxfirststart_title head_desc=$oxfirststart_text product=$firstarticle testid="FirstArticle_"|cat:$firstarticle->oxarticles__oxid->value testHeader=FirstArticle}]
[{/if}]

[{oxid_include_dynamic file="dyn/promotions.tpl"}]

[{if ($oView->getArticleList()|@count)>0 }]
  <strong id="test_LongRunHeader" class="head2">[{ oxmultilang ident="START_LONGRUNNINGHITS"}]</strong>
  [{if ($oView->getArticleList()|@count) is not even  }][{assign var="actionproduct_size" value="big"}][{/if}]
  [{foreach from=$oView->getArticleList() item=actionproduct}]
      [{include file="inc/product.tpl" showMainLink=true product=$actionproduct size=$actionproduct_size testid="LongRun_"|cat:$actionproduct->oxarticles__oxid->value }]
      [{assign var="actionproduct_size" value=""}]
  [{/foreach}]
[{/if}]

[{if ($oView->getNewestArticles()|@count)>0 }]
  <strong id="test_FreshInHeader" class="head2">
    [{ oxmultilang ident="START_JUSTARRIVED"}]

    [{assign var='rsslinks' value=$oView->getRssLinks() }]
    [{if $rsslinks.newestArticles}]
        <a class="rss" id="rssNewestProducts" href="[{$rsslinks.newestArticles.link}]" title="[{$rsslinks.newestArticles.title}]"></a>
        [{oxscript add="oxid.blank('rssNewestProducts');"}]
    [{/if}]
  </strong>
  [{foreach from=$oView->getNewestArticles() item=actionproduct}]
      [{include file="inc/product.tpl" showMainLink=true product=$actionproduct size="small" testid="FreshIn_"|cat:$actionproduct->oxarticles__oxid->value}]
  [{/foreach}]
[{/if}]

[{if ($oView->getCatOfferArticleList()|@count)>0 }]
  <strong id="test_CategoriesHeader" class="head2">[{ oxmultilang ident="START_CATEGORIES"}]</strong>
  [{if ($oView->getCatOfferArticleList()|@count) is not even  }][{assign var="actionproduct_size" value="big"}][{/if}]
  [{foreach from=$oView->getCatOfferArticleList() item=actionproduct name=CatArt}]
      [{if $actionproduct->getCategory() }]
          [{assign var="oCategory" value=$actionproduct->getCategory()}]
          [{assign var="actionproduct_title" value=$oCategory->oxcategories__oxtitle->value}]
          [{if $oView->showCategoryArticlesCount() && $oCategory->getNrOfArticles() > 0}][{assign var="actionproduct_title" value=$actionproduct_title|cat:" ("|cat:$oCategory->getNrOfArticles()|cat:")"}][{/if}]
          [{include file="inc/product.tpl" showMainLink=true product=$actionproduct size=$actionproduct_size head=$actionproduct_title head_link=$oCategory->getLink() testid="CatArticle_"|cat:$actionproduct->oxarticles__oxid->value  testHeader="Category_`$smarty.foreach.CatArt.iteration`"}]
          [{assign var="actionproduct_size" value=""}]
      [{/if}]
  [{/foreach}]
[{/if}]

[{include file="inc/tags.tpl"}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
