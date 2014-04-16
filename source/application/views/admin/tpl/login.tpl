<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>[{ oxmultilang ident="LOGIN_TITLE" }]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <link rel="shortcut icon" href="[{$oViewConf->getImageUrl()}]favicon.ico">
    <style type="text/css">
        html,body{background:#fff;height:100%;padding:0;margin:0;font:11px Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif;}
        div.box{width:330px;height:200px;padding:20px;background:#fff url([{$oViewConf->getImageUrl()}]login.png) no-repeat;position:absolute;top:50%;margin-top:-127px;left:50%;margin-left:-190px;}
        p{padding:0;margin:0;}
        img.logo {margin:0 0 0 125px;padding:0;}
        form{padding:0;margin:0;}
        label {width:100px;float:left;padding:2px 0;margin-top:2px;clear:both;}
        input,select {width:220px;margin-bottom:2px;font-face:Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif;}
        select{width:226px;}
        input.btn{margin-left:100px;width:226px;}
        a.help {text-decoration:none;text-align:center;display:block;color:#000;margin:2px 0 0 100px;}
        a.help:hover {text-decoration:underline;}
        div.errorbox{color:#f00;text-align:center;margin:0 0 5px 0;}
        .notify {position: fixed; width: 100%; font-size: 16px; color: #fff; background-color: #f77704; padding: 8px 0 8px 0; text-align: center; border-bottom: 1px solid #d36706;}
    </style>
</head>
<body>


<div class="box">

<form action="[{ $oViewConf->getSelfLink() }]" target="_top" method="post" name="login" id="login">
    <p>
        <img src="[{$oViewConf->getImageUrl()}]loginlogo.png" alt="" class="logo">

        [{ $oViewConf->getHiddenSid() }]

        <input type="hidden" name="fnc" value="checklogin">
        <input type="hidden" name="cl" value="login"><br>

        [{if $Errors.default|@count }]
            [{include file="inc_error.tpl" Errorlist=$Errors.default}]
        [{/if}]

        <label for="usr">[{ oxmultilang ident="GENERAL_USER" }]</label>
        <input type="text" name="user" id="usr" value="[{ $user }]" size="49" autofocus><br>

        <label for="pwd">[{ oxmultilang ident="GENERAL_PASSWORD" }]</label>
        <input type="password" name="pwd" id="pwd" value="[{ $pwd }]" size="49"><br>

        <label for="lng">[{ oxmultilang ident="LOGIN_LANGUAGE" }]</label>
        <select name="chlanguage" id="lng">
            [{foreach from=$aLanguages item=oLang key=iLang}]
            <option value="[{ $oLang->id }]" [{ if $oLang->selected }]SELECTED[{/if}]>[{ $oLang->name }]</option>
            [{/foreach}]
        </select><br>

        [{if $profiles}]
        <label for="prf">[{ oxmultilang ident="LOGIN_PROFILE" }]</label>
        <select name="profile" id="prf">
            [{foreach from=$profiles item=curr_profile key=position}]
               <option value="[{$position}]" [{if $curr_profile.2}]selected[{/if}]>[{$curr_profile.0}]</option>
            [{/foreach}]
        </select><br>
        [{/if}]

        <input type="submit" value="[{ oxmultilang ident="LOGIN_START" }]" class="btn"><br>
    </p>
</form>
</div>

<script type="text/javascript">if (window != window.top) top.location.href = document.location.href;</script>

</body>
</html>