[{assign var="template_title" value="NEWS_TITLE"|oxmultilangassign}]
[{include file="_header.tpl" title=$template_title location=$template_title}]

<strong class="boxhead">[{ oxmultilang ident="NEWS_LATESTNEWSBY" }] [{ $oxcmp_shop->oxshops__oxname->value }]</strong>
<div class="box info">
    <dl class="news">
    [{foreach from=$oView->getNews() item=oNews}]
        [{if !empty($oNews) && is_object($oNews)}]
            <dt>
                <a name="[{ $oNews->oxnews__oxid->value}]">[{ $oNews->oxnews__oxdate->value|date_format:"%d.%m.%Y" }] - [{ $oNews->oxnews__oxshortdesc->value}]</a>
            </dt>
            <dd>            
                [{$oNews->getLongDesc() force=1}]
            </dd>
        [{else}]

        [{/if}]
    [{/foreach}]
    </dl>
</div>
[{include file="inc/list_locator.tpl" PageLoc="Bottom"}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
