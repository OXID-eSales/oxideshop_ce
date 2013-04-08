<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html[{if $oView->getActiveLangAbbr()}] lang="[{ $oView->getActiveLangAbbr() }]"[{/if}]>
<head>
    [{assign var="_titlesuffix" value=$_titlesuffix|default:$oView->getTitleSuffix()}]
    [{assign var="title" value=$title|default:$oView->getTitle() }]
    <title>[{$oxcmp_shop->oxshops__oxtitleprefix->value}][{if $title}] | [{$title|strip_tags}][{/if}][{if $_titlesuffix}] | [{$_titlesuffix}][{/if}][{if $titlepagesuffix}] | [{$titlepagesuffix}][{/if}]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$oView->getCharSet()}]">
    [{if $oView->noIndex() == 1 }]
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    [{elseif $oView->noIndex() == 2 }]
    <meta name="ROBOTS" content="NOINDEX, FOLLOW">
    [{/if}]
    [{if $oView->getMetaDescription()}]<meta name="description" content="[{$oView->getMetaDescription()}]">[{/if}]
    [{if $oView->getMetaKeywords()}]<meta name="keywords" content="[{$oView->getMetaKeywords()}]">[{/if}]
    [{assign var="canonical_url" value=$oView->getCanonicalUrl()}]
    [{if $canonical_url }]<link rel="canonical" href="[{ $canonical_url }]">[{/if}]
    <link rel="shortcut icon" href="[{ $oViewConf->getBaseDir() }]favicon.ico">
    <style type="text/css">

        html,body { background:#fff;height:100%;padding:0;margin:0;font:11px Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif; }
        div.box{ width:347px;height:250px;padding:20px;position:absolute;top:50%;margin-top:-140px;left:50%;margin-left:-175px; border: 0px solid #eee; background: #fff; }
        p { padding:0;margin:0;}

        .loginBoxHeader { border-bottom: 2px solid #CDCDCD; }
        .loginBoxBody { background: #EEEEEE; border-top: 1px solid #fff; border-bottom: 1px solid #fff; padding: 15px; }
        .loginBoxFooter { border-top: 2px solid #CDCDCD; padding: 10px 15px; }

        form{ padding:0;margin:0; }
        label { width:90px; float:left; padding:2px 0; margin-top:2px; clear:both; }
        input.txt { width:220px;margin-bottom:2px;font-face:Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif; }
        input.chbox { float: left; margin-left: 90px; }
        .chboxDesc { float: left; margin-left: 3px; margin-top: 2px; }
        .loginBtn { clear: both; margin: 35px 0 0 90px; }
        .loginBtnAgb { clear: both; margin: 35px 0 0 107px; }

        .loginBoxBody a { color: #009; }

        div.errorbox{ color:#f00;text-align:center;margin:0 0 5px 0; }
        a.language { margin-left: 10px; margin-top: 5px; float:right; }
        a.language img { border: none; }
        a.language.act img { opacity:.7; }
        a.link { background: url([{ $oViewConf->getResourceUrl() }]bg/oxid_.gif) no-repeat 0 -384px;padding-left:14px;font-size:11px;text-decoration:none;color:#777 !important; line-height:1.1em; }
        a.link:hover { text-decoration: underline; }

    </style>
    [{assign var='rsslinks' value=$oView->getRssLinks() }]
    [{if $rsslinks}]
      [{foreach from=$rsslinks item='rssentry'}]
        <link rel="alternate" type="application/rss+xml" title="[{$rssentry.title|strip_tags}]" href="[{$rssentry.link}]">
      [{/foreach}]
    [{/if}]
</head>
<body>


<div class="box">
    <form name="login" action="[{ $oViewConf->getSslSelfLink() }]" method="post" target="_top">
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="fnc" value="login_noredirect">
        <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
        <input type="hidden" name="tpl" value="[{$oViewConf->getActTplName()}]">
        [{if $oView->getArticleId()}]
          <input type="hidden" name="aid" value="[{$oView->getArticleId()}]">
        [{/if}]
        [{if $oView->getProduct()}]
          [{assign var="product" value=$oView->getProduct() }]
          <input type="hidden" name="anid" value="[{ $product->oxarticles__oxnid->value }]">
        [{/if}]

        <div class="loginBox">
            <div class="loginBoxHeader">
                <img src="[{$oViewConf->getImageUrl()}]logo_white.gif" alt="" class="logo">
            </div>
            [{if $oView->confirmTerms()}]
            <div class="loginBoxBody">
                <input type="hidden" name="ord_agb" value="0">
                <input id="test_OrderConfirmAGBTop" type="checkbox" class="chk" name="ord_agb" value="1">

                [{oxifcontent ident="oxagb" object="oCont"}]
                    [{oxmultilang ident="ORDER_IAGREETOTERMS1" }] <a id="test_OrderOpenAGBTop" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open('[{ $oCont->getLink()|oxaddparams:"plain=1"}]', 'agb_popup', 'resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400');return false;">[{ oxmultilang ident="ORDER_IAGREETOTERMS2" }]</a> [{ oxmultilang ident="ORDER_IAGREETOTERMS3" }],&nbsp;
                [{/oxifcontent}]
                [{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]
                    [{ oxmultilang ident="ORDER_IAGREETORIGHTOFWITHDRAWAL1" }] <a id="test_OrderOpenWithdrawalTop" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open('[{ $oCont->getLink()|oxaddparams:"plain=1"}]', 'rightofwithdrawal_popup', 'resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400');return false;">[{ $oCont->oxcontents__oxtitle->value }]</a> [{ oxmultilang ident="ORDER_IAGREETORIGHTOFWITHDRAWAL3" }]
                [{/oxifcontent}]
                <div class="loginBtnAgb">
                    <input id="test_Login" type="submit" class="btn" name="send" value="[{ oxmultilang ident="INC_CMP_LOGIN_LOGIN" }]">
                </div>
            </div>
            <div class="loginBoxFooter">&nbsp;</div>
            [{else}]
            [{include file="inc/error.tpl" Errorlist=$Errors.default}]
            <div class="loginBoxBody">
                <label for="userLoginName">[{ oxmultilang ident="INC_CMP_LOGIN_EMAIL" }]</label>
                <input id="test_LoginEmail" type="text" id="userLoginName" name="lgn_usr" value="" size="49" class="txt"><br>

                <label for="userPassword">[{ oxmultilang ident="INC_CMP_LOGIN_PWD" }]</label>
                <input id="userPassword" type="password" name="lgn_pwd" value="" size="49" class="txt"><br>

                [{if $oView->showRememberMe()}]
                <input id="test_LoginKeepLoggedIn" class="chbox" type="checkbox" name="lgn_cook" value="1"><span class="chboxDesc">[{ oxmultilang ident="INC_CMP_LOGIN_KEEPLOGGEDIN" }]</span>
                [{/if}]

                <div class="loginBtn">
                    <input id="test_Login" type="submit" class="btn" name="send" value="[{ oxmultilang ident="INC_CMP_LOGIN_LOGIN" }]"><br>
                </div>
            </div>

            <div class="loginBoxFooter">
                [{if $oView->isLanguageLoaded() }]
                    [{foreach from = $oxcmp_lang item = _language}]
                        <a id="test_Lang_[{$_language->name}]" class="language[{if $_language->selected}] act[{/if}]" href="[{ $_language->link|oxaddparams:$oView->getDynUrlParams() }]" hreflang="[{ $_language->abbr }]" title="[{ $_language->name }]"><img src="[{$oViewConf->getImageUrl()}]lang/[{ $_language->abbr }].gif" alt="[{$_language->name}]"></a>
                    [{/foreach}]
                [{/if}]
                <a id="test_LoginRegister" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=register" }]" class="link" rel="nofollow">[{ oxmultilang ident="INC_CMP_LOGIN_OPENACCOUNT" }]</a><br />
                <a id="test_LoginLostPwd" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=forgotpwd" }]" class="link" rel="nofollow">[{ oxmultilang ident="INC_CMP_LOGIN_FORGOTPWD" }]</a>

                [{oxifcontent ident="oxagb" object="oCont"}]
                  <br /><a id="test_LoginTerms" rel="nofollow" class="link" href="[{ $oCont->getLink()|oxaddparams:"plain=1" }]" onclick="window.open('[{ $oCont->getLink()|oxaddparams:"plain=1"}]', 'terms_popup', 'resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400');return false;">[{ $oCont->oxcontents__oxtitle->value }]</a>
                [{/oxifcontent}]
                [{oxifcontent ident="oximpressum" object="oCont"}]
                  <br /><a id="test_LoginAboutUs" rel="nofollow" class="link" href="[{ $oCont->getLink()|oxaddparams:"plain=1" }]" onclick="window.open('[{ $oCont->getLink()|oxaddparams:"plain=1"}]', 'aboutus_popup', 'resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400');return false;">[{ $oCont->oxcontents__oxtitle->value }]</a>
                [{/oxifcontent}]
                [{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]
                  <br /><a id="test_LoginRightOfWithdrawal" rel="nofollow" class="link" href="[{ $oCont->getLink()|oxaddparams:"plain=1" }]" onclick="window.open('[{ $oCont->getLink()|oxaddparams:"plain=1"}]', 'rightofwithdrawal_popup', 'resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400');return false;">[{ $oCont->oxcontents__oxtitle->value }]</a>
                [{/oxifcontent}]

            </div>
            [{/if}]
        </div>
    </form>
</div>

<script type="text/javascript">if (window != window.top) top.location.href = document.location.href;</script>

</body>
</html>
