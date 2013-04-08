  <strong class="h2" id="test_LeftSideNewsHeader">[{ oxmultilang ident="INC_LEFTITEM_NEWS" }]</strong>
  <dl class="news">
    [{foreach from=$oxcmp_news item=oxcmp_news name=newsList}]
     <dt>
        <a id="test_newsTitle_[{$smarty.foreach.newsList.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=news" }]#[{$oxcmp_news->oxnews__oxid->value}]">
          [{ $oxcmp_news->oxnews__oxlongdesc|strip_tags|oxtruncate:100 }]
        </a>
     </dt>
     <dd>
         <a id="test_newsContinue_[{$smarty.foreach.newsList.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=news" }]#[{$oxcmp_news->oxnews__oxid->value}]" class="link">
             [{ oxmultilang ident="INC_CMP_NEWS_CONTINUE" }]
         </a>
     </dd>
    [{/foreach}]
  </dl>