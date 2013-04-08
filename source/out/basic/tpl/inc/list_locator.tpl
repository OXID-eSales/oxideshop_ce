[{assign var="pageNavigation" value=$oView->getPageNavigation()}]
  <div class="locator">
    [{if $pageNavigation->NrOfPages > 1}]
      <div class="lochead">
          <strong class="h4" id="test_listXofY_[{$PageLoc}]">[{ oxmultilang ident="INC_LIST_LOCATOR_PAGE" }] [{ $pageNavigation->actPage  }] / [{ $pageNavigation->NrOfPages  }]</strong>
          <div class="right">
           [{ if $pageNavigation->previousPage }]
             <a id="test_PrevPage[{$PageLoc}]" href="[{$pageNavigation->previousPage}]"><span class="arrow">&laquo;</span></a>
           [{/if}]
              |
           [{foreach key=iPage from=$pageNavigation->changePage item=page}]
             [{if $iPage > ($pageNavigation->actPage - 10) && $iPage < ($pageNavigation->actPage + 10)}]
               <a id="test_PageNr[{$PageLoc}]_[{$iPage}]" href="[{$page->url}]" [{if $iPage == $pageNavigation->actPage }]class="active"[{/if}]>[{$iPage}]</a>
             [{/if}]
           [{/foreach}]
              |
           [{ if $pageNavigation->nextPage }]
             <a id="test_NextPage[{$PageLoc}]" href="[{$pageNavigation->nextPage}]"><span class="arrow">&raquo;</span></a>
           [{/if}]
          </div>
      </div>
    [{/if }]
      <div class="locbody">
           <div class="left">
               [{ oxmultilang ident=$sLocatorCaption|default:"INC_LIST_LOCATOR_ARTICLESPERPAGE" }]
               [{foreach from=$oViewConf->getNrOfCatArticles() item=iArtPerPage}]
                 <a id="test_ArtPerPage[{$PageLoc}]_[{$iArtPerPage}]" href="[{ $oViewConf->getSelfLink() }]tpl=[{$oViewConf->getActTplName()}]&amp;_artperpage=[{$iArtPerPage}]&amp;[{$oView->getAdditionalParams()}]" class="[{if $oViewConf->getArtPerPageCount() == $iArtPerPage }]active[{/if}]" rel="nofollow">[{$iArtPerPage}]</a>
               [{/foreach}]
           </div>
           <div class="right">
               [{include file="inc/sort.snippet.tpl"}]
           </div>
      </div>
  </div>