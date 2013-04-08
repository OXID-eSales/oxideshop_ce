[{if $oxcmp_categories }]
    [{assign var="categories" value=$oxcmp_categories }]
    [{block name="footer_categories"}]
    <dl class="categories" id="footerCategories">
        <dt>[{oxmultilang ident="CATEGORIES" }]</dt>
        <dd>
            <ul class="list categories">
                [{foreach from=$categories item=_cat}]
                    [{if $_cat->getIsVisible() }]
                        [{if $_cat->getContentCats() }]
                            [{foreach from=$_cat->getContentCats() item=_oCont}]
                            <li><a href="[{$_oCont->getLink()}]"><i></i>[{ $_oCont->oxcontents__oxtitle->value }]</a></li>
                            [{/foreach}]
                        [{/if }]
                        <li><a href="[{$_cat->getLink()}]" [{if $_cat->expanded}]class="exp"[{/if}]>[{$_cat->oxcategories__oxtitle->value}] [{ if $oView->showCategoryArticlesCount() && ( $_cat->getNrOfArticles() > 0 )}] ([{$_cat->getNrOfArticles()}])[{/if}]</a></li>
                    [{/if}]
                [{/foreach}]
            </ul>
        </dd>
    </dl>
    [{/block}]
[{/if}]

