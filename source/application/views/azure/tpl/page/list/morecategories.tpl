[{oxscript include="js/widgets/oxarticlebox.js" priority=10 }]
[{oxscript add="$( '#content' ).oxArticleBox();"}]
[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{ oxmultilang ident="CATEGORY_OVERVIEW" }]</h1>

    [{assign var="_navcategorytree" value=$oView->getCategoryTree()}]
    [{assign var="_iCategoriesPerRow"  value=4}]
    [{assign var="iSubCategoriesCount" value=0}]
    [{if $_navcategorytree|count}]
        [{oxscript include="js/widgets/oxequalizer.js" priority=10 }]
        [{oxscript add="$(function(){oxEqualizer.equalHeight($( '.subcatList li .content' ));});"}]
        <ul class="subcatList clear">
            <li>
            [{foreach from=$_navcategorytree item=category name=MoreSubCat}]
                [{* TOP categories *}]
                [{if $category->getIsVisible()}]
                    [{* CMS top categories *}]
                    [{if $category->getContentCats() }]
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
                    [{if $iSubCategoriesCount%$_iCategoriesPerRow == 0}]
                    </li><li class="clear">
                    [{/if}]
                    [{assign var="iSubCategoriesCount" value=$iSubCategoriesCount+1}]
                    [{assign var="iconUrl" value=$category->getIconUrl()}]
                    <div class="box">
                        <h3>
                            <a id="moreSubCat_[{$smarty.foreach.MoreSubCat.iteration}]" href="[{ $category->getLink() }]">
                                [{$category->oxcategories__oxtitle->value }][{ if $oView->showCategoryArticlesCount() && ($category->getNrOfArticles() > 0) }] ([{ $category->getNrOfArticles() }])[{/if}]
                            </a>
                        </h3>
                        [{* Top categories subcategories *}]
                        [{if $category->getHasVisibleSubCats()}]
                            <ul class="content">
                                [{if $iconUrl}]
                                    <li class="subcatPic">
                                        <a href="[{ $category->getLink() }]">
                                            <img src="[{$category->getIconUrl() }]" alt="[{ $category->oxcategories__oxtitle->value }]" height="100" width="168">
                                        </a>
                                    </li>
                                [{/if}]
                                [{foreach from=$category->getSubCats() item=subcategory}]
                                    [{if $subcategory->getIsVisible() }]
                                        [{* CMS subcategories  *}]
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
                        [{elseif $iconUrl}]
                            <div class="content catPicOnly">
                                <div class="subcatPic">
                                    [{if $iconUrl}]
                                    <a href="[{ $category->getLink() }]">
                                        <img src="[{$category->getIconUrl() }]" alt="[{ $category->oxcategories__oxtitle->value }]" height="100" width="168">
                                    </a>
                                    [{/if}]
                                </div>
                            </div>
                        [{else}]
                            <div class="content"></div>
                        [{/if}]
                    </div>
            [{/if}]
            [{if $iSubCategoriesCount%$_iCategoriesPerRow == 0}]
            </li><li class="clear">
            [{/if}]
        [{/foreach}]
        </li>
        </ul>

    [{/if}]
    [{insert name="oxid_tracker"}]
[{/capture}]


[{include file="layout/page.tpl" sidebar="Left"}]
