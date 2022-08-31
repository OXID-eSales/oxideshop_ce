<ul>
    [{block name="admin_header_links"}]
    <li class="act">
        <a href="[{$oViewConf->getSelfLink() nofilter}]&cl=navigation&amp;item=home.tpl" id="homelink" target="basefrm" class="rc"><b>[{oxmultilang ident="NAVIGATION_HOME"}]</b></a>
    </li>
    <li class="sep">
        <a href="[{$shopURL}]" id="shopfrontlink" target="_blank" class="rc"><b>[{oxmultilang ident="NAVIGATION_SHOPFRONT"}]</b></a>
    </li>
    <li class="sep">
        <a href="[{$oViewConf->getSelfLink() nofilter}]&cl=navigation&amp;fnc=logout" id="logoutlink" target="_parent" class="rc"><b>[{oxmultilang ident="NAVIGATION_LOGOUT"}]</b></a>
    </li>
    [{/block}]
</ul>
