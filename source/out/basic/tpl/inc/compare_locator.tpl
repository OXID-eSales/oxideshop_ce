[{assign var="pageNavigation" value=$oView->getPageNavigation()}]
  <div class="locator compare">
    [{if $pageNavigation->NrOfPages > 1}]
      <div class="lochead">
          <strong class="h4" id="test_ComparePageXofY">[{ oxmultilang ident="INC_COMPARE_LOCATOR_PAGE" }] [{ $pageNavigation->actPage  }] / [{ $pageNavigation->NrOfPages  }]</strong>
          <div class="right">
           [{ if $pageNavigation->previousPage }]
             <a id="test_link_prevPage[{$where}]" href="[{$pageNavigation->previousPage}]"><span class="arrow">&laquo;</span></a>
           [{/if}]
              |
           [{foreach key=iPage from=$pageNavigation->changePage item=page}]
             [{if $iPage > ($pageNavigation->actPage - 10) && $iPage < ($pageNavigation->actPage + 10)}]
               <a id="test_PageNr[{$PageLoc}]_[{$iPage}]" href="[{$page->url}]" [{if $iPage == $pageNavigation->actPage }]class="active"[{/if}]>[{$iPage}]</a>
             [{/if}]
           [{/foreach}]
              |
           [{ if $pageNavigation->nextPage }]
             <a id="test_link_nextPage[{$where}]" href="[{$pageNavigation->nextPage}]"><span class="arrow">&raquo;</span></a>
           [{/if}]
          </div>
      </div>
    [{/if }]

      <div class="locbody">
          <div class="left">
               <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
                  <div>
                    <span class="btn"><input type="button" value="[{ oxmultilang ident="INC_COMPARE_LOCATOR_DISPLAYPOPUP" }]" class="btn" onclick="oxid.popup.compare('[{ $oViewConf->getSelfLink() }]cl=[{ $oViewConf->getActiveClassName() }]&amp;fnc=inPopup');"></span>
                  </div>
               </form>
          </div>
      </div>

  </div>
