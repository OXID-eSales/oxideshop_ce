[{assign var="template_title" value="RECOMMLIST_TITLE"|oxmultilangassign}]
[{assign var="template_title" value=$template_title|cat:" - "|cat:$oView->getSearchForHtml()}]
[{include file="_header.tpl" title=$template_title tree_path=$oView->getTreePath()}]

[{if $oView->getActiveRecommList() }]
    [{assign var="_actvrecommlist" value=$oView->getActiveRecommList() }]

    <strong id="test_recommlistHeaderAuthor" class="head">
        [{$_actvrecommlist->oxrecommlists__oxtitle->value}] <span class="recomm_author">([{ oxmultilang ident="RECOMMLIST_LISTBY" }] [{ $_actvrecommlist->oxrecommlists__oxauthor->value }])</span>
        [{assign var='rsslinks' value=$oView->getRssLinks() }]
        [{if $rsslinks.recommlistarts}]
            <a class="rss" id="rssRecommListProducts" href="[{$rsslinks.recommlistarts.link}]" title="[{$rsslinks.recommlistarts.title}]"></a>
            [{oxscript add="oxid.blank('rssRecommListProducts');"}]
        [{/if}]
    </strong>
    <div class="box info">
      [{if $oView->isReviewActive() }]
      <div class="right">
          [{ if !$oxcmp_user}]
            [{assign var="_star_title" value="RECOMMLIST_LOGGIN"|oxmultilangassign }]
          [{ elseif !$oView->canRate() }]
            [{assign var="_star_title" value="RECOMMLIST_ALREADYRATED"|oxmultilangassign }]
          [{ else }]
            [{assign var="_star_title" value="RECOMMLIST_RATETHISLIST"|oxmultilangassign }]
          [{/if}]
          [{math equation="x*y" x=20 y=$oView->getRatingValue() assign="currentRate" }]
          <ul id="star_rate_top" class="rating">
            <li class="current_rate" style="width: [{$currentRate}]%;"><a title="[{$_star_title}]"><b>1</b></a></li>
            [{section name=star start=1 loop=6}]
            <li class="s[{$smarty.section.star.index}]"><a rel="nofollow" [{ if !$oxcmp_user}]href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="recommid="|cat:$_actvrecommlist->getId()|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]"[{ elseif $oView->canRate() }]href="#review" onclick="oxid.review.rate([{$smarty.section.star.index}])"[{/if}] title="[{$_star_title}]"><b>[{$smarty.section.star.index}]</b></a></li>
            [{/section}]
          </ul>
          [{if $oView->getRatingCount()}]
            <a id="star_rating_text" rel="nofollow" href="#review" onclick="oxid.review.show();" class="fs10 link2">[{$oView->getRatingCount()}] [{if $oView->getRatingCount() == 1}][{ oxmultilang ident="RECOMMLIST_RATINGREZULT" }][{else}][{ oxmultilang ident="RECOMMLIST_RATINGREZULTS" }] [{/if}]</a>
          [{else}]
            <a id="star_rating_text" rel="nofollow" href="#review" onclick="oxid.review.show();" class="fs10 link2">[{ oxmultilang ident="RECOMMLIST_NORATINGS" }]</a>
          [{/if}]
      </div>
      [{/if}]

      <div id="test_recommlistDesc" class="recomlistdesc">
        [{ $_actvrecommlist->oxrecommlists__oxdesc->value }]
      </div>

      <div class="clear_both"></div>

    </div>
    [{if $oView->getArticleCount() }]
      [{include file="inc/list_locator.tpl" PageLoc="Bottom" where="Bottom"}]
    [{/if}]
      [{if $oView->getArticleList() }]
        [{include file="inc/recommlist.tpl" removeFunction="removeFunction" recommid=$_actvrecommlist->getId()}]
      [{/if }]
    [{if $oView->getArticleCount() }]
      [{include file="inc/list_locator.tpl" PageLoc="Bottom" where="Bottom"}]
    [{/if}]

[{if $oView->isReviewActive() }]
    <strong class="boxhead" id="test_reviewHeader">[{ oxmultilang ident="RECOMMLIST_LISTREVIEW" }]</strong>
    <div id="review" class="box info">
      [{ if $oxcmp_user }]
        <form action="[{ $oViewConf->getSelfActionLink() }]" method="post" id="rating">
            <div id="write_review">
                [{ if $oView->canRate() }]
                <input type="hidden" name="recommlistrating" value="0">
                <ul id="star_rate" class="rating">
                    <li id="current_rate" class="current_rate" style="width: 0px;"><a title="[{$_star_title}]"><b>1</b></a></li>
                    [{section name=star start=1 loop=6}]
                    <li class="s[{$smarty.section.star.index}]"><a rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=review" params=$oViewConf->getNavUrlParams() }]" onclick="oxid.review.rate([{$smarty.section.star.index}]);return false;" title="[{$smarty.section.star.index}] [{if $smarty.section.star.index==1}][{ oxmultilang ident="RECOMMLIST_STAR" }][{else}][{ oxmultilang ident="RECOMMLIST_STARS" }][{/if}]"><b>[{$smarty.section.star.index}]</b></a></li>
                    [{/section}]
                </ul>
                [{/if}]
                [{ $oViewConf->getHiddenSid() }]
                [{ $oViewConf->getNavFormParams() }]
                [{oxid_include_dynamic file="dyn/formparams.tpl" }]
                <input type="hidden" name="fnc" value="savereview">
                <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
                <input type="hidden" name="recommid" value="[{$_actvrecommlist->oxrecommlists__oxid->value}]">
                <textarea cols="102" rows="15" name="rvw_txt" class="fullsize"></textarea><br>
                <span class="btn"><input id="test_reviewSave" type="submit" value="[{ oxmultilang ident="RECOMMLIST_SAVEREVIEW" }]" class="btn"></span>
            </div>
        </form>
        <a id="write_new_review" rel="nofollow" class="fs10" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=review" params="recommid=`$_actvrecommlist->oxrecommlists__oxid->value`&amp;"|cat:$oViewConf->getNavUrlParams() }]" onclick="oxid.review.show();return false"><b>[{ oxmultilang ident="RECOMMLIST_WRITEREVIEW" }]</b></a>
      [{else}]
        <a id="test_Reviews_login" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="recommid="|cat:$_actvrecommlist->getId()|cat:"&amp;sourcecl="|cat:$oViewConf->getActiveClassName()|cat:$oViewConf->getNavUrlParams() }]" class="fs10"><b>[{ oxmultilang ident="RECOMMLIST_LOGGINTOWRITEREVIEW" }]</b></a>
      [{/if}]

      [{if $oView->getReviews() }]
        [{foreach from=$oView->getReviews() item=review name=ReviewsCounter}]
          <dl class="review">
            <dt>
                <span id="test_ReviewName_[{$smarty.foreach.ReviewsCounter.iteration}]" class="left"><b>[{ $review->oxuser__oxfname->value }]</b> [{ oxmultilang ident="RECOMMLIST_WRITES" }]</span>
                <span id="test_ReviewTime_[{$smarty.foreach.ReviewsCounter.iteration}]" class="right param"><b>[{ oxmultilang ident="RECOMMLIST_TIME" }]</b>&nbsp;[{ $review->oxreviews__oxcreate->value|date_format:"%H:%M" }]</span>
                <span id="test_ReviewDate_[{$smarty.foreach.ReviewsCounter.iteration}]" class="right param"><b>[{ oxmultilang ident="RECOMMLIST_DATE" }]</b>&nbsp;[{ $review->oxreviews__oxcreate->value|date_format:"%d.%m.%Y" }]</span>
                <span id="test_ReviewRating_[{$smarty.foreach.ReviewsCounter.iteration}]" class="right param">[{if $review->oxreviews__oxrating->value }]<b>[{ oxmultilang ident="RECOMMLIST_RATING" }]</b>&nbsp;[{ $review->oxreviews__oxrating->value }][{/if}]</span>
            </dt>
            <dd id="test_ReviewText_[{$smarty.foreach.ReviewsCounter.iteration}]">
                [{ $review->oxreviews__oxtext->value }]
            </dd>
          </dl>
        [{/foreach}]
      [{else}]
        <div class="dot_sep mid"></div>
        [{ oxmultilang ident="RECOMMLIST_REVIEWNOTAVAILABLE" }]
      [{/if}]
    </div>
[{/if}]

[{else}]
      [{assign var="hitsfor" value="RECOMMLIST_HITSFOR"|oxmultilangassign }]
      [{assign var="title" value=$oView->getArticleCount()|cat:" "|cat:$hitsfor|cat:" &quot;"|cat:$oView->getSearchForHtml()|cat:"&quot;" }]
      [{include file="inc/recomm_lists.tpl" template_title=$title}]
[{/if}]

[{ insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl"}]
