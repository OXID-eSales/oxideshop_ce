[{oxscript include="js/widgets/oxrating.js" priority=10 }]
[{oxscript add="$( '#reviewRating' ).oxRating({openReviewForm: false, hideReviewButton: false});"}]
[{oxscript include="js/widgets/oxreview.js" priority=10 }]
[{oxscript add="$( '#writeNewReview' ).oxReview();"}]
<div id="review">
    [{block name="widget_reviews_form"}]
        [{assign var="oViewParent" value=$oView->getParent()}]
        [{assign var="oArticle" value=$oView->getArticle()}]
        [{if $oxcmp_user}]
            <form action="[{$oViewConf->getSelfActionLink()}]" method="post" id="rating">
                <div id="writeReview">
                    [{if $oViewParent->canRate()}]
                        <input id="productRating" type="hidden" name="artrating" value="0">
                        <input id="recommListRating" type="hidden" name="recommlistrating" value="0">
                        <ul id="reviewRating" class="rating">
                            <li id="reviewCurrentRating" class="currentRate">
                                <a title="[{$_star_title}]"></a>
                            </li>
                            [{section name=star start=1 loop=6}]
                                <li class="s[{$smarty.section.star.index}]">
                                  <a class="ox-write-review ox-rateindex-[{$smarty.section.star.index}]" rel="nofollow" title="[{$smarty.section.star.index}] [{if $smarty.section.star.index==1}][{oxmultilang ident="STAR"}][{else}][{oxmultilang ident="STARS"}][{/if}]"></a>
                                </li>
                            [{/section}]
                        </ul>
                    [{/if}]
                    [{$oViewConf->getHiddenSid()}]
                    [{$oViewConf->getNavFormParams()}]
                    [{oxid_include_dynamic file="form/formparams.tpl"}]
                    <input type="hidden" name="fnc" value="savereview">
                    <input type="hidden" name="cl" value="[{$oViewParent->getClassName()}]">

                    [{if $oArticle}]
                        <input type="hidden" name="anid" value="[{$oArticle->oxarticles__oxid->value}]">
                    [{else}]
                        [{assign var="_actvrecommlist" value=$oView->getActiveRecommList() }]
                        <input type="hidden" name="recommid" value="[{$_actvrecommlist->oxrecommlists__oxid->value}]">
                    [{/if}]

                    [{assign var="sReviewUserHash" value=$oView->getReviewUserHash()}]
                    [{if $sReviewUserHash}]
                        <input type="hidden" name="reviewuserhash" value="[{$sReviewUserHash}]">
                    [{/if}]

                    <textarea  rows="15" name="rvw_txt" class="areabox"></textarea><br>
                    <button id="reviewSave" type="submit" class="submitButton">[{oxmultilang ident="SAVE_RATING_AND_REVIEW"}]</button>
                </div>
            </form>
            <a id="writeNewReview" rel="nofollow"><b>[{oxmultilang ident="WRITE_REVIEW"}]</b></a>
        [{else}]
            <a id="reviewsLogin" rel="nofollow" href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account" params="anid=`$oArticle->oxarticles__oxnid->value`"|cat:"&amp;sourcecl="|cat:$oViewParent->getClassName()|cat:$oViewConf->getNavUrlParams()}]"><b>[{oxmultilang ident="MESSAGE_LOGIN_TO_WRITE_REVIEW"}]</b></a>
        [{/if}]
    [{/block}]


    [{if $oView->getReviews()}]
        [{foreach from=$oView->getReviews() item=review name=ReviewsCounter}]
            <dl>
                [{block name="widget_reviews_record"}]
                    <dt id="reviewName_[{$smarty.foreach.ReviewsCounter.iteration}]" class="clear item">
                        <span>
                            <span>[{$review->oxuser__oxfname->value}]</span> [{oxmultilang ident="WRITES" suffix="COLON" }]
                            <span>[{$review->oxreviews__oxcreate->value|date_format:"%d.%m.%Y"}]</span>
                        </span>
                        [{if $review->oxreviews__oxrating->value}]
                            [{math equation="x*y" x=20 y=$review->oxreviews__oxrating->value assign="iRatingAverage"}]
                            <ul class="rating">
                                <li class="currentRate" style="width: [{$iRatingAverage}]%;"></li>
                            </ul>
                        [{/if}]
                    </dt>
                    <dd>
                        <div id="reviewText_[{$smarty.foreach.ReviewsCounter.iteration}]" class="description">[{$review->oxreviews__oxtext->value}]</div>
                    </dd>
                [{/block}]
            </dl>
        [{/foreach}]
    [{else}]
        <dl>
            <dt id="reviewName_[{$smarty.foreach.ReviewsCounter.iteration}]">
                [{oxmultilang ident="NO_REVIEW_AVAILABLE"}]
            </dt>
            <dd></dd>
        </dl>
    [{/if}]

</div>