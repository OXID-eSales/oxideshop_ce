[{include file="_header.tpl" tree_path=$oView->getTreePath() titlepagesuffix=$oView->getTitlePageSuffix()}]

    <div class="boxhead">
        <h1 id="test_catTitle">[{$oView->getTitle()}]</h1>
        [{if $oView->getArticleCount() && $oView->showCategoryArticlesCount()}]<em id="test_catArtCnt">([{ $oView->getArticleCount() }])</em>[{/if}]
        [{assign var="actCategory" value=$oView->getActiveCategory()}]
        [{if $actCategory && $actCategory->oxcategories__oxdesc->value }]<small id="test_catDesc">[{$actCategory->oxcategories__oxdesc->value}]</small>[{/if}]
        [{assign var='rsslinks' value=$oView->getRssLinks() }]
        [{if $rsslinks.activeCategory}]
            <a class="rss" id="rssActiveCategory" href="[{$rsslinks.activeCategory.link}]" title="[{$rsslinks.activeCategory.title}]"></a>
            [{oxscript add="oxid.blank('rssActiveCategory');"}]
        [{/if}]
    </div>

    [{capture name=list_details}]

        [{if $actCategory->oxcategories__oxthumb->value }]
            [{assign var="thumbUrl" value=$actCategory->getThumbUrl()}]
            [{if $thumbUrl }]
              <img src="[{ $thumbUrl }]" alt="[{ $actCategory->oxcategories__oxtitle->value }]"><br>
            [{/if}]
        [{/if}]

        [{assign var="oCategoryAttributes" value=$oView->getAttributes()}]
        [{if $oCategoryAttributes }]
            <form method="post" action="[{ $oViewConf->getSelfActionLink() }]" name="_filterlist" id="filterList">
            <div class="catfilter">
                [{ $oViewConf->getHiddenSid() }]
                [{ $oViewConf->getNavFormParams() }]
                <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
                <input type="hidden" name="tpl" value="[{$oViewConf->getActTplName()}]">
                <input type="hidden" name="fnc" value="executefilter">

                <table cellpadding="0" cellspacing="0">
                [{foreach from=$oCategoryAttributes item=oFilterAttr key=sAttrID name=testAttr}]
                    <tr>
                        <td>
                            <label id="test_attrfilterTitle_[{$sAttrID}]_[{$smarty.foreach.testAttr.iteration}]">[{ $oFilterAttr->getTitle() }]:</label>
                        </td>
                        <td>
                           <select name="attrfilter[[{ $sAttrID }]]" onchange="oxid.form.send('filterList');">
                               <option value="" selected>[{ oxmultilang ident="LIST_PLEASECHOOSE" }]</option>
                               [{foreach from=$oFilterAttr->getValues() item=sValue}]
                               <option value="[{ $sValue }]" [{ if $oFilterAttr->getActiveValue() == $sValue }]selected[{/if}]>[{ $sValue }]</option>
                               [{/foreach}]
                           </select>
                        </td>
                    </tr>
                [{/foreach}]
                </table>

                <noscript>
                    <input type="submit" value="[{ oxmultilang ident="LIST_APPLYFILTER" }]">
                </noscript>
            </div>
            </form>
        [{/if}]

        [{if $oView->hasVisibleSubCats()}]
            [{ oxmultilang ident="LIST_SELECTOTHERCATS1" }]<b>[{$actCategory->oxcategories__oxtitle->value}]</b> [{ oxmultilang ident="LIST_SELECTOTHERCATS2" }]
            <hr>
            <ul class="list">
            [{foreach from=$oView->getSubCatList() item=category name=MoreSubCat}]
                [{if $category->getContentCats() }]
                    [{foreach from=$category->getContentCats() item=ocont name=MoreCms}]
                    <li><a id="test_MoreSubCms_[{$smarty.foreach.MoreSubCat.iteration}]_[{$smarty.foreach.MoreCms.iteration}]" href="[{$ocont->getLink()}]">[{ $ocont->oxcontents__oxtitle->value }]</a></li>
                    [{/foreach}]
                [{/if}]
                [{if $category->getIsVisible()}]
                    [{assign var="iconUrl" value=$category->getIconUrl()}]
                    [{if $iconUrl}]
                        <a id="test_MoreSubCatIco_[{$smarty.foreach.MoreSubCat.iteration}]" href="[{ $category->getLink() }]">
                            <img src="[{$category->getIconUrl() }]" alt="[{ $category->oxcategories__oxtitle->value }]">
                        </a>
                    [{else}]
                        <li><a id="test_MoreSubCat_[{$smarty.foreach.MoreSubCat.iteration}]" href="[{ $category->getLink() }]">[{ $category->oxcategories__oxtitle->value }][{if $oView->showCategoryArticlesCount() && $category->getNrOfArticles() > 0 }] ([{ $category->getNrOfArticles() }])[{/if}]</a></li>
                    [{/if}]
                [{/if}]
            [{/foreach}]
            </ul>
        [{/if}]

        [{if $actCategory->oxcategories__oxlongdesc->value}]
            <hr>
            <span id="test_catLongDesc">[{oxeval var=$actCategory->oxcategories__oxlongdesc}]</span>
        [{/if}]
    [{/capture}]

    <div class="box [{if $smarty.capture.list_details|trim ==''}]empty[{/if}]">
    [{$smarty.capture.list_details}]
    </div>

    [{if $oView->getArticleCount() }]
        [{include file="inc/list_locator.tpl" PageLoc="Top"}]
    [{/if}]

    [{foreach from=$oView->getArticleList() item=actionproduct name=test_articleList}]
        [{include file="inc/product.tpl" product=$actionproduct testid="action_"|cat:$actionproduct->oxarticles__oxid->value test_Cntr=$smarty.foreach.test_articleList.iteration}]
    [{/foreach}]


    [{if $oView->getArticleCount() }]
        [{include file="inc/list_locator.tpl" PageLoc="Bottom"}]
    [{/if}]

[{insert name="oxid_tracker" title="LIST_CATEGORY"|oxmultilangassign product=""}]
[{include file="_footer.tpl" }]
