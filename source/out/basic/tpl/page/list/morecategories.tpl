[{ assign var="template_location" value="LIST_MORE_TITLE"|oxmultilangassign}]

[{include file="_header.tpl" location=$template_location}]

<!-- no products -->

<div class="containerfullrow">
  <div class="categorydetailsrow_more">
  [{if $oView->showTopCatNavigation()}]
    [{assign var="_navcategorytree" value=$oView->getCategoryTree()}]
    [{ math equation="ceil(x / y)" x=$_navcategorytree->count() y=3 assign="firstcount" }]
    [{ math equation="round(x / y)" x=$_navcategorytree->count() y=3 assign="lastcount" }]
    [{assign var="globcount" value=0}]
    [{assign var="localcount" value=0}]

    <table class="morecats">
      <colgroup>
        <col width="30%">
        <col width="5%">
        <col width="30%">
        <col width="5%">
        <col width="30%">
      </colgroup>
      <tr>
        <td valign="top">
        [{foreach from=$_navcategorytree item=category name=CatRoot}]
        [{if $category->getIsVisible() }]

          [{if $category->getContentCats() }]
            [{foreach from=$category->getContentCats() item=osubcat }]
              [{assign var="globcount" value=`$globcount+1`}]

              <dl>
                <dt>
                    <a href="[{ $osubcat->getLink() }]">[{ $osubcat->oxcontents__oxtitle->value }]</a>
                </dt>
              </dl>

              <div class="listmore_separator"></div>
            [{/foreach}]

            [{if $globcount == $firstcount }]
              </td><td>&nbsp;</td><td valign="top">
            [{elseif $globcount > $firstcount }]
              [{assign var="localcount" value=`$localcount+1`}]
              [{if $localcount == $lastcount }]
              </td><td>&nbsp;</td><td valign="top">
              [{/if}]
            [{/if}]

          [{/if}]

          [{assign var="globcount" value=`$globcount+1`}]
          <dl>
            <dt>
                <a id="test_CatRoot_[{$smarty.foreach.CatRoot.iteration}]" href="[{ $category->getLink() }]">[{ $category->oxcategories__oxtitle->value }] [{if $oView->showCategoryArticlesCount() && $category->getNrOfArticles() > 0 }]([{ $category->getNrOfArticles() }])[{/if}]</a>
            </dt>

          [{foreach from=$category->getSubCats() item=osubcat name=SubCat}]

            <dd>
            [{ if $osubcat->getContentCats() }]
              [{foreach from=$osubcat->getContentCats() item=oCatContent }]
              <a href="[{ $oCatContent->getLink() }]">[{ $oCatContent->oxcontents__oxtitle->value }]</a>
              [{/foreach}]
            [{ /if}]

            [{if $osubcat->getIsVisible()}]
              <a id="test_CatRoot_[{$smarty.foreach.CatRoot.iteration}]_SubCat_[{$smarty.foreach.SubCat.iteration}]" href="[{ $osubcat->getLink() }]">[{ $osubcat->oxcategories__oxtitle->value }] [{if $oView->showCategoryArticlesCount() && $osubcat->getNrOfArticles() > 0 }]([{ $osubcat->getNrOfArticles() }])[{/if}]</a>
            [{/if}]
            </dd>
          [{/foreach}]
          </dl>
          <div class="listmore_separator"></div>

          [{if $globcount == $firstcount }]
            </td><td>&nbsp;</td><td valign="top">
          [{elseif $globcount > $firstcount }]
            [{assign var="localcount" value=`$localcount+1`}]
            [{if $localcount == $lastcount }]
            </td><td>&nbsp;</td><td valign="top">
            [{/if}]
          [{/if}]

        [{/if}]
        [{/foreach }]
        </td>
      </tr>
    </table>
  [{/if}]
  </div>
</div>

[{insert name="oxid_tracker" title="LIST_CATEGORY"|oxmultilangassign product=""}]
[{include file="_footer.tpl"}]
