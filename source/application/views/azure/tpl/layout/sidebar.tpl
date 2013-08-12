[{foreach from=$oxidBlock_sidebar item="_block"}]
    [{$_block}]
[{/foreach}]

[{block name="sidebar"}]
    [{block name="sidebar_adminbanner"}]
        [{if $oView->isDemoShop()}]
            [{ include file="widget/sidebar/adminbanner.tpl" }]
        [{/if}]
    [{/block}]

    [{block name="sidebar_categoriestree"}]
        [{if $oView->getClassName() != 'start' && $oView->getClassName() != 'compare'}]
            [{oxid_include_widget cl="oxwCategoryTree" cnid=$oView->getCategoryId() deepLevel=0 noscript=1 nocookie=1}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_trustedshopsratings"}]
        [{if $oView->getClassName() eq "start"}]
            [{if $oViewConf->showTs("WIDGET") }]
                [{include file="widget/trustedshops/ratings.tpl" }]
            [{/if}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_partners"}]
        [{if $oView->getClassName() eq "start"}]
            [{include file="widget/sidebar/partners.tpl" }]
        [{/if}]
    [{/block}]

    [{block name="sidebar_boxproducts"}]
        [{if $oView->getTop5ArticleList()}]
            [{include file="widget/product/boxproducts.tpl" _boxId="topBox" _oBoxProducts=$oView->getTop5ArticleList() _sHeaderIdent="TOP_OF_THE_SHOP"}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_recommendation"}]
        [{if $oViewConf->getShowListmania() && $oView->getSimilarRecommListIds() }]
            [{oxid_include_widget nocookie=1 cl="oxwRecommendation" aArticleIds=$oView->getSimilarRecommListIds() searchrecomm=$oView->getRecommSearch()}]
        [{elseif $oViewConf->getShowListmania() && $oView->getRecommSearch()}]
            [{oxid_include_widget nocookie=1 cl="oxwRecommendation" _parent=$oView->getClassName() searchrecomm=$oView->getRecommSearch()}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_tags"}]
        [{if $oView->showTags() && $oView->getClassName() ne "details" && $oView->getClassName() ne "alist" && $oView->getClassName() ne "suggest" && $oView->getClassName() ne "tags"}]
            [{oxid_include_widget nocookie=1 cl="oxwTagCloud" blShowBox="1" noscript=1 }]
        [{/if}]
    [{/block}]

    [{block name="sidebar_news"}]
        [{if $oxcmp_news|count }]
            [{include file="widget/sidebar/news.tpl" oNews=$oxcmp_news}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_facebookfacepile"}]
          [{if $oView->isActive('FbFacepile') && $oView->isConnectedWithFb()}]
            <div id="facebookFacepile" class="box">
                <h3>[{oxmultilang ident="FACEBOOK_FACEPILE"}]</h3>
                <div class="content" id="productFbFacePile">
                    [{include file="widget/facebook/enable.tpl" source="widget/facebook/facepile.tpl" ident="#productFbFacePile" type="text"}]
                </div>
            </div>
        [{/if}]
    [{/block}]

    [{block name="sidebar_shopluperatings"}]
        [{if $oView->getClassName() eq "start"}]
           [{include file="widget/shoplupe/ratings.tpl" }]
        [{/if}]
    [{/block}]
[{/block}]

