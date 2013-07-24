[{oxscript include="js/widgets/oxrating.js" priority=10 }]
[{oxscript add="$( '#itemRating' ).oxRating();"}]

<ul id="itemRating" class="rating">
    [{math equation="x*y" x=20 y=$oView->getRatingValue() assign="iRatingAverage"}]

    [{if !$oxcmp_user}]
        [{assign var="_star_title" value="MESSAGE_LOGIN_TO_RATE"|oxmultilangassign}]
    [{elseif !$oView->canRate()}]
        [{assign var="_star_title" value="MESSAGE_ALREADY_RATED"|oxmultilangassign}]
    [{else}]
        [{assign var="_star_title" value="MESSAGE_RATE_THIS_ARTICLE"|oxmultilangassign}]
    [{/if}]

    <li class="currentRate" style="width: [{$iRatingAverage}]%;">
        <a title="[{$_star_title}]"></a>
        <span title="[{$iRatingAverage}]"></span>
    </li>
    [{section name=star start=1 loop=6}]
        <li class="s[{$smarty.section.star.index}]">
            <a  class="[{if $oView->canRate()}]ox-write-review[{/if}] ox-rateindex-[{$smarty.section.star.index}]" rel="nofollow"
                [{if !$oxcmp_user}]
                    [{assign var="sAnid" value=$oView->getArticleNId()}]
                    href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=$sAnid"|cat:"&amp;sourcecl="|cat:$oViewConf->getTopActiveClassName()|cat:$oViewConf->getNavUrlParams() }]"
                [{elseif $oView->canRate()}]
                    href="#review"
                [{/if}]
                title="[{$_star_title}]">
            </a>
         </li>
    [{/section}]
    <li class="ratingValue">
        <a id="itemRatingText" class="rates" rel="nofollow" [{if $sRateUrl}]href="[{if !$oxcmp_user}][{oxgetseourl ident=$sRateUrl params=$sRateUrlParams}][{else}][{$sRateUrl}][{/if}]#review"[{/if}]>
            [{if $oView->getRatingCount()}]
                ([{$oView->getRatingCount()}])
            [{else}]
                [{oxmultilang ident="NO_RATINGS"}]
            [{/if}]
        </a>
    </li>
</ul>
[{oxscript widget=$oView->getClassName()}]


