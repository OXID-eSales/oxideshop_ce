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
        [{if $oxcmp_categories }]
            [{include file="widget/sidebar/categoriestree.tpl" categories=$oxcmp_categories->getClickRoot() act=$oxcmp_categories->getClickCat() deepLevel=0}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_trustedshopsratings"}]
        [{if $oView->getClassName() eq "start"}]
            [{if $oViewConf->showTs("WIDGET")}]
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
        [{if $oView->getClassName() eq "start" && $oView->getTop5ArticleList()}]
            [{include file="widget/product/boxproducts.tpl" _boxId="topBox" _oBoxProducts=$oView->getTop5ArticleList() _sHeaderIdent="BOX_TOPOFTHESHOP_HEADER"}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_recommendation"}]
        [{if $oViewConf->getShowListmania() }]
            [{include file="widget/sidebar/recommendation.tpl"}]
        [{/if}]
    [{/block}]

    [{block name="sidebar_tags"}]
        [{if $oView->showTags() && $oView->getClassName() ne "details" && $oView->getClassName() ne "alist" && $oView->getClassName() ne "suggest" && $oView->getClassName() ne "tags"}]
            [{if $oView->getTagCloudManager() }]
                [{include file="widget/sidebar/tags.tpl" oTagsManager=$oView->getTagCloudManager()}]
            [{/if}]
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
		    	<h3>[{oxmultilang ident="WIDGET_FACEBOOK_FACEPILE_HEADER"}]</h3>
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

