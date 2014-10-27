<div id="newsBox" class="box">
    <h3>[{ oxmultilang ident="NEWS" }]</h3>
    <ul class="content">
        [{foreach from=$oNews item=_oNewsItem name=_sNewsList }]
            <li >
                [{ $_oNewsItem->getLongDesc()|strip_tags|oxtruncate:100 }]<br>
                <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=news" }]#[{$_oNewsItem->oxnews__oxid->value}]" class="readMore">[{ oxmultilang ident="MORE" suffix="ELLIPSIS" }]</a>
            </li>
        [{/foreach}]
    </ul>
</div>