<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>[{oxmultilang ident="LOGIN_TITLE"}]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <link rel="shortcut icon" href="[{$oViewConf->getImageUrl()}]favicon.ico">
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]login.css">
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]colors_[{$oViewConf->getEdition()|lower}].css">
</head>
<body>

[{include file="include/login_messages.tpl"}]

<div class="admin-login-box">

    <div id="shopLogo"><img src="[{$oViewConf->getImageUrl('logo_dark.svg')}]" /></div>

    <form action="[{$oViewConf->getSelfLink()}]" target="_top" method="post" name="login" id="login">

        [{block name="admin_login_form"}]
            [{$oViewConf->getHiddenSid()}]

            <input type="hidden" name="fnc" value="checklogin">
            <input type="hidden" name="cl" value="login">

            [{if !empty($Errors.default)}]
                [{include file="inc_error.tpl" Errorlist=$Errors.default}]
            [{/if}]

            <label for="usr">[{oxmultilang ident="GENERAL_USER"}]</label>
            <input type="text" name="user" id="usr" value="[{$user}]" size="49" autofocus><br>

            <label for="pwd">[{oxmultilang ident="GENERAL_PASSWORD"}]</label>
            <input type="password" name="pwd" id="pwd" value="[{$pwd}]" size="49"><br>

            <label for="lng">[{oxmultilang ident="LOGIN_LANGUAGE"}]</label>
            <select name="chlanguage" id="lng">
                [{foreach from=$aLanguages item=oLang key=iLang}]
                <option value="[{$oLang->id}]" [{if $oLang->selected}]SELECTED[{/if}]>[{$oLang->name}]</option>
                [{/foreach}]
            </select><br>

            [{if $profiles}]
            <label for="prf">[{oxmultilang ident="LOGIN_PROFILE"}]</label>
            <select name="profile" id="prf">
                [{foreach from=$profiles item=curr_profile key=position}]
                   <option value="[{$position}]" [{if $curr_profile.2}]selected[{/if}]>[{$curr_profile.0}]</option>
                [{/foreach}]
            </select><br>
            [{/if}]
        [{/block}]

        <input type="submit" value="[{oxmultilang ident="LOGIN_START"}]" class="btn"><br>
    </form>
</div>

<script type="text/javascript">if (window != window.top) top.location.href = document.location.href;</script>

</body>
</html>
