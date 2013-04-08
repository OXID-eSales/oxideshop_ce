[{oxscript add="$('a.js-external').attr('target', '_blank');"}]
[{oxscript include="js/widgets/oxarticlebox.js" priority=10 }]
[{oxscript add="$( '#content' ).oxArticleBox();"}]

[{assign var="actCategory" value=$oView->getActiveCategory()}]


[{capture append="oxidBlock_sidebar"}]
    [{assign var="listType" value=$oView->getListType()}]
    [{if $listType=='manufacturer' || $listType=='vendor'}]
        [{if $actCategory && $actCategory->getIconUrl()}]
        <div class="box">
            <h3>
                [{if $listType=='manufacturer'}]
                    [{ oxmultilang ident="BRAND" }]
                [{elseif $listType=='vendor'}]
                    [{ oxmultilang ident="VENDOR" }]
                [{/if}]
            </h3>
            <div class="featured icon">
                <img src="[{$actCategory->getIconUrl()}]" alt="[{$actCategory->getTitle()}]">
            </div>
        </div>
        [{/if}]
    [{/if}]
[{/capture}]

[{capture append="oxidBlock_content"}]
        [{if $actCategory->oxcategories__oxthumb->value && $actCategory->getThumbUrl()}]
            <img src="[{$actCategory->getThumbUrl()}]" alt="[{$actCategory->oxcategories__oxtitle->value}]" class="categoryPicture">
        [{/if}]

        [{if $listType!='tag' && $actCategory && $actCategory->getShortDescription() }]
            <div class="categoryTopDescription" id="catDesc">[{$actCategory->getShortDescription()}]</div>
        [{/if}]
        [{if $actCategory->oxcategories__oxlongdesc->value }]
            <div class="categoryTopLongDescription" id="catLongDesc">[{oxeval var=$actCategory->oxcategories__oxlongdesc}]</div>
        [{/if}]

        [{if $oView->hasVisibleSubCats()}]
            [{assign var="iSubCategoriesCount" value=0}]
            [{oxscript include="js/widgets/oxequalizer.js" priority=10 }]
            [{oxscript add="$(function(){oxEqualizer.equalHeight($( '.subcatList li .content' ));});"}]
            <ul class="subcatList clear">
                <li>
                [{foreach from=$oView->getSubCatList() item=category name=MoreSubCat}]
                    [{ if $category->getContentCats() }]
                        [{foreach from=$category->getContentCats() item=ocont name=MoreCms}]
                            [{assign var="iSubCategoriesCount" value=$iSubCategoriesCount+1}]
                            <div class="box">
                            <h3>
                                <a id="moreSubCms_[{$smarty.foreach.MoreSubCat.iteration}]_[{$smarty.foreach.MoreCms.iteration}]" href="[{$ocont->getLink()}]">[{ $ocont->oxcontents__oxtitle->value }]</a>
                            </h3>
                            <ul class="content"></ul>
                            </div>
                        [{/foreach}]
                    [{/if }]
                    [{if $iSubCategoriesCount%4 == 0}]
                    </li><li>
                    [{/if}]
                    [{if $category->getIsVisible()}]
                        [{assign var="iSubCategoriesCount" value=$iSubCategoriesCount+1}]
                        [{assign var="iconUrl" value=$category->getIconUrl()}]
                            <div class="box">
                                <h3>
                                    <a id="moreSubCat_[{$smarty.foreach.MoreSubCat.iteration}]" href="[{ $category->getLink() }]">
                                        [{$category->oxcategories__oxtitle->value }][{ if $oView->showCategoryArticlesCount() && ($category->getNrOfArticles() > 0) }] ([{ $category->getNrOfArticles() }])[{/if}]
                                    </a>
                                </h3>
                                [{if $category->getHasVisibleSubCats()}]
                                    <ul class="content">
                                        [{if $iconUrl}]
                                            <li class="subcatPic">
                                                <a href="[{ $category->getLink() }]">
                                                    <img src="[{$category->getIconUrl() }]" alt="[{ $category->oxcategories__oxtitle->value }]">
                                                </a>
                                            </li>
                                        [{/if}]
                                        [{foreach from=$category->getSubCats() item=subcategory}]
                                            [{if $subcategory->getIsVisible() }]
                                                [{ foreach from=$subcategory->getContentCats() item=ocont name=MoreCms}]
                                                    <li>
                                                        <a href="[{$ocont->getLink()}]"><strong>[{ $ocont->oxcontents__oxtitle->value }]</strong></a>
                                                    </li>
                                                [{/foreach }]
                                                <li>
                                                    <a href="[{ $subcategory->getLink() }]">
                                                        <strong>[{ $subcategory->oxcategories__oxtitle->value }]</strong>[{ if $oView->showCategoryArticlesCount() && ($subcategory->getNrOfArticles() > 0) }] ([{ $subcategory->getNrOfArticles() }])[{/if}]
                                                    </a>
                                                </li>
                                             [{/if}]
                                        [{/foreach}]
                                    </ul>
                                [{else}]
                                    <div class="content catPicOnly">
                                        <div class="subcatPic">
                                        [{if $iconUrl}]
                                            <a href="[{ $category->getLink() }]">
                                                <img src="[{$category->getIconUrl() }]" alt="[{ $category->oxcategories__oxtitle->value }]">
                                            </a>
                                         [{/if}]
                                        </div>
                                    </div>
                                [{/if}]
                            </div>
                    [{/if}]
                [{if $iSubCategoriesCount%4 == 0}]
                </li>
                <li>
                [{/if}]
                [{/foreach}]
            </li>
            </ul>
        [{/if}]

    [{if $oView->getArticleList()|@count > 0}]
        <h1 class="pageHead">[{$oView->getTitle()}]
            [{assign var='rsslinks' value=$oView->getRssLinks() }]
            [{ if $rsslinks.activeCategory}]
                <a class="rss js-external" id="rssActiveCategory" href="[{$rsslinks.activeCategory.link}]" title="[{$rsslinks.activeCategory.title}]"><img src="[{$oViewConf->getImageUrl('rss.png')}]" alt="[{$rsslinks.activeCategory.title}]"><span class="FXgradOrange corners glowShadow">[{$rsslinks.activeCategory.title}]</span></a>
            [{/if }]
        </h1>
        <div class="listRefine clear bottomRound">
        [{* uncomment if want to use descriptions in locator
           [{if $actCategory && $actCategory->getShortDescription() }]
                <div id="catDescLocator" class="categoryDescription">[{$actCategory->getShortDescription()}]</div>
            [{/if}]

            [{if $actCategory->oxcategories__oxlongdesc->value }]
                <div id="catLongDescLocator" class="categoryDescription">[{oxeval var=$actCategory->oxcategories__oxlongdesc}]</div>
            [{/if}]
        *}]
            [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigationLimitedTop() attributes=$oView->getAttributes() listDisplayType=true itemsPerPage=true sort=true }]
        </div>
        [{* List types: grid|line|infogrid *}]
        [{include file="widget/product/list.tpl" type=$oView->getListDisplayType() listId="productList" products=$oView->getArticleList()}]
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigationLimitedBottom() place="bottom"}]
    [{/if}]
    [{insert name="oxid_tracker"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left" tree_path=$oView->getTreePath()}]