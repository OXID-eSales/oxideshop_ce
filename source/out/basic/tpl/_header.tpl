<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html[{if $oView->getActiveLangAbbr()}] lang="[{ $oView->getActiveLangAbbr() }]"[{/if}] [{if $oViewConf->getFbAppId()}]xmlns:fb="http://www.facebook.com/2008/fbml"[{/if}]>
<head>
    [{assign var="_titlesuffix" value=$_titlesuffix|default:$oView->getTitleSuffix()}]
    [{assign var="_titleprefix" value=$_titleprefix|default:$oView->getTitlePrefix() }]
    [{assign var="title" value=$title|default:$oView->getTitle() }]
    <title>[{ $_titleprefix }][{if $title&& $_titleprefix }] | [{/if}][{$title|strip_tags}][{if $_titlesuffix}] | [{$_titlesuffix}][{/if}][{if $titlepagesuffix}] | [{$titlepagesuffix}][{/if}]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$oView->getCharSet()}]">
    [{if $oView->noIndex() == 1 }]
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    [{elseif $oView->noIndex() == 2 }]
    <meta name="ROBOTS" content="NOINDEX, FOLLOW">
    [{/if}]
    [{if $oView->getMetaDescription()}]<meta name="description" content="[{$oView->getMetaDescription()}]">[{/if}]
    [{if $oView->getMetaKeywords()}]<meta name="keywords" content="[{$oView->getMetaKeywords()}]">[{/if}]
    [{if $oViewConf->getFbAppId()}]
        [{if $oViewConf->getActiveClassName() == 'details' }]
            <meta property="og:type" content="product">
            <meta property="og:image" content="[{$oView->getActPicture()}]">
            <meta property="og:url" content="[{$oView->getCanonicalUrl()}]">
            <meta property="og:site_name" content="[{$oViewConf->getCurrentHomeDir()}]">
            <meta property="fb:app_id" content="[{$oViewConf->getFbAppId()}]">
            <meta property="og:title" content="[{ $_titleprefix }][{if $title&& $_titleprefix }] | [{/if}][{$title|strip_tags}][{if $_titlesuffix}] | [{$_titlesuffix}][{/if}][{if $titlepagesuffix}] | [{$titlepagesuffix}][{/if}]">
        [{/if}]
    [{/if}]

    [{assign var="canonical_url" value=$oView->getCanonicalUrl()}]
    [{if $canonical_url }]<link rel="canonical" href="[{ $canonical_url }]">[{/if}]
    <link rel="shortcut icon" href="[{ $oViewConf->getImageUrl() }]favicon.ico">
    <link rel="stylesheet" type="text/css" href="[{ $oViewConf->getResourceUrl() }]oxid.css">
    <!--[if IE 8]><link rel="stylesheet" type="text/css" href="[{ $oViewConf->getResourceUrl() }]oxid_ie8.css"><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="[{ $oViewConf->getResourceUrl() }]oxid_ie7.css"><![endif]-->
    <!--[if IE 6]><link rel="stylesheet" type="text/css" href="[{ $oViewConf->getResourceUrl() }]oxid_ie6.css"><![endif]-->

    [{assign var='rsslinks' value=$oView->getRssLinks() }]
    [{if $rsslinks}]
      [{foreach from=$rsslinks item='rssentry'}]
        <link rel="alternate" type="application/rss+xml" title="[{$rssentry.title|strip_tags}]" href="[{$rssentry.link}]">
      [{/foreach}]
    [{/if}]
    [{if $oViewConf->isTplBlocksDebugMode()}]
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(function(){
                $("hr.debugBlocksStart").each(function(){
                    var blockTitle = $(this).attr("title");
                    var blockId    = $(this).attr("id");
                    var endBlock   = $("hr.debugBlocksEnd[title="+blockId+"]");

                    var _firstElement = $(this).next();
                    var _lastElement  = endBlock.prev();
                    while (_firstElement.hasClass('debugBlocksStart')) {
                        _firstElement  = _firstElement.prev();
                    }
                    while (_lastElement.hasClass('debugBlocksEnd')) {
                        _lastElement  = _lastElement.prev();
                    }
                    var divLeft    = Math.min(_firstElement.offset().left, _lastElement.offset().left);
                    var divTop     = Math.min(_firstElement.offset().top, _lastElement.offset().top+_lastElement.outerHeight());
                    var divHeight  = Math.max(_firstElement.offset().top, (_lastElement.offset().top+_lastElement.outerHeight())) - divTop;
                    var divWidth   = Math.max(_firstElement.offset().left+_firstElement.outerWidth(), _lastElement.offset().left+_lastElement.outerWidth()) - divLeft;
                    var blockDiv   = $("<div class='tplDebugBlock' style='z-index:1;background-color:rgba(200, 200, 200, 0.2)'>").html("<span id='"+blockId+"_title' style='z-index:3;color:#fff;background-color:#444;background-color:rgba(0, 0, 0, 0.7);padding:2px 6px;'>Block: "+blockTitle+"</span>");

                    blockDiv.attr('id', blockId+"_border");
                    blockDiv.css({
                            'position' : 'absolute',
                            'top'      : divTop,
                            'left'     : divLeft,
                            'width'    : divWidth-4,
                            'height'   : divHeight-4,
                            'border'   : '1px dashed #a33',
                            'padding'  : '2px 1px'
                    });
                    $("body").append(blockDiv);

                    $("#"+blockId+"_title").hover(function(){
                        $(this).css('z-index',4);
                        $(this).css('background-color', '#000');
                        $("#"+blockId+"_border").css({
                            'border':'2px solid #f00',
                            'padding':'1px 0',
                            'z-index': 2
                        });
                    },function(){
                        $(this).css('z-index',3);
                        try{
                            $(this).css('background-color', 'rgba(0, 0, 0, 0.7)');
                        }catch(err){
                            $(this).css('background-color', '#444'); // for IE, as rgba will fail
                        }

                        $("#"+blockId+"_border").css({
                            'border':'1px dashed #a33',
                            'padding':'2px 1px',
                            'z-index': 1
                        });
                    });

                });
                $("body")
                    .append($("<button>Toggle template debug blocks</button>")
                    .css({
                        'right'      : 0,
                        'top'        : 0,
                        'position'   : 'fixed',
                        'background' : '#a33',
                        'color'      : '#fff',
                        'border'     : '1px solid #600',
                        'padding'    : '3px 10px',
                        'cursor'     : 'pointer',
                        'width'      : '230px',
                        'z-index'    : 4
                    })
                    .click(function(){
                        $('div.tplDebugBlock').toggle();
                    })
                    .hover(function(){
                        $(this).css('background', '#533');
                    },function(){
                        $(this).css('background', '#a33');
                    })
                );
            });
        </script>
    [{/if}]
</head>
<body>

<div id="page">

    <div id="header">
        <div class="bar oxid">
            <a class="logo" href="[{ $oViewConf->getHomeLink() }]">
                <img src="[{$oViewConf->getImageUrl()}]logo.png" alt="[{$oxcmp_shop->oxshops__oxtitleprefix->value}]">
            </a>

            [{if $oView->showTopBasket()}]
                [{oxid_include_dynamic file="dyn/top_basket.tpl" type="basket"}]
            [{/if}]
            [{oxid_include_dynamic file="dyn/top_account.tpl" type="account"}]
            <dl class="box service">
                <dt id="tm.service.dd">[{ oxmultilang ident="INC_HEADER_SERVICE" }]</dt>
                <dd>
                    [{strip}]
                    <ul>
                        <li><a id="test_link_service_contact" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=contact" }]" rel="nofollow">[{ oxmultilang ident="INC_HEADER_CONTACT" }]</a></li>
                        <li><a id="test_link_service_help" href="[{ $oViewConf->getHelpPageLink() }]" rel="nofollow">[{ oxmultilang ident="INC_HEADER_HELP" }]</a></li>
                        <li><a id="test_link_service_links" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=links" }]">[{ oxmultilang ident="INC_HEADER_LINKS" }]</a></li>
                        <li><a id="test_link_service_guestbook" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=guestbook" }]" rel="nofollow">[{ oxmultilang ident="INC_HEADER_GUESTBOOK" }]</a></li>
                        [{if $oView->isActive('Invitations') }]
                        <li><a id="test_link_service_invite" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=invite" }]" rel="nofollow">[{ oxmultilang ident="INC_HEADER_INVITEFRIENDS" }]</a></li>
                        [{/if}]
                    </ul>
                    [{/strip}]
                </dd>
            </dl>

            <div class="clear"></div>
        </div>

        <div class="bar links[{if !$oView->showTopCatNavigation()}] single[{/if}]">
            <div class="fixed">
                [{if $oView->isLanguageLoaded() }]
                    [{foreach from = $oxcmp_lang item = _language}]
                        <a id="test_Lang_[{$_language->name}]" class="language[{if $_language->selected}] act[{/if}]" href="[{ $_language->link|oxaddparams:$oView->getDynUrlParams() }]" hreflang="[{ $_language->abbr }]" title="[{ $_language->name }]"><img src="[{$oViewConf->getImageUrl()}]lang/[{ $_language->abbr }].gif" alt="[{$_language->name}]"></a>
                    [{/foreach}]
                [{/if}]
                [{if $oView->loadCurrency()}]
                    [{foreach from = $oxcmp_cur item = _currency name=curr}]
                        <a id="test_Curr_[{$_currency->name}]" class="currency[{if $smarty.foreach.curr.first}] sep[{/if}][{if $_currency->selected}] act[{/if}]" href="[{ oxgetseourl ident=$_currency->link params=$oView->getDynUrlParams() }]" rel="nofollow">[{ $_currency->name }]</a>
                    [{/foreach}]
                [{/if}]
            </div>
            <div class="left">
                [{if !$oView->showTopCatNavigation() }]
                    <a id="test_HeaderHome" href="[{ $oViewConf->getHomeLink() }]">[{ oxmultilang ident="INC_HEADER_HOME" }]</a>
                [{/if}]

                [{if $oViewConf->getShowWishlist()}]
                    [{if $oView->getWishlistName()}]
                        <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=wishlist" }]" class="">[{ $oView->getWishlistName() }][{ oxmultilang ident="INC_HEADER_PRIVATWISHLIST" }]</a>
                    [{/if}]
                [{/if}]
            </div>

            <div class="right">
                [{oxifcontent ident="oxagb" object="oCont"}]
                <a id="test_HeaderTerms" href="[{ $oCont->getLink() }]" rel="nofollow">[{ $oCont->oxcontents__oxtitle->value }]</a>
                [{/oxifcontent}]
                [{oxifcontent ident="oximpressum" object="oCont"}]
                <a id="test_HeaderImpressum" href="[{ $oCont->getLink() }]">[{ $oCont->oxcontents__oxtitle->value }]</a>
                [{/oxifcontent}]
                [{if $oView->getMenueList()}]
                  [{foreach from=$oView->getMenueList() item=oMenueContent }]
                    <a href="[{ $oMenueContent->getLink() }]">[{$oMenueContent->oxcontents__oxtitle->value}]</a>
                  [{/foreach}]
                [{/if}]
            </div>
            <div class="clear"></div>
        </div>

        [{if $oView->showTopCatNavigation()}]
        <div class="bar categories">
            <a id="test_HeaderHome" href="[{ $oViewConf->getHomeLink() }]" class="fixed">[{ oxmultilang ident="INC_HEADER_HOME" }]</a>
            <ul class="menue horizontal" id="mn.categories">

            [{assign var="iCatCnt" value="1"}]
            [{foreach from=$oxcmp_categories item=ocat key=catkey name=root}]
              [{if $ocat->getIsVisible() }]

                [{if $ocat->getContentCats()}]
                    [{foreach from=$ocat->getContentCats() item=ocont key=contkey name=cont}]
                        [{if $iCatCnt <= $oView->getTopNavigationCatCnt()}]
                            <li><a id="root[{$iCatCnt}]" href="[{$ocont->getLink()}]" [{if $ocont->expanded}]class="exp"[{/if}]>[{$ocont->oxcontents__oxtitle->value}] </a></li>
                        [{/if}]
                        [{assign var="iCatCnt" value=$iCatCnt+1 }]
                    [{/foreach}]
                [{/if}]

                [{if $iCatCnt <= $oView->getTopNavigationCatCnt()}]
                <li>
                    <a id="root[{$iCatCnt}]" href="[{$ocat->getLink()}]" [{if $ocat->expanded}]class="exp"[{/if}]>[{$ocat->oxcategories__oxtitle->value}] [{if $oView->showCategoryArticlesCount() && $ocat->getNrOfArticles() > 0}] ([{$ocat->getNrOfArticles()}])[{/if}] </a>
                    [{if $ocat->getSubCats()}]
                    [{strip}]
                    <ul class="menue vertical dropdown">
                    [{foreach from=$ocat->getSubCats() item=osubcat key=subcatkey name=SubCat}]
                        [{if $osubcat->getContentCats()}]
                            [{foreach from=$osubcat->getContentCats() item=osubcont key=subcontkey name=subcont}]
                            <li><a id="test_Top_root[{ $iCatCnt }]_Cms_[{$smarty.foreach.SubCat.iteration}]_[{$smarty.foreach.subcont.iteration}]" href="[{$osubcont->getLink()}]">[{$osubcont->oxcontents__oxtitle->value}] </a></li>
                            [{/foreach}]
                        [{/if}]
                        [{if $osubcat->getIsVisible() }]
                            <li><a id="test_Top_root[{ $iCatCnt }]_SubCat_[{$smarty.foreach.SubCat.iteration}]" href="[{$osubcat->getLink()}]">[{$osubcat->oxcategories__oxtitle->value}] [{if $oView->showCategoryArticlesCount() && $osubcat->getNrOfArticles() > 0}] ([{$osubcat->getNrOfArticles()}])[{/if}] </a></li>
                        [{/if}]
                    [{/foreach}]
                    </ul>
                    [{/strip}]
                    [{/if}]
                </li>
                [{/if}]
                [{assign var="iCatCnt" value=$iCatCnt+1 }]

              [{/if}]
            [{/foreach}]

            [{if $iCatCnt > $oView->getTopNavigationCatCnt()}]
                <li>
                    [{assign var="_navcatmore" value=$oView->getCatMore()}]
                    <a id="root[{$oView->getTopNavigationCatCnt()+1}]" href="[{ oxgetseourl ident="`$_navcatmore->closelink`&amp;cl=alist" }]" class="more[{if $_navcatmore->expanded}] exp[{/if}]">[{ oxmultilang ident="INC_HEADER_URLMORE" }] </a>
                    [{strip}]
                    <ul class="menue vertical dropdown">
                    [{foreach from=$oxcmp_categories item=omorecat key=morecatkey name=more}]
                      [{if $omorecat->getIsVisible() }]
                        [{if $omorecat->getContentCats()}]
                            [{foreach from=$omorecat->getContentCats() item=omorecont key=morecontkey name=morecont}]
                            <li><a href="[{$omorecont->getLink()}]">[{$omorecont->oxcontents__oxtitle->value}] </a></li>
                            [{/foreach}]
                        [{/if}]
                        <li><a id="test_Top_RootMore_MoreCat_[{$smarty.foreach.more.iteration}]" href="[{$omorecat->getLink()}]">[{$omorecat->oxcategories__oxtitle->value}] [{if $oView->showCategoryArticlesCount() && $omorecat->getNrOfArticles() > 0}] ([{$omorecat->getNrOfArticles()}])[{/if}] </a></li>
                      [{/if}]
                    [{/foreach}]
                    </ul>
                    [{/strip}]
                </li>
            [{/if}]

            </ul>
            <div class="clear"></div>
        </div>
        [{oxscript add="oxid.catnav('mn.categories');" }]
        [{/if}]

        <div class="clear"></div>
    </div>

    <div id="content">
        <div id="left">[{ include file="_left.tpl" }]</div>
        <div id="path">[{ include file="_path.tpl" is_start=$isStart}]</div>
        <div id="right">[{include file="_right.tpl" }]</div>
        <div id="body">
        [{oxid_include_dynamic file="dyn/newbasketitem_message.tpl"}]
        [{include file="inc/error.tpl" Errorlist=$Errors.default}]
