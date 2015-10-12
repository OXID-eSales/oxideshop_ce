[{if $oxcmp_categories}]
    [{assign var="categories" value=$oxcmp_categories}]
    [{block name="footer_categories"}]
    <dl class="list categories" id="footerCategories">
        <dt class="list-header">[{oxmultilang ident="CATEGORIES"}]</dt>
        [{foreach from=$categories item=_cat}]
            [{if $_cat->getIsVisible()}]
                [{if $_cat->getContentCats()}]
                    [{foreach from=$_cat->getContentCats() item=_oCont}]
                    <dd><a href="[{$_oCont->getLink()}]"><i></i>[{$_oCont->oxcontents__oxtitle->value}]</a></dd>
                    [{/foreach}]
                [{/if}]
                <dd><a href="[{$_cat->getLink()}]" [{if $_cat->expanded}]class="exp"[{/if}]>[{$_cat->oxcategories__oxtitle->value}] [{if $oView->showCategoryArticlesCount() && ( $_cat->getNrOfArticles() > 0 )}] ([{$_cat->getNrOfArticles()}])[{/if}]</a></dd>
            [{/if}]
        [{/foreach}]
    </dl>
    [{/block}]
[{/if}]

