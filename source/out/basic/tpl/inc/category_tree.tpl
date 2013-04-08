[{if $tree || $oView->getContentCategory() }]
[{assign var="oContentCat" value=$oView->getContentCategory() }]
[{defun name="category_tree" tree=$tree act=$act class=$class testSubCat=''}]
[{strip}]
    <ul [{if $class}]class="[{$class}]"[{/if}]>
    [{foreach from=$tree item=ocat key=catkey name=$test_catName}]
        [{if ( !$ocat->isTopCategory() || !$oView->showTopCatNavigation() ) && $ocat->getContentCats() }]
            [{foreach from=$ocat->getContentCats() item=ocont key=contkey name=cont}]
            <li><a id="test_BoxLeft_Cms_[{if $ocat->isTopCategory()}][{$ocat->oxcategories__oxid->value}]_[{$smarty.foreach.cont.iteration}][{else}][{$testSubCat}]_sub[{$smarty.foreach.cont.iteration}][{/if}]" href="[{$ocont->getLink()}]" class="[{if $ocat->isTopCategory()}]root[{/if}][{if $oContentCat && $oContentCat->getId()==$ocont->getId()}] act last[{/if}]">[{ $ocont->oxcontents__oxtitle->value }]</a></li>
            [{/foreach}]
        [{/if}]
        [{if $ocat->getIsVisible() }]
        <li>
            <a id="test_BoxLeft_Cat_[{if $ocat->isTopCategory()}][{$ocat->oxcategories__oxid->value}]_[{$smarty.foreach.$test_catName.iteration}][{else}][{$testSubCat}]_sub[{$smarty.foreach.$test_catName.iteration}][{/if}]" href="[{$ocat->getLink()}]" class="[{if $ocat->isTopCategory()}]root [{/if}][{if $ocat->hasVisibleSubCats}][{if $ocat->expanded }]exp [{/if}]has [{else}]last [{/if}][{if isset($act) && $act->getId()==$ocat->getId() && !$oContentCat }]act [{/if}]">[{$ocat->oxcategories__oxtitle->value}] [{if $oView->showCategoryArticlesCount() && $ocat->getNrOfArticles() > 0}] ([{$ocat->getNrOfArticles()}])[{/if}]</a>
            [{if $ocat->getSubCats() && $ocat->expanded}]
                [{fun name="category_tree" tree=$ocat->getSubCats() act=$act class="" testSubCat=$ocat->oxcategories__oxid->value }]
            [{/if}]
        </li>
        [{/if}]
    [{foreachelse}]
        [{if $oContentCat }]
            <li><a id="test_BoxLeft_Cms_0" class="root act" href="[{$oContentCat->getLink()}]">[{ $oContentCat->oxcontents__oxtitle->value }]</a></li>
        [{/if}]
    [{/foreach}]
    </ul>
[{/strip}]
[{/defun}]
[{/if}]
