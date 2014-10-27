[{include file="widget/header/servicebox.tpl"}]
<ul id="topMenu">
<li class="login flyout[{if $oxcmp_user->oxuser__oxpassword->value}] logged[{/if}]">
   [{include file="widget/header/loginbox.tpl"}]
</li>
[{if !$oxcmp_user}]
    <li><a id="registerLink" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=register" }]" title="[{oxmultilang ident="REGISTER"}]">[{oxmultilang ident="REGISTER"}]</a></li>
[{/if}]
</ul>
[{oxscript widget=$oView->getClassName()}]