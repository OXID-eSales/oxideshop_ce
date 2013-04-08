<div class="socialbookmarks">
    [{assign var='_title' value=$product->oxarticles__oxtitle->value|strip_tags|cat:$product->oxarticles__oxvarselect->value|default:''|cat:$oView->getTitleSuffix()|escape:'url' }]
    [{assign var='_link'  value=$product->getLink()|escape:'url' }]

    <!-- Mister Wong -->
    [{assign var='link' value="http://www.mister-wong.com/index.php?action=addurl&amp;bm_url=`$_link`&amp;bm_description=`$_title`" }]
    <a id="sbookmarks.misterWong" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Mister Wong" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Mister Wong" src="[{$oViewConf->getImageUrl()}]bookmarks/mister_wong.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.misterWong');"}]

    <!-- Web News -->
    [{assign var='link' value="http://www.webnews.de/einstellen?url=`$_link`&amp;title=`$_title`" }]
    <a id="sbookmarks.webnews" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_WEBNEWS_TITLE" }]" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_WEBNEWS_TITLE" }]" src="[{$oViewConf->getImageUrl()}]bookmarks/webnews.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.webnews');"}]

    <!-- Icio -->
    [{assign var='link' value="http://www.icio.de/add.php?url=`$_link`&amp;title=`$_title`"}]
    <a id="sbookmarks.icio" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Icio" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Icio" src="[{$oViewConf->getImageUrl()}]bookmarks/icio.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.icio');"}]

    <!-- Folkd -->
    [{assign var='link' value="http://www.folkd.com/page/submit.html?step2_sent=1&amp;url=`$_link`&amp;check=page&amp;add_title=`$_title`&amp;add_description=&amp;add_tags_show=&amp;add_tags=&amp;add_state=public"}]
    <a id="sbookmarks.folkd" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Folkd" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Folkd" src="[{$oViewConf->getImageUrl()}]bookmarks/folkd.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.folkd');"}]

    <!-- Yigg -->
    [{assign var='link' value="http://yigg.de/neu?exturl=`$_link`" }]
    <a id="sbookmarks.yigg" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Yigg" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Yigg" src="[{$oViewConf->getImageUrl()}]bookmarks/yigg_trans.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.yigg');"}]

    <!-- Del.ico.us -->
    [{assign var='link' value="http://del.icio.us/post?url=`$_link`" }]
    <a id="sbookmarks.delicious" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Del.ico.us" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Del.ico.us" src="[{$oViewConf->getImageUrl()}]bookmarks/delicious.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.delicious');"}]

    <!-- Yahoo -->
    [{assign var='link' value="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=`$_link`&amp;t=`$_title`" }]
    <a id="sbookmarks.yahoo" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Yahoo" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Yahoo" src="[{$oViewConf->getImageUrl()}]bookmarks/yahoo.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.yahoo');"}]

    <!-- Google -->
    [{assign var='link' value="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=`$_link`&amp;title=`$_title`" }]
    <a id="sbookmarks.google" href="[{$link}]" class="sbookmarks" title="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Google" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_GENERAL_TITLE" }] Google" src="[{$oViewConf->getImageUrl()}]bookmarks/google.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.google');"}]

    <!-- Yieeha -->
    [{assign var='link' value="http://www.yieeha.de/product/create?source=light_button&amp;pname=`$_title`&amp;purl=`$_link`" }]
    <a id="sbookmarks.yieeha" href="[{$link}]" class="sbookmarks noborder" title="[{ oxmultilang ident="INC_BOOKMARKS_YIEEHA_TITLE" }]" rel="nofollow">
        <img alt="[{ oxmultilang ident="INC_BOOKMARKS_YIEEHA_TITLE" }]" src="[{$oViewConf->getImageUrl()}]bookmarks/yieeha.gif"/>
    </a>
    [{oxscript add="oxid.blank('sbookmarks.yieeha');"}]

</div>