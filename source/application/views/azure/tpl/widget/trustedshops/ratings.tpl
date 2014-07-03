[{assign var="tsRatings" value=$oViewConf->getTsRatings()}]
[{if !$tsRatings.empty}]
    <span class='hidden' xmlns:v="http://rdf.data-vocabulary.org/#" typeof="v:Review-aggregate">
    <span rel="v:rating">
            <span property="v:value">[{$tsRatings.result}] </span>
        </span> /
    <span property="v:best">[{$tsRatings.max}] </span> [{ oxmultilang ident="FROM" }] <span property="v:count">[{$tsRatings.count}]</span>
        <a href="https://www.trustedshops.de/bewertung/info_[{$tsId}].html" title="[{$tsRatings.shopName}] [{ oxmultilang ident='RATINGS' }]">[{$tsRatings.shopName}] [{ oxmultilang ident='RATINGS' }]</a>
</span>
[{/if}]