<?xml version="1.0" encoding="[{$oView->getCharSet()}]" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
        [{assign var="channel" value=$oView->getChannel()}]
        <title>[{$channel.title}]</title>
        <link>[{$channel.link}]</link>
        <description>[{$channel.description}]</description>
        <language>[{$channel.language}]</language>
        <copyright>[{$channel.copyright}]</copyright>
        <lastBuildDate>[{$channel.lastBuildDate}]</lastBuildDate>
        <generator>[{$channel.generator}]</generator>
        [{if $channel.managingEditor}]
            <managingEditor>[{$channel.managingEditor}]</managingEditor>
        [{/if}]
        [{*<!-- webMaster>[{$channel.link}]</webMaster -->
        <!-- ttl>[{$channel.link}]</ttl -->*}]
        <image>
            <url>[{$channel.image.url}]</url>
            <title>[{$channel.image.title}]</title>
            <link>[{$channel.image.link}]</link>
        </image>
        [{*<!-- pubDate>[{$channel.link}]</pubDate -->*}]

        <atom:link href="[{$channel.selflink}]" rel="self" type="application/rss+xml" />
        [{foreach from=$channel.items item='item'}]
            <item>
                <title>[{$item->title}]</title>
                <link>[{$item->link}]</link>
                <pubDate>[{$item->date}]</pubDate>
                <description>[{$item->description}]</description>
                <guid isPermaLink="[{if $item->isGuidPermalink}]true[{else}]false[{/if}]">[{$item->guid}]</guid>
                [{*<!-- category></category -->*}]
            </item>
        [{/foreach}]
</channel>
</rss>
