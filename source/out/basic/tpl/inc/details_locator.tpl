[{assign var="actCategory" value=$oView->getActiveCategory()}]
[{if ($actCategory && $actCategory->iProductPos) || $actCategory->prevProductLink || $actCategory->nextProductLink }]


  <div class="locator">
     [{if $actCategory && $actCategory->iProductPos }]
       <div class="lochead">
           <strong class="h4" id="test_prodXofY_[{$where}]">[{ oxmultilang ident="INC_DETAILS_LOCATOR_PRODUCT" }] [{ $actCategory->iProductPos }] / [{ $actCategory->iCntOfProd }]</strong>
           <div class="right"><a id="test_BackOverview[{$where}]" href="[{$actCategory->toListLink }]">[{ oxmultilang ident="INC_DETAILS_LOCATOR_BACKTOOVERVIEW" }][{if ($oView->getListType() == "list" || $oView->getListType() == "vendor" || $oView->getListType() == "manufacturer") && $actCategory}] [{ $actCategory->oxcategories__oxtitle->value }][{/if}]</a></div>
       </div>
     [{/if }]
     <div class="locbody">
        <span id="selID_ArticleNav[{$where}]">
            [{assign var="blSep" value=""}]
            [{if $actCategory->prevProductLink }]
              <a id="test_link_prevArticle[{$where}]" href="[{$actCategory->prevProductLink }]"><span class="arrow">&laquo;</span> [{ oxmultilang ident="INC_DETAILS_LOCATOR_PREVIOUSPRODUCT" }]</a>
              [{assign var="blSep" value="y"}]
            [{/if}]
            [{if $actCategory->nextProductLink }]
              [{ if $blSep == "y"}]
                <span class="sep">|</span>
              [{/if}]
              <a id="test_link_nextArticle[{$where}]" href="[{$actCategory->nextProductLink }]">[{ oxmultilang ident="INC_DETAILS_LOCATOR_NEXTPRODUCT" }] <span class="arrow">&raquo;</span></a>
            [{/if}]
        </span>
     </div>
  </div>

[{/if}]
